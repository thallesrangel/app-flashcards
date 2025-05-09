@extends('template-default')

@section('content')

  @include('components.navbar')

  <div class="container">
    <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
        <i class="bi bi-arrow-left"></i> Back to list
    </a>

    <h3 class="fw-semibold mb-2 text-center">Dashboard de Aprendizado</h3>
    <p class="text-muted text-center">Acompanhe seu progresso e gerencie seu aprendizado de inglês</p>

    <div class="row">
      <div class="col-12 col-sm-6 col-md-4 mt-2">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Total de Flashcards</h5>
                <i class="bi bi-card-text fs-4 text-secondary"></i>
              </div>
              <h2>{{ $flashcardsCount }}</h2>
              <p class="card-text">Flashcards criados para estudo</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 mt-2">

            <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="card-title mb-0">Práticas Realizadas</h5>
                  <i class="bi bi-clipboard-check fs-4 text-secondary"></i>
                </div>
                <h2>{{ $flashcardItemsCount }}</h2>
                <p class="card-text">Total de sessões de prática</p>
              </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4 mt-2">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Sequência de Dias</h5>
                <i class="bi bi-calendar-check fs-4 text-secondary"></i>
              </div>
              <h2>{{ $practiceStreak }}</h2>
              <p class="card-text">Dias consecutivos de prática</p>
        
              <div class="d-flex gap-1 mt-3">
                @for ($i = 1; $i <= 7; $i++)
                  <div style="width: 25px; height: 10px;" class="rounded {{ $i <= $practiceStreak ? 'bg-warning' : 'bg-light border' }}"></div>
                @endfor
              </div>
            </div>
          </div>
        </div>
        
    </div>

    <div class="row mt-5">
      <h4>Insights de Progresso</h4>
      <p class="text-muted">Análise detalhada do seu progresso no aprendizado</p>
  
      <div class="col-12 mt-2">
          <h5 class="mb-2">Atividade diária (últimos 30 dias)</h5>
  
          <div class="d-flex flex-wrap align-items-end" style="height: 150px; border-radius: 10px; padding: 10px 0;">
              @foreach ($cardsPerDay as $day => $count)
                  @php
                      $height = min(10 + ($count * 5), 100);
                      $color = $count > 0 ? '#343a40' : '#dee2e6'; // dark vs cinza claro
                  @endphp
                  <div 
                      class="rounded mx-auto"
                      style="
                          flex: 1 0 3.33%;
                          height: {{ $height }}px;
                          background: {{ $color }};
                          margin: 0 10px;
                          transition: all 0.3s ease-in-out;
                          border-radius: 4px;
                          cursor: pointer;
                      "
                      title="{{ \Carbon\Carbon::parse($day)->format('d/m') }}: {{ $count }} cards"
                  ></div>
              @endforeach
          </div>
      </div>

      <div class="col-12 mt-5">
        <h5 class="mb-2">Progresso por semana</h5>
      
        @forelse ($weeklyData as $week)
            @php
                $percent = round(($week['total'] / $maxWeekly) * 100);
            @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ $week['week_start'] }}</span>
                    <span class="text-muted">{{ $week['total'] }} cards</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div 
                        class="progress-bar bg-dark" 
                        role="progressbar" 
                        style="width: {{ $percent }}%;" 
                        aria-valuenow="{{ $percent }}" 
                        aria-valuemin="0" 
                        aria-valuemax="100">
                        {{ $percent }}%
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhuma prática registrada nas últimas semanas.</p>
        @endforelse
      </div>
    </div>
    
    <div class="row">
      <div class="col-12 col-sm-6 col-md-6 col-lg-6 mt-5">

          <h5 class="mb-2">Categorias mais praticadas</h5>

          @forelse ($categoryStats as $cat)
              <div class="mb-2">
                  <div class="d-flex justify-content-between">
                      <span class="text-muted">{{ $cat['name'] }}</span>
                      <span class="text-muted">{{ $cat['percent'] }}% ({{ $cat['total'] }} cards)</span>
                  </div>
                  <div class="progress" style="height: 20px;">
                      <div 
                          class="progress-bar bg-dark" 
                          role="progressbar" 
                          style="width: {{ $cat['percent'] }}%;" 
                          aria-valuenow="{{ $cat['percent'] }}" 
                          aria-valuemin="0" 
                          aria-valuemax="100">
                          {{ $cat['percent'] }}%
                      </div>
                  </div>
              </div>
          @empty
            <p class="text-muted">Nenhuma prática registrada por categoria.</p>
          @endforelse
      </div>

      <div class="col-12 col-sm-6 col-md-6 col-lg-6 mt-5">


        <h5 class="mb-2">Distribuição de tempo por categoria</h5>
    
        @forelse ($timeCategoryStats as $stat)
            <div class="mb-2">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">{{ $stat['name'] }}</span>
                    <span class="text-muted">{{ $stat['total'] }} práticas</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div 
                        class="progress-bar bg-dark" 
                        role="progressbar" 
                        style="width: {{ $stat['percent'] }}%;" 
                        aria-valuenow="{{ $stat['percent'] }}" 
                        aria-valuemin="0" 
                        aria-valuemax="100">
                        {{ $stat['percent'] }}%
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhuma prática registrada.</p>
        @endforelse
      </div>
    </div>

    </div>
  </div>
@endsection
