@if ($data->lastPage() > 1)
    <nav class="mt-2">
        <ul class="pagination">
            <li class="page-item {{ ($data->currentPage() == 1) ? ' disabled' : '' }}">
            <a class="page-link" href="{{ $data->url(1) }}" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
            </a>
            </li>
            @for ($i = 1; $i <= $data->lastPage(); $i++)
            <li class="page-item {{ ($data->currentPage() == $i) ? ' active' : '' }}">
                <a class="page-link" href="{{ $data->url($i) }}">{{ $i }}</a>
            </li>
            @endfor
            <li class="page-item {{ ($data->currentPage() == $data->lastPage()) ? ' disabled' : '' }}">
            <a class="page-link" href="{{ $data->url($data->currentPage()+1) }}" aria-label="Próximo">
                <span aria-hidden="true">&raquo;</span>
            </a>
            </li>
        </ul>
    </nav>
@endif