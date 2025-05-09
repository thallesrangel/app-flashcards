@extends('template-default')

@section('content')

    @include('components.navbar');

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

  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title">Conversation Practice</h3>
      <p class="text-muted">Practice your English by having a conversation with an AI partner.</p>
      
      <label for="conversationMode" class="form-label small fw-bold">Conversation Mode:</label>
      
      <select id="conversationMode" class="form-select form-select-sm d-inline w-auto">
        <option value="" selected>Select a mode</option>
      
        <optgroup label="Everyday Life">
          <option value="casual">Casual</option>
          <option value="shopping">Shopping</option>
          <option value="restaurant">Restaurants</option>
          <option value="phone">Phone call</option>
        </optgroup>
      
        <optgroup label="Travel & Emergencies">
          <option value="travel">Travel / Airport / Hotel</option>
          <option value="emergency">Emergency situations</option>
          <option value="doctor">Doctor appointment</option>
        </optgroup>
      
        <optgroup label="Professional">
          <option value="job-interview">Job interview</option>
          <option value="business">Business</option>
          <option value="meeting">Meetings</option>
          <option value="presentation">Presentation</option>
        </optgroup>
      
        <optgroup label="Academic">
          <option value="school">School environment</option>
          <option value="university">College / University</option>
        </optgroup>
      </select>
      

      <div class="chat-window mb-3 position-relative">
        <div class="chat-placeholder text-center">

          <i class="bi bi-chat-right-dots fs-1 mb-3 d-block"></i>

          <p>Start a conversation to practice your English skills.</p>
          <p class="small">Try asking a question or introducing yourself.</p>
        </div>
        <div id="chat-messages" class="px-2 pt-2"></div>
      </div>
      
      <div class="mb-3 d-flex gap-2">
        <button type="button" id="conversation-starter-ideas" class="btn btn-outline-dark rounded-pill px-4 py-2">
          Conversation Starter <i class="bi bi-lightbulb me-2"></i>
        </button>
        <button class="btn btn-dark rounded-pill px-4 py-2" id="btn-gravar">
            Gravar <i class="bi bi-mic"></i>
        </button>
      </div>
      
      <div class="d-flex gap-2">
        <textarea class="form-control" id="content" placeholder="Type your message here..." rows="3"></textarea>
        <button type="button" id="send-message" class="btn btn-dark rounded-pill px-4 py-2 align-self-end">
          Send
        </button>
      </div>
    </div>
  </div>
</div>


<script>
    $('#send-message').on('click', function (e) {
      e.preventDefault();

    const message = $('#content').val().trim();
    const conversation_mode = $('#conversationMode').val();

    if (!message) {
      alert_error('Erro', "Escreva um texto para praticar.");
      return;
    }

    // Oculta o placeholder
    $('.chat-placeholder').hide();

    // Adiciona a mensagem do usuário
    $('#chat-messages').append(`
        <div class="text-end mb-2">
        <span class="d-inline-block bg-dark text-white p-2 rounded-3">${message}</span>
        </div>
    `);

    $('#content').val('');

    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `${APP_URL}/chat-ia/talk-ia`,
        method: 'POST',
        data: { content: message, conversation_mode: conversation_mode},
        success: function (response) {
        $('#chat-messages').append(`
            <div class="text-start mb-2">
            <span class="d-inline-block bg-light text-dark p-2 rounded-3">${response.content}</span>
            </div>
        `);

        // Scrolla para o final
        $('.chat-window').scrollTop($('.chat-window')[0].scrollHeight);
        }
    });
    });

  
    // Habilita o botão quando digitar algo
    // $('#content').on('input', function () {
    //   $('#send-message').prop('disabled', $(this).val().trim() === '');
    // });
  </script>



<script>
    const starters = [
      "What's your favorite travel destination?",
      "Do you enjoy cooking?",
      "Tell me about your hobbies.",
      "What's your dream job?",
      "Do you like animals?",
      "Have you ever been abroad?",
      "What’s your favorite movie?",
      "Do you prefer coffee or tea?",
      "What kind of music do you like?",
      "Do you enjoy reading?",
      "What's your favorite book?",
      "How do you spend your weekends?",
      "Do you like sports?",
      "Tell me about your family.",
      "What do you usually do after work?",
      "What's your favorite season?",
      "Have you ever tried a new language?",
      "What country would you like to visit?",
      "Do you like video games?",
      "What kind of food do you like?",
      "Do you enjoy learning English?",
      "What's your favorite English word?",
      "Do you prefer the beach or the mountains?",
      "What do you usually eat for breakfast?",
      "Do you like your job or school?",
      "Have you ever met a celebrity?",
      "What's your favorite app or website?",
      "Do you enjoy shopping?",
      "What’s your favorite holiday?",
      "What makes you happy?",
      "What is your biggest goal this year?",
      "Do you like art or museums?",
      "What's a typical day like for you?",
      "What languages can you speak?",
      "Do you believe in luck?",
      "What’s the best gift you've received?",
      "Do you prefer books or movies?",
      "What would you do with a million dollars?",
      "Do you like public speaking?",
      "Do you prefer to work alone or in a team?",
      "Have you ever done something adventurous?",
      "Do you like to dance?",
      "What’s your favorite childhood memory?",
      "How do you relax?",
      "Do you prefer mornings or nights?",
      "Have you ever had a pet?",
      "What do you do to stay healthy?",
      "Do you like learning new things?",
      "What's your favorite way to travel?",
      "What’s something new you learned recently?"
    ];
  
    $('#conversation-starter-ideas').on('click', function () {
      const randomIndex = Math.floor(Math.random() * starters.length);
      const randomStarter = starters[randomIndex];
  
      $('#content').val(randomStarter); // substitui o conteúdo do textarea com id="content"
    });
  </script>
  


@endsection
