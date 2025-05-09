@extends('template-default')

@section('content')

    @include('components.navbar')

  <style>
   .chat-window {
    height: 400px;
    overflow-y: auto;
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    position: relative;
  }
    .chat-placeholder {
      padding: 4rem 1rem;
      color: #6c757d;
    }
    textarea.form-control {
      resize: none;
    }
  </style>

<div class="container">

    <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
        <i class="bi bi-arrow-left"></i> Back to list
    </a>
    
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('chat-ia') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                Composition
            </a>

            <a href="{{ route('composition') }}" class="btn btn-outline-dark px-4 py-2 rounded-pill">
                historic
            </a>
        </div>
    </div>

    
  <div class="card">
    <div class="card-body">
        <h4 class="fw-bold">Composition</h4>
        <p class="text-muted">Write your composition in English based on the proposed topic.</p>
      
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="mb-0 fw-bold">Theme</p>
            
            <button type="button" id="btn-new-theme"class="btn btn-outline-dark rounded-pill px-4 py-2">
                New Theme <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="bg-light p-3 rounded mb-2">
            <div id="idea-theme">Clique em "New Theme" para começar</div>
        </div>
    
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="compositionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="write-tab" data-bs-toggle="tab" data-bs-target="#write" type="button" role="tab" aria-controls="write" aria-selected="true">
                    Write
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab" aria-controls="feedback" aria-selected="false">
                    Feedback
                </button>
            </li>
        </ul>
        
        <!-- Tab content -->
        <div class="tab-content" id="compositionTabsContent">
            <div class="tab-pane fade show active" id="write" role="tabpanel" aria-labelledby="write-tab">
                <div class="mb-3">
                    <textarea class="form-control" id="content" placeholder="Type here..." rows="15"></textarea>
                </div>
            </div>
            <div class="tab-pane fade" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
                <div class="row g-3 mb-3">
                  <!-- Texto original -->
                  <div class="col-md-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <p class="fw-bold">Your Original Text</p>
                        <p id="original-composition" class="mb-0 text-muted">No content yet.</p>
                      </div>
                    </div>
                  </div>
              
                  <!-- Texto corrigido -->
                  <div class="col-md-6">
                    <div class="card h-100">
                      <div class="card-body">
                        <p class="fw-bold">Corrected Text</p>
                        <p id="corrected-composition" class="mb-0 text-muted">No corrections yet.</p>
                      </div>
                    </div>
                  </div>
              
                  <!-- Feedback geral -->
                  <div class="col-12">
                    <div class="card">
                      <div class="card-body">
                        <p class="fw-bold">Feedback</p>
                        <p id="feedback-composition" class="mb-0 text-muted">No feedback yet.</p>
                      </div>
                    </div>
                  </div>

                  <div id="feedback-evaluation" class="mt-4"></div>

                </div>
              </div>
        </div>

        <div class="d-flex justify-content-between align-items-center w-100 mb-3">
            <a  href="{{ route('composition')}}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                New Composition
            </a>

            <button type="button" id="btn-check-composition-ia" class="btn btn-dark rounded-pill px-4 py-2">
                Correct Composition <i class="bi bi-stars"></i>
            </button>
        </div>
    </div>
  </div>
</div>


<script>
    $('#btn-check-composition-ia').on('click', function(e) {
        e.preventDefault();
        
        if (!$('#content').val()) {
            alert_error('Erro', "Write something to start.");
            return;
        }

        var contractData = {
            content: $('#content').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $('#loadingModal').modal('show');

        $.ajax({
            url: `${APP_URL}/composition/check-text`,
            method: 'POST',
            data: contractData,
            success: function(response) {
                $('#loadingModal').modal('hide');

                $('#corrected-composition').text(response.corrigido);
                $('#feedback-composition').text(response.feedback);
                $('#original-composition').text($('#content').val());

                $('#feedback-evaluation').html(`
                    <h5 class="fw-semibold">Writing Evaluation</h5>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Cohesion:</strong> ${response.cohesion}</li>
                        <li class="list-group-item"><strong>Coherence:</strong> ${response.coherence}</li>
                        <li class="list-group-item"><strong>Grammar:</strong> ${response.grammar}</li>
                        <li class="list-group-item"><strong>Vocabulary:</strong> ${response.vocabulary}</li>
                    </ul>
                `);

                $('#feedback-tab').tab('show');
            },
            error: function(xhr, status, error) {
                $('#loadingModal').modal('hide');
            
                var content = "";
                
                $.each(data.responseJSON.errors, function(key,value) {
                    content += '<p>'+ value+'</p>';
                });
                
                alert_error('Erro', content);
            }
        });
    });

    $('#btn-new-theme').on('click', function(e) {
        e.preventDefault();
    
        $('#loadingModal').modal('show');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: `${APP_URL}/composition/new-idea`,
            method: 'POST',
            data: {},
            success: function(response) {
                $('#idea-theme').html(`
                    <h5 class="">${response.topic}</h5>
                    
                    <p class="mb-2 fw-bold">Description</p>
                    <p>${response.explanation}</p>

                    <p class="mb-2 fw-bold">Suggested Vocabulary</p>
                    <p>${response.suggested_vocabulary}</p>
                `);

                $('#loadingModal').modal('hide');
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                alert_error('Erro', 'Não foi possível gerar uma nova ideia.');
            }
        });
    });

</script>
@endsection