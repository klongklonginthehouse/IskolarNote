<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'IskolarNotes')</title>
    <link rel="stylesheet" href="{{ asset('css/iskolar.css') }}">
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<main class="wrap auth-shell" id="main">
    <section class="hero" aria-labelledby="hero-title">
        <h1 id="hero-title">@yield('hero-title', 'IskolarNotes')</h1>
        @yield('hero')
    </section>

    <section class="panel auth-card" aria-labelledby="form-title">
        <h2 id="form-title">@yield('form-title')</h2>
        @yield('form')
        <p class="auth-alt">@yield('alt')</p>
    </section>
</main>
</body>
</html>
