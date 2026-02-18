<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reviewer Feedback - {{ $article->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header-info {
            margin-top: 10px;
            font-size: 11px;
            color: #666;
        }
        .reviewer-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .reviewer-info h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .reviewer-info p {
            margin: 5px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .question-cell {
            font-weight: bold;
            width: 40%;
            background-color: #f5f5f5;
        }
        .answer-cell {
            width: 60%;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reviewer Feedback Report</h1>
        <div class="header-info">
            <strong>Article Title:</strong> {{ $article->title }}<br>
            <strong>Article ID:</strong> {{ $article->unique_code }}<br>
            <strong>Generated Date:</strong> {{ date('F j, Y') }}
        </div>
    </div>

    <div class="reviewer-info">
        <h2>Reviewer Information</h2>
        <p><strong>Name:</strong> {{ $comment->name }} {{ $comment->sur_name ?? '' }}</p>
        <p><strong>Email:</strong> {{ $comment->email }}</p>
        <p><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($comment->created_at)->format('F j, Y') }}</p>
        <p><strong>Status:</strong> {{ App\Helper::displayReviewerCommentStatus($comment->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Response</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $qa)
            <tr>
                <td class="question-cell">{{ $qa['question'] }}</td>
                <td class="answer-cell">{!! nl2br(e($qa['answer'])) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This document was generated on {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>

