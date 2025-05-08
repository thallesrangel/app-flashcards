@extends('template-default')

@section('content')

    @include('components.navbar');

    <div class="container">
        <div class="row mb-5 text-center">
            <div class="col-12">
                <h2 class="fw-semibold colores">Learn English with Flashcards and AI</h2>
                <p class="text-muted fs-5">Create flashcards, practice your English, and receive feedback powered by artificial intelligence.</p>
                

                <div class="d-flex justify-content-between mb-4">
                    
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                        <i class="bi bi-clipboard-data me-2"></i> Dashboard
                    </a>

                    <a href="{{ route('chat-ia') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                        <i class="bi bi-chat-right me-2"></i> Conversation
                    </a>
                
                    <a href="{{ route('flashcard.create') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                        <i class="bi bi-plus-lg me-2"></i> Create flashcard
                    </a>
                </div>
            </div>
        </div>

        @if($data->isEmpty())
            <div class="card border-0">
                <div class="card-body text-center py-5">
                    <p class="fw-semibold fs-5 mb-2">Nenhum flashcard encontrado</p>
                    <p class="text-muted mb-4">Crie seu primeiro flashcard para começar a praticar seu inglês.</p>
                    <a href="{{ route('flashcard.create') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">Criar Flashcard</a>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($data as $item)
                    <div class="col-md-4 col-sm-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <a class="text-dark text-decoration-none" href="{{ route('flashcard.show', $item->id) }}"><h4>{{ $item->title }}</h4></a>
                                
                                <p class="text-muted"><i class="bi bi-calendar"></i> 
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                </p>
                                <p>
                                    <span class="badge bg-dark me-3">{{ $item->flashcard_items_count }} práticas</span>
                                    <span class="badge bg-dark">{{ $item->category->name }}</span>
                                </p>
                                
                                <a class="btn btn-light w-100 d-flex justify-content-between align-items-center" href="{{ route('flashcard.show', $item->id) }}">
                                    <span>Praticar</span> 
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                @include('components.pagination')
            </div>
        @endif
    </div>
@endsection
