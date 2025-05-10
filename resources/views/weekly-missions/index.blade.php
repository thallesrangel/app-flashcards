@extends('template-default')

@section('content')

@include('components.navbar')

<div class="container">
  <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
    <i class="bi bi-arrow-left"></i> Back to list
  </a>
  
  <h4>Weekly Missions</h4>
  <p class="text-muted">Complete these creative challenges to improve your English skills.</p>

  @forelse ($data as $item)
    <div class="card h-100 mb-4">
      <div class="card-body d-flex flex-column">
        <h4 class="fw-bold mb-1">{{ $item->title }}</h4>
        <p class="text-muted small mb-3">{{ $item->description ?? 'No description available.' }}</p>
      
        <div class="d-flex justify-content-between align-items-center mb-3 small">
          <span class="badge bg-dark">
            <i class="bi bi-calendar-event me-1"></i> Due: {{ \Carbon\Carbon::parse($item->past_due)->format('F d, Y') }}
          </span>
          <span class="badge bg-dark">
            <i class="bi bi-hourglass-split me-1"></i> In progress
          </span>
        </div>
      
        <a href="{{ route('weekly-missions.show', $item->id) }}" class="btn btn-dark mt-auto">
          <i class="bi bi-play-circle me-2"></i>Start Mission
        </a>
      </div>
    </div>
  @empty
    <div class="alert alert-warning">
      No missions available at the moment.
    </div>
  @endforelse  
</div>
@endsection