<!DOCTYPE html>
<html>

<head>
    <title>Todo Reminder</title>
</head>

<body>
    <h1>Reminder: {{ $todo->title }}</h1>
    <p>This is a reminder for your todo scheduled at {{ $todo->due_date->format('Y-m-d H:i:s') }}.</p>
    <p>Please find attached the CSV file containing extra details.</p>
</body>

</html>
