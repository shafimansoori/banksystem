@extends('layouts.master')

@section('title', 'Manage Announcements')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-3">
            Manage Announcements
            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addAnnouncementModal">
                <i class="mdi mdi-plus"></i> Add Announcement
            </button>
        </h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($announcements as $announcement)
                                <tr class="{{ $announcement->deleted_at ? 'table-secondary' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $announcement->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $announcement->type == 'info' ? 'info' : ($announcement->type == 'warning' ? 'warning' : ($announcement->type == 'success' ? 'success' : 'danger')) }}">
                                            {{ ucfirst($announcement->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($announcement->deleted_at)
                                            <span class="badge badge-dark">Deleted</span>
                                        @elseif($announcement->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $announcement->creator->first_name }}</td>
                                    <td>{{ $announcement->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if(!$announcement->deleted_at)
                                            <button class="btn btn-sm btn-info edit-announcement" value="{{ $loop->iteration - 1 }}">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <a href="{{ route('toggle_announcement', $announcement->id) }}" class="btn btn-sm btn-{{ $announcement->is_active ? 'warning' : 'success' }}">
                                                <i class="mdi mdi-{{ $announcement->is_active ? 'eye-off' : 'eye' }}"></i>
                                            </a>
                                            <a href="{{ route('delete_announcement', $announcement->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="mdi mdi-delete"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('restore_announcement', $announcement->id) }}" class="btn btn-sm btn-success">
                                                <i class="mdi mdi-restore"></i> Restore
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="float-right">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('save_announcement') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea class="form-control" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type" required>
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="updateAnnouncementForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" id="edit_type" name="type" required>
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('custom-script')
<script>
    $(document).ready(function() {
        var announcements = {!! json_encode($announcements->items(), JSON_HEX_TAG) !!};

        $('.edit-announcement').click(function() {
            var index = $(this).val();
            var announcement = announcements[index];

            var updateRoute = '{{ route("update_announcement", ":id") }}';
            updateRoute = updateRoute.replace(':id', announcement.id);
            $('#updateAnnouncementForm').attr('action', updateRoute);

            $('#edit_title').val(announcement.title);
            $('#edit_content').val(announcement.content);
            $('#edit_type').val(announcement.type);

            $('#editAnnouncementModal').modal('show');
        });
    });
</script>
@endsection
