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
    <h2>Import Emails</h2>

    <!-- Step 1: File Upload Form -->
    <form id="upload-form" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="json_file">Upload JSON File</label>
            <input type="file" class="form-control" id="json_file" name="json_file" accept=".json" required>
        </div>
        <button type="submit" id="upload-button" class="btn btn-primary mt-3">
            Import
            <span id="loading-spinner-upload" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
        </button>
    </form>


    <!-- Progress bar -->
    <div class="progress mt-3" style="display:none;" id="progress-bar-container">
        <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>

    <!-- Feedback message after upload -->
    <div id="upload-feedback" class="alert mt-3" style="display:none;"></div>

    <!-- Step 2: Question Form (initially hidden) -->
    <div id="question-section" style="display:none;">
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
        <!-- ChatGPT Response -->
        <div id="chat-response" class="alert alert-info mt-3" style="display:none;"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {

        // Function to handle AJAX requests with file uploads
        function ajaxFileUpload(form, url, progressBarSelector, successCallback, errorCallback) {
            var formData = new FormData(form);
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $(progressBarSelector).css('width', percentComplete + '%').text(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: successCallback,
                error: errorCallback
            });
        }

        // Function to handle simple AJAX form submission
        function ajaxFormSubmit(form, url, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: 'POST',
                data: $(form).serialize(),
                success: successCallback,
                error: errorCallback
            });
        }

        // Step 1: Handle File Upload Form Submission
        $('#upload-form').on('submit', function(e) {
            e.preventDefault();
            $('#loading-spinner-upload').show();
            $('#upload-button').prop('disabled', true);
            $('#progress-bar-container').show();

            ajaxFileUpload(this, '{{ route("import") }}', '#progress-bar',
                function(response) {
                    $('#loading-spinner-upload').hide();
                    $('#progress-bar-container').hide();
                    $('#upload-feedback').addClass('alert-success').text('File uploaded successfully!').show();
                    $('#question-section').show(); // Show the question section after successful upload
                },
                function() {
                    $('#loading-spinner-upload').hide();
                    $('#progress-bar-container').hide();
                    $('#upload-feedback').addClass('alert-danger').text('Error uploading file. Please try again.').show();
                    $('#upload-button').prop('disabled', false);
                }
            );
        });

        // Step 2: Handle ChatGPT Question Form Submission
        $('#chat-form').on('submit', function(e) {
            e.preventDefault();
            $('#loading-spinner').show();
            $('#send-button').prop('disabled', true);
            $('#chat-response').hide();

            ajaxFormSubmit(this, '{{ route("ask") }}',
                function(response) {
                    $('#loading-spinner').hide();
                    $('#send-button').prop('disabled', false);
                    $('#chat-response').text(response.response).show(); // Show ChatGPT's response
                },
                function() {
                    $('#loading-spinner').hide();
                    $('#send-button').prop('disabled', false);
                    $('#chat-response').text('An error occurred while processing your request. Please try again.').show();
                }
            );
        });
    });
</script>


</body>
</html>
