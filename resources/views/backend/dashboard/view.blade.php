@extends('backend.master')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.css">
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 2rem !important;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-title {
            font-weight: 700;
            font-size: 2rem;
            position: relative;
            display: inline-block;
        }

        .user-name {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.15rem 0.75rem;
            border-radius: 50px;
            display: inline-block;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: perspective(100px) rotateX(5deg);
            animation: glow 2s infinite alternate;
        }

        .welcome-emoji {
            display: inline-block;
            animation: bounce 1.5s infinite;
        }

        .welcome-subtitle {
            opacity: 0.9;
            font-size: 1.15rem;
            margin-top: 0.5rem;
        }

        .welcome-meta {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-top: 1.5rem !important;
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
            }

            100% {
                box-shadow: 0 0 15px rgba(255, 255, 255, 0.8);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }
    </style>
@endpush

@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="welcome-card bg-gradient-primary text-white p-4 rounded">
                        <h2 class="welcome-title mb-1">
                            Welcome back,
                            <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>!
                            <span class="welcome-emoji">☺️</span>
                        </h2>
                        <p class="welcome-subtitle mb-0">Your cost management dashboard is ready</p>

                        <div class="welcome-meta d-flex mt-3">
                            <div class="mr-4">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span id="current-date">{{ now()->format('l, F j, Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-clock mr-2"></i>
                                <span id="current-time">{{ now()->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Update time every minute
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent =
                now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
        }

        setInterval(updateTime, 60000);
        updateTime(); // Initial call
    </script>
@endpush
