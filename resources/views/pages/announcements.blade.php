@extends('layouts.master')

@section('title', 'Announcements')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-4">Announcements</h4>
        
        @if($announcements->count() > 0)
            @foreach($announcements as $announcement)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="card-title">
                                    @if($announcement->type == 'info')
                                        <i class="mdi mdi-information text-info"></i>
                                    @elseif($announcement->type == 'warning')
                                        <i class="mdi mdi-alert text-warning"></i>
                                    @elseif($announcement->type == 'success')
                                        <i class="mdi mdi-check-circle text-success"></i>
                                    @elseif($announcement->type == 'danger')
                                        <i class="mdi mdi-alert-circle text-danger"></i>
                                    @endif
                                    {{ $announcement->title }}
                                </h5>
                                <p class="card-text">{{ $announcement->content }}</p>
                                <small class="text-muted">
                                    <i class="mdi mdi-clock-outline"></i> 
                                    {{ $announcement->created_at->format('d M Y, H:i') }}
                                    | By {{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}
                                </small>
                            </div>
                            <span class="badge badge-{{ $announcement->type == 'info' ? 'info' : ($announcement->type == 'warning' ? 'warning' : ($announcement->type == 'success' ? 'success' : 'danger')) }}">
                                {{ ucfirst($announcement->type) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-3">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="mdi mdi-information"></i> No announcements at this time.
            </div>
        @endif
    </div>
</div>

@endsection
