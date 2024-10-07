<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RelataGPT - Pergunte sobre Abu Nayem e Aftab Girach</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>RelataGPT</h1>
    <p>Pergunte sobre Abu Nayem e Aftab Girach:</p>

    <!-- Formulário de perguntas -->
    <form action="{{ route('ask') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="question" class="form-label">Sua pergunta</label>
            <input type="text" class="form-control" id="question" name="question" required placeholder="Digite sua pergunta...">
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

    @if(isset($chatResponse))
        <div class="mt-5">
            <h4>Resposta do ChatGPT:</h4>
            <p>{{ $chatResponse }}</p>
        </div>
    @endif
</div>
</body>
</html>
