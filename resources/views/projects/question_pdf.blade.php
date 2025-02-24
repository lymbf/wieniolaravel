<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $question->title }} - PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            font-size: 14px;
            color: #333;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #F1C40F;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo img {
            max-height: 100px;
        }
        .title-container {
            text-align: right;
        }
        .title-container .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .title-container .subheader {
            font-size: 14px;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 18px;
            color: #F1C40F;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .content {
            margin-top: 10px;
            line-height: 1.5;
        }
        .attachment-list, .answer-list {
            list-style: none;
            padding: 0;
        }
        .attachment-list li, .answer-list li {
            margin-bottom: 5px;
        }
        .thumbnail {
            margin-bottom: 10px;
            text-align: center;
        }
        .thumbnail img {
            max-width: 100%;
            max-height: 150px;
        }
        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Nagłówek z logo i tytułem -->
    <div class="header">
        <div class="logo">
        <img src="{{ 'file://'.realpath(public_path('images/logo.png')) }}" alt="Logo" style="max-height: 100px;">

        </div>
        <div class="title-container">
            <div class="title">{{ $question->title }}</div>
            <div class="subheader">Projekt: {{ $project->name }}</div>
        </div>
    </div>
    
    <div class="section">
        <h2>Szczegóły pytania</h2>
        <div class="content">
            <p><strong>Zadane:</strong> {{ $question->asked_at->format('d.m.Y H:i') }} przez {{ $question->user->name }}</p>
            <p>{{ $question->description }}</p>
        </div>
    </div>
    
    @if($question->attachments->isNotEmpty())
    <div class="section">
        <h2>Załączniki do pytania</h2>
        <ul class="attachment-list">
            @foreach($question->attachments as $attachment)
                @php
                    $ext = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                    <li class="thumbnail">
                        <img src="file://{{ str_replace('\\', '/', public_path('storage/' . $attachment->file_path)) }}"
                             alt="{{ $attachment->original_name }}">
                    </li>
                @else
                    <li>{{ $attachment->original_name }}</li>
                @endif
            @endforeach
        </ul>
    </div>
    @endif

    <div class="section">
        <h2>Odpowiedzi</h2>
        @if($question->answers->isEmpty())
            <div class="content">Brak odpowiedzi.</div>
        @else
            @foreach($question->answers as $answer)
                <div class="content" style="margin-bottom: 10px;">
                    <p><strong>{{ $answer->user->name }}</strong> ({{ $answer->answered_at->format('d.m.Y H:i') }})</p>
                    <p>{{ $answer->answer_text }}</p>
                    @if($answer->attachments->isNotEmpty())
                        <p><strong>Załączniki:</strong></p>
                        <ul class="attachment-list">
                            @foreach($answer->attachments as $attachment)
                                @php
                                    $ext = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));
                                @endphp
                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                    <li class="thumbnail">
                                        <img src="file://{{ str_replace('\\', '/', public_path('storage/' . $attachment->file_path)) }}"
                                             alt="{{ $attachment->original_name }}">
                                    </li>
                                @else
                                    <li>{{ $attachment->original_name }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
                <hr>
            @endforeach
        @endif
    </div>

    <div class="footer">
        Strona wygenerowana przez System Zarządzania Projektami &copy; {{ date('Y') }}
    </div>
</body>
</html>
