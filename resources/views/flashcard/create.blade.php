@extends('template-default')

@section('content')

@include('components.navbar');

<div class="container">
    <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <h3 class="fw-semibold mb-4">Criar Novo Flashcard</h3>

    <form action="{{ route('flashcard.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="title" class="form-label fw-medium">Título do Flashcard</label>
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
            <p>Tema do seu flashcard</p>
            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                Gerenciar as categorias
            </button>

            <div id="category-radio-list" class="d-flex flex-wrap gap-2 mt-4">
                @foreach ($categories as $index => $category)
                    <input type="radio" 
                           class="btn-check" 
                           name="category_id" 
                           id="category{{ $category->id }}" 
                           value="{{ $category->id }}"
                           autocomplete="off"
                           {{ $loop->first ? 'checked' : '' }}>
                    <label class="btn btn-sm btn-outline-primary" for="category{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
        </div>
        
        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2">
            Salvar Flashcard
        </button>
    </form>
</div>


<div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-labelledby="manageCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Gerenciar Categorias</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <form id="create-category-form">
              @csrf
              <div class="input-group mb-3">
                  <input type="text" name="name" id="newCategoryName" class="form-control" placeholder="Nova categoria" required>
                  <button type="submit" class="btn btn-dark">Adicionar</button>
              </div>
          </form>
          <ul id="category-list" class="list-group">
              @foreach ($categories as $category)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                      {{ $category->name }}
                      <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}"><i class="bi bi-trash3"></i></button>
                  </li>
              @endforeach
          </ul>
        </div>
      </div>
    </div>
</div>

@endsection
