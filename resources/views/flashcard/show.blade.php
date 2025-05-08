@extends('template-default')

@section('content')

    @include('components.navbar');

    <div class="container">
        <input type="hidden" id="flashcard_id" name="flashcard_id" value="{{ $flashcard->id }}">

        <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
            <i class="bi bi-arrow-left"></i> Back to list
        </a>

        <h3 id="flashcard-title" class="fw-semibold mb-2">{{ $flashcard->title }}</h3>
        <p>
            <span class="badge bg-dark me-3"><span id="practice-count">0</span> práticas</span>

            <span class="badge bg-dark">{{ $flashcard->category->name }}</span>
        </p>

        <form id="form-pratice">
            <div class="card border rounded-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h4 class="fw-bold">Pratice Your English</h4>
                        
                        <p class="text-muted">Write in English below to practice with AI and improve. Try using the suggested vocabulary words.</p>

                        <div class="bg-light p-3 rounded mb-2 ">
                            <p class="fw-bold">Idea to pratice:</p>
                            <p id="idea-phrase">Tell the basics about the subject</p>
                            <button id="btn-new-idea" class="btn btn-sm btn-default"><i class="bi bi-arrow-clockwise"></i> New Idea</button>
                        </div>

                        <p class="fw-bold">Suggested Vocabulary:</p>

                        <div class="mb-4">
                            <div class="mb-2" id="word-container"></div>

                            <div class="d-inline-flex align-items-center">
                                <select id="level-selector"
                                        class="form-select form-select-sm w-auto"
                                        aria-label="Select level">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediary">Intermediary</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                              
                                <button id="btn-new-words"
                                        class="btn btn-sm btn-default ms-2">
                                  <i class="bi bi-arrow-clockwise"></i> New Words
                                </button>
                            </div>
                              
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="fw-bold mb-0">Your Text:</p>
                            <div class="d-flex align-items-center">
                              <label title="Isso muda vocabulário, tom, correções e velocidade de resposta." for="ai-personality" class="me-2 mb-0 fw-bold">Personalidade da IA:</label>
                              <select id="ai-personality" class="form-select form-select-sm" style="width: auto;">
                                <option value="professor">Professor</option>
                                <option value="prestativo">Prestativo</option>
                                <option value="divertida">Divertida</option>
                                <option value="objetiva">Objetiva</option>
                                <option value="paciente">Paciente</option>
                                <option value="motivacional">Motivacional</option>
                                <option value="rigorosa">Rigorosa</option>
                                <option value="interativa">Interativa</option>
                                <option value="explicativa">Explicativa</option>
                                <option value="resumida">Resumida</option>
                              </select>                              
                            </div>
                        </div>

                        <textarea 
                            class="form-control rounded-3" 
                            name="content"
                            id="content"
                            rows="7"
                            placeholder="Write here..."
                        ></textarea>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div id="ia-result" class="mb-4 d-none">

                                <div class="alert alert-success" role="alert">
                                    <p><i class="bi bi-check-circle"></i> Texto corrigido</p>
                                    <p id="ia-result-corrected-content"></p>
                                </div>

                                <div class="alert alert-warning" role="alert">
                                    <p><i class="bi bi-info-circle"></i> Feedback</p>
                                    <p id="ia-result-feedback"></p>
                                </div>

                                <div class="mb-3">
                                    <label for="exercise-difficulty" class="form-label">Quão difícil foi este exercício?</label>
                                    <select class="form-select" id="exercise-difficulty">
                                      <option value="">Selecione uma opção</option>
                                      <option value="very_easy">Muito Fácil</option>
                                      <option value="easy">Fácil</option>
                                      <option value="medium">Médio</option>
                                      <option value="hard">Difícil</option>
                                      <option value="very_hard">Muito difícil</option>
                                    </select>
                                </div>

                                <button id="btn-save-practice" class="btn btn-dark rounded-pill px-4 py-2 w-100">
                                    <i class="bi bi-clipboard2"></i> Salvar esta prática
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <a class="btn btn-outline-dark rounded-pill px-4 py-2" href="">
                                Reset <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    
                        <div class="text-end ms-auto">
                            <button class="btn btn-dark rounded-pill px-4 py-2" id="btn-gravar">
                                Gravar <i class="bi bi-mic"></i>
                            </button>
                    
                            <button id="btn-check-text-ia" class="btn btn-dark rounded-pill px-4 py-2">
                                Verificar texto com IA <i class="bi bi-stars"></i>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </form>
        

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-historic-tab" data-bs-toggle="pill" data-bs-target="#pills-historic" type="button" role="tab" aria-controls="pills-historic" aria-selected="true">Histórico de práticas</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-analytics-tab" data-bs-toggle="pill" data-bs-target="#pills-analytics" type="button" role="tab" aria-controls="pills-analytics" aria-selected="false">Estatísticas</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-historic" role="tabpanel" aria-labelledby="pills-historic-tab">
                
                <a href="{{ route('flashcard.pdf', $flashcard->id) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-4 py-2">
                    Gerar PDF completo das práticas
                </a>

                <div id="practice-list" class="row"></div>
            </div>
            
            <div class="tab-pane fade" id="pills-analytics" role="tabpanel" aria-labelledby="pills-analytics-tab">
                <div class="container">
                    <h4 class="text-center mb-4">Acompanhe o progresso do flashcard</h4>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-5">
                                <h5>Dificuldade ao longo da semana</h5>
                                <canvas id="difficultyChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div>
                                <h5>Frequência de prática (semana atual)</h5>
                                <div id="week-grid" class="d-flex gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
