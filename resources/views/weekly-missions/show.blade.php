@extends('template-default')

@section('content')

    @include('components.navbar')

    <div class="container">
        <a href="{{ route('flashcard') }}" class="btn btn-default mb-4">
            <i class="bi bi-arrow-left"></i> Back to list
        </a>
        
        <div class="card">
            <div class="card-body">
                <h4 class="fw-bold" id="title">{{ $mission->title }}</h4>
                <p class="text-muted" id="description">{{ $mission->description }}</p>

                <div class="mb-3">
                    <p class="fw-bold">Your Response</p>
                    <textarea class="form-control" id="content" placeholder="Type here..." rows="10"></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center w-100 mb-3">
                    <button type="button" id="btn-check-ia" class="btn btn-dark rounded-pill px-4 py-2">
                        Check <i class="bi bi-stars"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4 d-none" id="feedback-card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">AI Feedback</h5>
        
                <div class="mb-3">
                    <label class="fw-bold">Corrected Text</label>
                    <div class="border rounded p-3 bg-light" id="corrected-composition"></div>
                </div>
        
                <div class="mb-3">
                    <label class="fw-bold">Feedback (PT-BR)</label>
                    <div class="border rounded p-3 bg-light" id="feedback-composition"></div>
                </div>
        
                <div class="row">
                    <div class="col-md-6">
                        <label class="fw-bold">Cohesion</label>
                        <div class="border rounded p-2 bg-light" id="cohesion-comment"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Coherence</label>
                        <div class="border rounded p-2 bg-light" id="coherence-comment"></div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="fw-bold">Grammar</label>
                        <div class="border rounded p-2 bg-light" id="grammar-comment"></div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="fw-bold">Vocabulary</label>
                        <div class="border rounded p-2 bg-light" id="vocabulary-comment"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $('#btn-check-ia').on('click', function(e) {
        e.preventDefault();
        
        if (!$('#content').val()) {
            alert_error('Erro', "Write something to start.");
            return;
        }

        var contractData = {
            title: $('#title').text(),
            description: $('#description').text(),
            content: $('#content').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $('#loadingModal').modal('show');

        $.ajax({
            url: `${APP_URL}/weekly-mission/check-text`,
            method: 'POST',
            data: contractData,
            success: function(response) {
                $('#loadingModal').modal('hide');

                $('#corrected-composition').text(response.corrigido);
                $('#feedback-composition').text(response.feedback);
                $('#cohesion-comment').text(response.cohesion);
                $('#coherence-comment').text(response.coherence);
                $('#grammar-comment').text(response.grammar);
                $('#vocabulary-comment').text(response.vocabulary);

                $('#feedback-card').removeClass('d-none');
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
</script>
@endsection