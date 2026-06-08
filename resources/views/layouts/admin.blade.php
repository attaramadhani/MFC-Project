<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'MFC — Admin Panel' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        @include('layouts.admin_sidebar')

        <main class="admin-main">
            @if(isset($pageTitle) && $pageTitle !== 'Dashboard')
            <div class="admin-main-header">
                <div>
                    <h1 class="admin-page-title">{{ $pageTitle }}</h1>
                </div>
            </div>
            @endif

            <div class="admin-main-body">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- ═══ TOAST NOTIFICATION ═══ --}}
    @if(session('success') || session('error') || session('info'))
    <div class="mfc-toast-wrap" id="mfcToastWrap">
        <div class="mfc-toast {{ session('error') ? 'mfc-toast--error' : (session('info') ? 'mfc-toast--info' : 'mfc-toast--success') }}" id="mfcToast">
            <div class="mfc-toast-icon">
                @if(session('error'))
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @elseif(session('info'))
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                @endif
            </div>
            <div class="mfc-toast-msg">{{ session('success') ?? session('error') ?? session('info') }}</div>
            <button class="mfc-toast-close" onclick="document.getElementById('mfcToastWrap').remove()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="mfc-toast-progress" id="mfcToastProgress"></div>
        </div>
    </div>
    <script>
        (function() {
            const wrap = document.getElementById('mfcToastWrap');
            const progress = document.getElementById('mfcToastProgress');
            if (!wrap) return;
            setTimeout(() => wrap.classList.add('show'), 50);
            progress.style.transition = 'width 4s linear';
            setTimeout(() => progress.style.width = '0%', 100);
            setTimeout(() => {
                wrap.classList.remove('show');
                setTimeout(() => wrap.remove(), 400);
            }, 4200);
        })();
    </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/image_compressor.js') }}?v={{ filemtime(public_path('js/image_compressor.js')) }}"></script>
    @stack('scripts')
</body>
</html>