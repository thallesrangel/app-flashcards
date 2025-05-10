@extends('template-default')

@section('content')

@include('components.navbar')

<div class="container">
    <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
        <i class="bi bi-arrow-left"></i> Back to list
    </a>

    <h3 class="fw-semibold mb-4">Create New Weekly Mission</h3>

    <form action="{{ route('weekly-missions.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="title" class="form-label fw-medium">Title</label>
            <input 
                type="text" 
                class="form-control rounded-3" 
                id="title" 
                name="title"
                placeholder="Example: travel vocabulary"
            >
            @if($errors->has('title'))
                <small class="text-danger">{{ $errors->first('title') }}</small>
            @endif
        </div>
    
        <div class="mb-4">
            <label for="description" class="form-label fw-medium">Description</label>
            <textarea 
                class="form-control rounded-3" 
                id="description" 
                name="description" 
                rows="4"
                placeholder="Describe your mission here..."
            ></textarea>
            @if($errors->has('description'))
                <small class="text-danger">{{ $errors->first('description') }}</small>
            @endif
        </div>

        <div class="mb-4">
            <label for="past_due" class="form-label fw-medium">Past Due</label>
            <input 
                type="date" 
                class="form-control rounded-3" 
                id="past_due" 
                name="past_due"
            >
            @if($errors->has('past_due'))
                <small class="text-danger">{{ $errors->first('past_due') }}</small>
            @endif
        </div>
        
        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2">
            Save Flashcard
        </button>
    </form>
    
</div>


@endsection
