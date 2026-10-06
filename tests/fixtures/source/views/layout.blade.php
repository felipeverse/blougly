<!DOCTYPE html>
<html lang="{{ $site['languages'][0] ?? 'en-US' }}">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', $site['name'])</title>
    <link rel="stylesheet" href="/assets/style.css" />
</head>
<body>
    <header>
        <h1><a href="/">{{ $site['name'] }}</a></h1>
    </header>
    <main>@yield('content')</main>
    <footer>
        <p>{{ $site['description'] }}</p>
    </footer>
</body>
</html>
