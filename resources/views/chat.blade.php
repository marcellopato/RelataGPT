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
    <p>Ask about Abu Nayem and Aftab Girach:</p>

    <!-- Question form -->
    <form action="{{ route('ask') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="question" class="form-label">Your question</label>
            <input type="text" class="form-control" id="question" name="question" required placeholder="Type your question...">
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>

    @if(isset($chatResponse))
        <div class="mt-5">
            <h4>ChatGPT's response:</h4>
            <p>{{ $chatResponse }}</p>
        </div>
    @endif
</div>
</body>
</html>
