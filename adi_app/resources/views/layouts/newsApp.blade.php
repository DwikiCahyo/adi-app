<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-icon-180x180.png') }}">
        
        <link rel="manifest" href="{{asset('manifest.json')}}">
        <meta name="theme-color" content="#ffffff">

        <!-- Scripts -->
        @php
            $isProduction = app()->environment('production');
            $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
        @endphp

        @if ($isProduction && file_exists($manifestPath))
            @php
                $manifest = json_decode(file_get_contents($manifestPath), true);
            @endphp
            <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
            <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        @else
            @viteReactRefresh
            @vite(['resources/js/app.js', 'resources/css/app.css'])
        @endif

        <!-- Modal Pop-up Styles -->
        <style>
            /* Overlay */
            .modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9998;
                animation: fadeIn 0.3s ease;
                opacity: 1;
                transition: opacity 0.3s ease;
            }

            .modal-overlay.active {
                display: block;
            }

            .modal-overlay.closing {
                opacity: 0;
            }

            /* Modal Container */
            .modal-container {
                display: none;
                position: fixed;
                z-index: 9999;
                animation: slideIn 0.3s ease;
                opacity: 1;
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            .modal-container.active {
                display: block;
            }

            .modal-container.closing {
                opacity: 0;
            }

            /* Desktop: Modal di tengah */
            @media (min-width: 768px) {
                .modal-container {
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 90%;
                    max-width: 500px;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                }

                .modal-container.closing {
                    transform: translate(-50%, -48%);
                }
            }

            /* Mobile: Full screen */
            @media (max-width: 767px) {
                .modal-container {
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }

                .modal-container.closing {
                    transform: translateY(20px);
                }
            }

            /* Modal Content */
            .modal-content {
                position: relative;
                width: 100%;
                height: 100%;
                background: linear-gradient(to bottom, #4DD0E1 0%, #2196F3 100%);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
            }

            @media (min-width: 768px) {
                .modal-content {
                    min-height: 600px;
                }
            }

            /* Close Button */
            .close-btn {
                position: absolute;
                top: 16px;
                right: 16px;
                width: 40px;
                height: 40px;
                background-color: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: #333;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .close-btn:hover {
                background-color: #fff;
                transform: scale(1.1);
            }

            .close-btn.visible {
                display: flex;
            }

            .close-btn.counting {
                display: flex;
                cursor: not-allowed;
                opacity: 0.6;
                font-size: 18px;
                font-weight: bold;
            }

            /* Logo */
            .logo {
                width: 180px;
                height: auto;
                margin-bottom: 40px;
            }

            .logo img {
                width: 100%;
                height: auto;
                display: block;
            }

            /* Text */
            .logo-text {
                text-align: center;
                margin-top: -20px;
                margin-bottom: 40px;
            }

            .logo-text h2 {
                font-size: 32px;
                font-weight: bold;
                color: #1A237E;
                letter-spacing: 1px;
            }

            .main-title {
                text-align: center;
                color: white;
                margin-bottom: 20px;
            }

            .main-title h1 {
                font-size: 48px;
                font-weight: bold;
                margin-bottom: 10px;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .main-title p {
                font-size: 28px;
                font-weight: 600;
                line-height: 1.4;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .subtitle {
                text-align: center;
                color: white;
                font-size: 24px;
                font-weight: 600;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            @media (max-width: 767px) {
                .logo {
                    width: 160px;
                }

                .main-title h1 {
                    font-size: 40px;
                }

                .main-title p {
                    font-size: 24px;
                }

                .subtitle {
                    font-size: 20px;
                }
            }

            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -48%);
                }
                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }

            @media (max-width: 767px) {
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-blue-100">
            {{-- Header --}}
            @include('layouts.headerNews')
        
            {{-- Top Menu --}}
            @if (request()->routeIs('news.index') || request()->routeIs('events.index') || request()->routeIs('resource.index'))
                @include('layouts.top-menuNews')
            @endif
        
            {{-- Main Content --}}
            <main class="flex-grow p-4 space-y-4">
                @yield('content')
            </main>
        
            {{-- Bottom Nav --}}
            @include('layouts.bottom-navNews')

            <footer class="bg-white shadow mt-6">
                
            </footer>
        </div>

        {{-- Modal Pop-up - Only show on news.index --}}
        @if(request()->routeIs('news.index'))
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-container" id="modalContainer">
            <div class="modal-content">
                {{-- Close Button --}}
                <button class="close-btn" id="closeBtn" onclick="closeModal()">×</button>

                {{-- Logo --}}
                <div class="logo">
                    <img src="{{ asset('Images/Logo.png') }}" alt="MyCDC7K Logo">
                </div>

                <div class="logo-text">
                    <h2>MyCDC7K</h2>
                </div>

                {{-- Main Title --}}
                <div class="main-title">
                    <p>Be</p>
                    <p>Impactful Church<br>for Community</p>
                </div>

                {{-- Subtitle --}}
                <div class="subtitle">
                    Pray - Connect - Care - Share
                </div>
            </div>
        </div>

        {{-- Modal JavaScript --}}
        <script>
            // Track if user has left the page
            var hasLeftPage = false;

            // Check if modal has been closed in this browsing session
            var modalClosed = sessionStorage.getItem('mycdc7k_modal_closed');

            // Detect when user leaves the page (go to home screen or switch apps)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // User left the page/app
                    hasLeftPage = true;
                } else if (hasLeftPage) {
                    // User came back to the page/app
                    // Reset the modal closed status
                    sessionStorage.removeItem('mycdc7k_modal_closed');
                    
                    // Reload the page to show modal again
                    window.location.reload();
                }
            });

            // Also detect page focus/blur for iOS compatibility
            window.addEventListener('blur', function() {
                hasLeftPage = true;
            });

            window.addEventListener('focus', function() {
                if (hasLeftPage && sessionStorage.getItem('mycdc7k_modal_closed')) {
                    sessionStorage.removeItem('mycdc7k_modal_closed');
                    window.location.reload();
                }
            });

            // Show modal on page load if it hasn't been manually closed
            if (!modalClosed) {
                window.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('modalOverlay').classList.add('active');
                    document.getElementById('modalContainer').classList.add('active');
                    
                    var closeBtn = document.getElementById('closeBtn');
                    var countdown = 3;
                    
                    // Show button with countdown
                    closeBtn.classList.add('counting');
                    closeBtn.textContent = countdown;
                    
                    // Countdown interval
                    var countdownInterval = setInterval(function() {
                        countdown--;
                        closeBtn.textContent = countdown;
                        
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            closeBtn.textContent = '×';
                            closeBtn.classList.remove('counting');
                            closeBtn.classList.add('visible');
                            closeBtn.style.cursor = 'pointer';
                            closeBtn.style.opacity = '1';
                        }
                    }, 1000);
                });
            }

            // Close modal function with animation
            function closeModal() {
                var closeBtn = document.getElementById('closeBtn');
                
                // Only allow close if countdown is finished
                if (closeBtn.classList.contains('counting')) {
                    return;
                }
                
                var overlay = document.getElementById('modalOverlay');
                var container = document.getElementById('modalContainer');
                
                // Add closing animation
                overlay.classList.add('closing');
                container.classList.add('closing');
                
                // Mark modal as closed for this session
                sessionStorage.setItem('mycdc7k_modal_closed', 'true');
                
                // Remove elements after animation completes
                setTimeout(function() {
                    overlay.classList.remove('active', 'closing');
                    container.classList.remove('active', 'closing');
                }, 300);
            }

            // Close when clicking overlay
            document.getElementById('modalOverlay').addEventListener('click', function() {
                var closeBtn = document.getElementById('closeBtn');
                if (closeBtn.classList.contains('visible')) {
                    closeModal();
                }
            });
        </script>
        @endif
    </body>
</html>