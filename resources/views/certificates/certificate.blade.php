<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 100px; }
        h1 { font-size: 40px; margin-bottom: 50px; }
    </style>
</head>
<body>
    <h1>Certificate of Completion</h1>
    <p>This certifies that <strong>{{ $user->name }}</strong></p>
    <p>has successfully completed the course</p>
    <p><strong>{{ $course->name }}</strong></p>
    <p>Date: {{ $date }}</p>
</body>
</html>
