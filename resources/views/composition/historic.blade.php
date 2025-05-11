@extends('template-default')

@section('content')

@include('components.navbar')

<div class="container">
  
    <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
        <i class="bi bi-arrow-left"></i> Back to list
    </a>
    
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('composition') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                Composition
            </a>

            <a href="{{ route('composition.historic') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                historic
            </a>
        </div>
    </div>
  
  <h4>Historic Composition</h4>

    @forelse ($data as $index => $item)
        <div class="card h-100 mb-4">
            <div class="card-body d-flex flex-column">

            <h5 class="mb-1">Corrected</h5>
            <p class="text-muted small mb-3">{{ $item->corrected_content ?? 'No corrected content available.' }}</p>

            <a class="btn btn-outline-dark mb-2" data-bs-toggle="collapse" href="#collapseDetails{{ $index }}" role="button" aria-expanded="false" aria-controls="collapseDetails{{ $index }}">
                <i class="bi bi-arrows-angle-expand"></i>  Show details
            </a>

            <div class="collapse" id="collapseDetails{{ $index }}">
                <h5 class="mb-1">Original</h5>
                <p class="text-muted small mb-3">{{ $item->original_content ?? 'No original content available.' }}</p>

                <h5 class="mb-1">Feedback</h5>
                <p class="text-muted small mb-3">{{ $item->feedback ?? 'No feedback available.' }}</p>
            </div>

            </div>
        </div>
    @empty
        <div class="alert alert-warning">
            No missions available at the moment.
        </div>
    @endforelse

</div>
@endsection