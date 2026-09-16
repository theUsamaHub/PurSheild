<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FurShield') }} - Sign In</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
            <h4 class="auth-title text-center">FurShield</h4>
            <p class="auth-subtitle text-center">Every Paw/Wing Deserves a Shield of Love</p>

            <div class="mt-4">
                {{ $slot }}
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
