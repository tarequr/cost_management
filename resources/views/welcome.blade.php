<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cost Management Landing Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS */
        .hero-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .hero-content {
            text-align: center;
            padding: 2rem;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #2C3E50, #3498DB);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.25rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .auth-buttons .btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Cost Management</a>
            <div class="auth-buttons ms-auto">
                @if (Route::has('login'))
                    <div class="">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-outline-primary">Home</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif


                {{-- <a class="btn btn-outline-primary">Login</a>
                <a class="btn btn-primary">Register</a> --}}
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Cost Management</h1>
                <p class="hero-description">
                    Transform your financial management with our powerful cost tracking and optimization platform.
                    Take control of your expenses and make informed decisions for your business growth.
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Cost Management. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

