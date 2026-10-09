{{-- Name from Settings › General (same as the app), the built-in one if none is set or the database is down --}}
@php($siteName = rescue(fn () => trim((string) setting('config_name')), '', false) ?: 'SHM-System')
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteName }}</title>
    <!-- Fonts are self-hosted, see resources/sass/shm/_fonts.scss -->
    <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/assets/fonts/KantumruyPro.woff2') }}" as="font" type="font/woff2" crossorigin>
    <!-- Styles: Material Icons is a static file; the rest is built by Vite (see vite.config.js) -->
    <link rel="stylesheet" href="{{ asset('css/material-icons/material-icons.css') }}">
    @vite(['resources/assets/css/vuesax.css', 'node_modules/vue-select/dist/vue-select.css', 'resources/assets/css/main.css', 'resources/sass/app.scss', 'resources/js/app.js'])
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/logo/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/apple-touch-icon.png') }}">
    <!-- Theme before first paint: saved choice, else device setting (same rule as store/state.js) -->
    <script>
      (function () {
        var theme = null
        try { theme = localStorage.getItem('theme') } catch (e) {}
        if (theme !== 'light' && theme !== 'dark') {
          theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }
        document.documentElement.setAttribute('data-theme', theme)
      })()
    </script>
    <!-- Loading screen in the sidebar's blue; removed by router.afterEach -->
    <style>
      #loading-bg {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 18px;
        background: linear-gradient(150deg, #5972ac 0%, #324693 100%);
        color: #fff;
        font-family: 'Kantumruy Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        transition: opacity .35s ease, visibility .35s ease;
      }
      html[data-theme="dark"] #loading-bg {
        background: linear-gradient(150deg, #324693 0%, #10163a 100%);
      }
      #loading-bg.is-hidden {
        opacity: 0;
        visibility: hidden;
      }
      .loading-logo {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 96px;
        height: 96px;
      }
      .loading-logo img {
        width: 72px;
        height: 72px;
        padding: 4px;
        border-radius: 50%;
        background: #fff;
        object-fit: contain;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, .18), 0 10px 24px rgba(0, 0, 0, .25);
        animation: loading-pulse 1.6s ease-in-out infinite;
      }
      .loading-logo::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, .18);
        border-top-color: #fff;
        animation: loading-spin .9s linear infinite;
      }
      .loading-title {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: .02em;
      }
      .loading-dots {
        display: flex;
        gap: 6px;
        margin-top: -6px;
      }
      .loading-dots span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .85);
        animation: loading-dot 1.2s ease-in-out infinite;
      }
      .loading-dots span:nth-child(2) { animation-delay: .15s; }
      .loading-dots span:nth-child(3) { animation-delay: .3s; }
      @keyframes loading-spin { to { transform: rotate(360deg); } }
      @keyframes loading-pulse { 50% { transform: scale(.94); } }
      @keyframes loading-dot { 0%, 80%, 100% { opacity: .3; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-4px); } }
      @media (prefers-reduced-motion: reduce) {
        #loading-bg, .loading-logo img, .loading-logo::before, .loading-dots span { animation: none; transition: none; }
      }
    </style>
  </head>
  <body>
    <!-- Same body class App.vue sets, applied now so the first paint already matches -->
    <script>
      document.body.classList.add(document.documentElement.getAttribute('data-theme') === 'dark' ? 'theme-dark' : 'theme-semi-dark')
    </script>
    <div id="loading-bg" role="status" aria-label="Loading">
      <div class="loading-logo"><img src="{{ asset('images/logo/logo.svg') }}" alt=""></div>
      <div class="loading-title">{{ $siteName }}</div>
      <div class="loading-dots"><span></span><span></span><span></span></div>
    </div>
    <noscript>
      <strong>{{ $siteName }} needs JavaScript. Please enable it to continue. · សូមបើក JavaScript ដើម្បីប្រើប្រព័ន្ធ SHM។</strong>
    </noscript>
    <div id="app">
    </div>

  </body>
</html>
