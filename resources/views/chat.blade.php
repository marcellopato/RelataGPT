<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RelataGPT - Ask about Abu Nayem and Aftab Girach</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Ask ChatGPT</h2>
    <form id="chat-form">
        @csrf
        <div class="form-group">
            <label for="question">Your Question</label>
            <input type="text" class="form-control" id="question" name="question" placeholder="Ask a question..." required>
        </div>
        <button type="submit" id="send-button" class="btn btn-primary mt-3">
            Send
            <span id="loading-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
        </button>
    </form>

    <!-- Local para exibir a resposta -->
    <div id="chat-response" class="alert alert-info mt-3" style="display:none;"></div>
</div>

<!-- jQuery para lidar com a requisição -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready($(document).ready(function() {
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();

            // Mostrar o spinner de carregamento
            $('#loading-spinner').show();
            $('#send-button').prop('disabled', true);
            $('#chat-response').hide();

            // Enviar a requisição AJAX
            $.ajax({
                url: '{{ route("ask") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'processing') {
                        // Iniciar polling para verificar se a resposta foi processada
                        pollForResponse(response.chatResponseId);
                    }
                },
                error: function() {
                    $('#loading-spinner').hide();
                    $('#send-button').prop('disabled', false);
                    $('#chat-response').text('An error occurred. Please try again.').show();
                }
            });
        });

        // Função para fazer polling até que a resposta esteja disponível
        function pollForResponse(chatResponseId) {
            var interval = setInterval(function() {
                $.ajax({
                    url: '/chat-response/' + chatResponseId, // Criar essa rota para buscar a resposta
                    method: 'GET',
                    success: function(response) {
                        if (response.is_processed) {
                            // Parar o polling quando a resposta estiver pronta
                            clearInterval(interval);

                            // Esconder o spinner
                            $('#loading-spinner').hide();
                            $('#send-button').prop('disabled', false);

                            // Exibir a resposta
                            $('#chat-response').text(response.response).show();
                        }
                    },
                    error: function() {
                        clearInterval(interval);
                        $('#loading-spinner').hide();
                        $('#send-button').prop('disabled', false);
                        $('#chat-response').text('Error fetching response.').show();
                    }
                });
            }, 3000); // Poll a cada 3 segundos
        }
    }));

</script>

</body>
</html>
