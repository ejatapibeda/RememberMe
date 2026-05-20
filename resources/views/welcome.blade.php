<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>RememberME</title>
</head>
<body>
    <div id="app"></div>
</body>
</html>
