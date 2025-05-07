import './bootstrap';

$(document).ready(function() {

    $('#btn-new-idea').on('click', function(e) {
        e.preventDefault();
 
        const flashcard_title = $('#flashcard-title').text();
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: `${APP_URL}/flashcard/new-idea`,
            method: 'POST',
            data: {
                flashcard_title: flashcard_title,
          
            },
            success: function(response) {
                $('#idea-phrase').text(response.content);
            },
            error: function(xhr) {
                alert_error('Erro', 'Não foi possível gerar uma nova frase.');
            }
        });
    });



    $('#btn-check-text-ia').on('click', function(e) {
        e.preventDefault();
        
        if (!$('#content').val()) {
            alert_error('Erro', "Escreva um texto para praticar.");
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
            url: `${APP_URL}/flashcard-item/check-text`,
            method: 'POST',
            data: contractData,
            success: function(response) {
                $('#loadingModal').modal('hide');
                $('#ia-result').removeClass('d-none');
            
                $('#ia-result-corrected-content').text(response.corrigido);
                $('#ia-result-feedback').text(response.feedback);
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

    $('#btn-save-practice').on('click', function(e) {
        e.preventDefault();
    
        const original = $('#content').val();
        const corrigido = $('#ia-result-corrected-content').text();
        const feedback = $('#ia-result-feedback').text();
        const level = $('#exercise-difficulty').val();
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: `${APP_URL}/flashcard-item/store-practice`,
            method: 'POST',
            data: {
                original: original,
                corrigido: corrigido,
                feedback: feedback,
                flashcard_id: $('#flashcard_id').val(),
                level: level
            },
            success: function(response) {
                alert_success('Salvo com sucesso!', 'Sua prática foi salva.');
                renderPracticeItem(response.item);

                const currentCount = parseInt($('#practice-count').text(), 10);
                $('#practice-count').text(currentCount + 1);

                $('#ia-result').addClass('d-none');
                $('#content').val("");
                $('#ia-result-corrected-content').text("");
                $('#ia-result-feedback').text("");
            },
            error: function(xhr) {
                alert_error('Erro', 'Não foi possível salvar a prática.');
            }
        });
    });


    function traduzirNivel(level) {
        const niveis = {
            very_easy: 'Muito fácil',
            easy: 'Fácil',
            medium: 'Médio',
            hard: 'Difícil',
            very_hard: 'Muito difícil'
        };
        return niveis[level] || level;
    }

    function renderPracticeItem(item) {

        const html = `
            <div class="card mt-4">
                <div class="card-body">
                    <p class="small text-muted d-flex justify-content-between">
                        <span>${item.created_at_formatted ?? ''}</span>
                        <span class="badge bg-dark">${traduzirNivel(item.level)}</span>
                    </p>        

                    <h6 class="card-subtitle mb-2">Texto original:</h6>
                    <p class="card-text text-muted">${item.content}</p>
                    
                    <h6 class="card-subtitle mb-2">Texto corrigido:</h6>
                    <p class="card-text text-success">${item.corrected_content}</p>
                    
                    <h6 class="card-subtitle mb-2">Feedback:</h6>
                    <p class="card-text text-warning">${item.feedback}</p>

                    <a href="${APP_URL}/flashcard-item/practice/${item.id}/pdf" target="_blank" class="btn btn-sm btn-dark rounded-pill px-4 py-2">
                        Baixar em PDF <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        `;
        $('#practice-list').prepend(html);
    }

    function loadPractices(flashcardId) {
        $.ajax({
            url: `${APP_URL}/flashcard-item/list/${flashcardId}`,
            method: 'GET',
            success: function(response) {
                $('#practice-list').empty();

                const practiceCount = response.items ? response.items.length : 0;
                $('#practice-count').text(practiceCount);


                if (practiceCount > 0) {
                    response.items.forEach(item => renderPracticeItem(item));
                } else {
                    $('#practice-list').html(`
                        <div class="text-center py-4">
                            <p class="fw-semibold fs-5 mb-2">Nenhuma prática encontrada</p>
                            <p class="text-muted mb-0">Digite um frase em inglês e clique em verificar para começar.</p>
                        </div>
                    `);
                }
                
            },
            error: function() {
                alert('Erro ao carregar práticas.');
            }
        });
    }
    
    const flashcardId = $('#flashcard_id').val();

    loadPractices(flashcardId);
});







$(document).ready(function () {
    $.get('/flashcard/stats', function (data) {
        renderFrequency(data.frequency);
        renderDifficulty(data.levelsPerDay);
    });

    function renderFrequency(frequency) {
        const container = $('#week-grid');
        container.empty();

        for (const [date, count] of Object.entries(frequency)) {
            const dayName = new Date(date).toLocaleDateString('pt-BR', { weekday: 'short' });
            const square = $(`
                <div class="p-3 text-center rounded ${count > 0 ? 'bg-success text-white' : 'bg-light'}">
                    <strong>${dayName}</strong><br>${count}x
                </div>
            `);
            container.append(square);
        }
    }

    function renderDifficulty(levelsPerDay) {
        const labels = [];
        const levelCounts = {
            very_easy: [],
            easy: [],
            medium: [],
            hard: [],
            very_hard: []
        };

        for (const [date, levels] of Object.entries(levelsPerDay)) {
            labels.push(new Date(date).toLocaleDateString('pt-BR', { weekday: 'short' }));

            const countPerLevel = {
                very_easy: 0,
                easy: 0,
                medium: 0,
                hard: 0,
                very_hard: 0
            };

            levels.forEach(lvl => countPerLevel[lvl]++);
            for (const key in levelCounts) {
                levelCounts[key].push(countPerLevel[key]);
            }
        }

        const ctx = document.getElementById('difficultyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Muito fácil', data: levelCounts.very_easy, backgroundColor: '#28a745' },
                    { label: 'Fácil', data: levelCounts.easy, backgroundColor: '#20c997' },
                    { label: 'Médio', data: levelCounts.medium, backgroundColor: '#ffc107' },
                    { label: 'Difícil', data: levelCounts.hard, backgroundColor: '#fd7e14' },
                    { label: 'Muito difícil', data: levelCounts.very_hard, backgroundColor: '#dc3545' }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});




















$(function () {
    $('#create-category-form').on('submit', function (e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.post('/categories', $(this).serialize(), function () {
            $('#newCategoryName').val('');
            reloadCategories();
        });
    });

    // Excluir categoria
    $('#category-list').on('click', '.delete-category', function () {
        if (!confirm('Tem certeza que deseja excluir essa categoria?')) return;

        const id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: `/categories/${id}`,
            type: 'DELETE',
            data: {},
            success: function () {
                reloadCategories();
            }
        });
    });

    function reloadCategories() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.get('/categories/list', function (html) {
            $('#category-radio-list').html(html);
        });
    
        $.get('/categories', function (categories) {
            const list = categories.map(cat => `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${cat.name}
                    <button class="btn btn-sm btn-danger delete-category" data-id="${cat.id}">🗑️</button>
                </li>
            `);
    
            $('#category-list').html(list.join(''));
        });
    }    
});





/// Recog

var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
var recognition = new SpeechRecognition();
recognition.lang = 'en-US';
recognition.interimResults = true;
recognition.continuous = true;

$(document).ready(function () {
    var isListening = false;
    var finalTranscript = ''; 
    var interimTranscript = '';

    function startRecognition() {
        recognition.start();
        isListening = true;
        $('#btn-gravar').text('Pause').append(' <i class="bi bi-mic-mute"></i>');
    }

    function stopRecognition() {
        recognition.stop();
        isListening = false;
        $('#btn-gravar').text('Start Recording');
    }

    recognition.onresult = function (event) {
        interimTranscript = ''; // limpa o que estava antes

        for (var i = event.resultIndex; i < event.results.length; ++i) {
            var transcript = event.results[i][0].transcript;

            if (event.results[i].isFinal) {
                finalTranscript += transcript + ' ';
            } else {
                interimTranscript += transcript;
            }
        }

        // Atualiza o campo com o texto final + temporário
        $('#content').val(finalTranscript + interimTranscript);
    };

    $('#btn-gravar').click(function (e) {
        e.preventDefault();
        if (!isListening) {
            startRecognition();
        } else {
            stopRecognition();
        }
    });

    
    $('#content').on('input', function () {
        const currentText = $(this).val();
        const fullTranscript = finalTranscript + interimTranscript;
    
        if (currentText === '') {
            // Usuário apagou tudo
            finalTranscript = '';
            interimTranscript = '';
        } else if (currentText === fullTranscript) {
            // Nada mudou de fato, mantém como está
            return;
        } else if (fullTranscript.startsWith(currentText)) {
            // Usuário apagou parte final (normalmente com backspace)
            const removedLength = fullTranscript.length - currentText.length;
            if (interimTranscript.length >= removedLength) {
                interimTranscript = interimTranscript.slice(0, -removedLength);
            } else {
                const diff = removedLength - interimTranscript.length;
                interimTranscript = '';
                finalTranscript = finalTranscript.slice(0, -diff);
            }
        } else {
            // Usuário editou manualmente, aceita como novo ponto de partida
            finalTranscript = currentText;
            interimTranscript = '';
        }
    });    

    recognition.onend = function () {
        if (isListening) recognition.start();
    };
});
