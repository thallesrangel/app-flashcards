@foreach ($categories as $index => $category)
    <input type="radio" 
           class="btn-check" 
           name="category_id" 
           id="category{{ $category->id }}" 
           value="{{ $category->id }}"
           autocomplete="off"
           {{ $loop->first ? 'checked' : '' }}>
    <label class="btn btn-sm btn-outline-primary rounded-pill" for="category{{ $category->id }}">
        {{ $category->name }}
    </label>
@endforeach
