<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>404 - {{ config('app.name', 'FurShield') }}</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" integrity="sha384-Bk5cbLkZQ5raZ0+H2/+VbfYx3WpvxvQK4zqXZr7sYODuaX7bKXoSOnipQxkaS8sv" crossorigin="anonymous">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(160deg,#f0f7f2 0%,#e2f1eb 40%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#0a1648;-webkit-font-smoothing:antialiased}
.err-card{text-align:center;padding:48px 40px;max-width:460px;width:100%}
.err-paw{margin-bottom:24px}
.err-paw svg{width:72px;height:72px;filter:drop-shadow(0 4px 12px rgba(26,107,60,.15))}
.err-code{font-size:120px;font-weight:800;line-height:1;color:#1a6b3c;opacity:.10;letter-spacing:-4px;margin-bottom:-30px;user-select:none}
.err-icon{font-size:40px;color:#1a6b3c;margin-bottom:16px;opacity:.7}
.err-title{font-size:22px;font-weight:700;color:#0a1648;margin-bottom:8px}
.err-msg{font-size:15px;color:#52699b;line-height:1.6;margin-bottom:28px}
.err-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#1a6b3c;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;transition:all .2s;border:0;cursor:pointer}
.err-btn:hover{background:#145730;color:#fff;transform:translateY(-1px);box-shadow:0 4px 16px rgba(26,107,60,.25)}
.err-links{margin-top:20px;display:flex;gap:20px;justify-content:center}
.err-links a{color:#52699b;text-decoration:none;font-size:13px;font-weight:500;transition:color .2s}
.err-links a:hover{color:#1a6b3c}
</style>
</head>
<body>
<div class="err-card">
<div class="err-paw">
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" fill="#1a6b3c"><ellipse cx="11" cy="17" rx="5.5" ry="7.5" transform="rotate(-25 11 17)"/><ellipse cx="21" cy="9" rx="5.5" ry="7.5" transform="rotate(-8 21 9)"/><ellipse cx="33" cy="10" rx="5.5" ry="7.5" transform="rotate(15 33 10)"/><ellipse cx="41" cy="22" rx="5.5" ry="7.5" transform="rotate(30 41 22)"/><path d="M10 36c0-5 6-8 9-13 3-5 8-5 11 0 3 5 9 9 9 14 0 6-5 8-10 6-5-2-7-2-11 0-5 2-8-1-8-7z"/></svg>
</div>
<div class="err-code">404</div>
<div class="err-icon"><i class="bi bi-compass"></i></div>
<h1 class="err-title">Page Not Found</h1>
<p class="err-msg">Looks like this page wandered off on its own adventure. The link might be broken or the page may have been moved.</p>
<a href="{{ url('/') }}" class="err-btn"><i class="bi bi-house-door"></i> Go Home</a>
<div class="err-links">
<a href="javascript:history.back()"><i class="bi bi-arrow-left"></i> Go Back</a>
@guest<a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a>@endguest
@auth<a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>@endauth
</div>
</div>
</body>
</html>
