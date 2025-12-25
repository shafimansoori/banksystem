<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.announcements', compact('announcements'));
    }

    public function manage()
    {
        $announcements = Announcement::withTrashed()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.manage_announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
        ]);

        DB::beginTransaction();

        try {
            Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('manage_announcements')->with('success', 'Duyuru başarıyla oluşturuldu');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return redirect()->route('manage_announcements')->with('error', 'Duyuru oluşturulamadı');
        }
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
        ]);

        DB::beginTransaction();

        try {
            $announcement = Announcement::findOrFail($id);

            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->route('manage_announcements')->with('success', 'Duyuru güncellendi');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return redirect()->route('manage_announcements')->with('error', 'Duyuru güncellenemedi');
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->update(['is_active' => !$announcement->is_active]);

            return redirect()->route('manage_announcements')->with('success', 'Duyuru durumu güncellendi');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return redirect()->route('manage_announcements')->with('error', 'İşlem başarısız');
        }
    }

    public function destroy(int $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();

            return redirect()->route('manage_announcements')->with('success', 'Duyuru silindi');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return redirect()->route('manage_announcements')->with('error', 'Duyuru silinemedi');
        }
    }

    public function restore(int $id)
    {
        try {
            $announcement = Announcement::withTrashed()->findOrFail($id);
            $announcement->restore();

            return redirect()->route('manage_announcements')->with('success', 'Duyuru geri yüklendi');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return redirect()->route('manage_announcements')->with('error', 'Duyuru geri yüklenemedi');
        }
    }
}
