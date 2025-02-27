<!DOCTYPE html>
<html>

<head>
    <title>ChatBot - Laravel 11 + OpenAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .chat-container {
            width: 70%;
            max-width: 100%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            perspective: 1000px;
        }

        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding-bottom: 15px;
            border-bottom: 2px solid #dee2e6;
        }

        .message {
            padding: 10px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
            margin-bottom: 10px;
            transform: translateZ(30px);
        }

        .user {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            text-align: right;
        }

        .bot {
            background-color: #e9ecef;
            color: black;
            align-self: flex-start;
        }

        .input-group {
            margin-top: 15px;
        }

        .form-control {
            border-radius: 30px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            border-radius: 30px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <h3 class="text-center">Chat Bot</h3>

        <div class="chat-box d-flex flex-column" id="chat-box">
            @foreach ($chats as $chat)
                <div class="message user align-self-end">
                    <strong>You:</strong> {{ $chat->user_message }}
                </div>
                <div class="message bot align-self-start">
                    <strong>Bot:</strong> {{ $chat->bot_response }}
                </div>
            @endforeach
        </div>

        <form id="chat-form" class="mt-3">
            <div class="row">
                <div class="form-group col-sm-11">
                    <input type="text" name="title" class="form-control" id="chat-input"
                        placeholder="Enter your query..." required />
                </div>
                <div class="col-sm-1">
                    <button type="submit" class="btn btn-success">Send</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#chat-form").submit(function(event) {
                event.preventDefault();

                let userMessage = $("#chat-input").val().trim();
                if (!userMessage) return;

                let chatBox = $("#chat-box");

                // Append User Message
                chatBox.append(
                    `<div class="message user align-self-end"><strong>You:</strong> ${userMessage}</div>`
                );

                // Clear input field
                $("#chat-input").val("");

                // Append Bot Message Placeholder
                let botMessageId = "bot-message-" + new Date().getTime();
                chatBox.append(
                    `<div class="message bot align-self-start" id="${botMessageId}"><strong>Bot:</strong> <span></span></div>`
                );

                let botMessageDiv = $("#" + botMessageId + " span");
                let botResponse = "";

                // Use EventSource for real-time streaming
                let eventSource = new EventSource(
                    `{{ route('chat-gpt.send') }}?title=${encodeURIComponent(userMessage)}`);

                eventSource.onmessage = function(event) {
                    if (event.data) {
                        botResponse += event.data;
                        botMessageDiv.html(botResponse.replace(/\n/g, "<br>")); // Preserve new lines
                    }
                };

                eventSource.onerror = function() {
                    eventSource.close();
                };
            });
        });
    </script>
</body>
</html>
