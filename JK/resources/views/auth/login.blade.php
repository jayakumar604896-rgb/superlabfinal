<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SuperLab CMS</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f7ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            width: 1000px;
            max-width: 95vw;
            display: flex;
            min-height: 600px;
            animation: slideUp 0.6s ease-out forwards;
        }

        /* Left Split panel */
        .info-panel {
            flex: 1;
            background: linear-gradient(135deg, #005fa9 0%, #003661 100%);
            color: #ffffff;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .info-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -50px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
        }

        .brand-logo {
            padding: 10px;
            background: #ffffff;
            border-radius: 12px;
            display: inline-block;
        }

        .welcome-msg h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .welcome-msg p {
            color: #dbeafe;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .footer-note {
            font-size: 0.85rem;
            color: #93c5fd;
        }

        /* Right login form panel */
        .form-panel {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header h2 {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #6b7280;
            margin-bottom: 35px;
        }

        /* Input Controls */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #005fa9;
        }

        .form-control:focus {
            border-color: #005fa9;
            box-shadow: 0 0 0 0.25rem rgba(0, 95, 169, 0.15);
        }

        .btn-teal {
            background-color: #005fa9;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-teal:hover {
            background-color: #004780;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 95, 169, 0.2);
        }

        .btn-teal:active {
            transform: translateY(0);
        }

        /* Animation keyframes */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                min-height: auto;
            }
            .info-panel {
                padding: 40px 30px;
                min-height: 250px;
            }
            .form-panel {
                padding: 40px 30px;
            }
            .welcome-msg h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Info Branding Panel -->
        <div class="info-panel">
            <div class="brand-logo-container">
                <div class="brand-logo shadow-sm">
                    <!-- SVG Brand Logo -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 150" width="180" height="60">
                        <g>
                            <path d="M 45,35 C 38,35 34,42 34,50 C 34,58 39,63 43,68 C 45,71 42,75 38,76 C 30,78 22,86 22,96 C 22,108 30,116 38,124 C 40,126 44,124 43,121 C 40,112 36,98 42,88 C 44,85 48,88 47,91 C 44,102 46,112 49,122 C 50,125 54,124 53,120 C 49,106 50,88 58,74 C 64,63 67,52 64,40 C 62,37 57,40 58,44 C 61,54 58,63 53,70 C 50,74 46,71 47,67 C 49,58 48,48 51,39 C 52,35 48,35 45,35 Z" fill="#005fa9" />
                            <circle cx="45" cy="22" r="8" fill="#005fa9" />
                            <path d="M 28,68 C 18,78 12,90 12,106 C 12,122 20,132 26,140 C 28,142 31,140 29,137 C 23,128 18,114 24,100 C 27,94 31,98 29,102 C 24,114 25,126 29,137 C 30,140 34,138 33,134 C 28,118 31,98 39,84 C 41,80 34,75 32,77 C 30,79 29,74 28,68 Z" fill="#f39200" />
                            <path d="M 45,2 L 47,7 L 52,7 L 48,10 L 50,15 L 45,12 L 40,15 L 42,10 L 38,7 L 43,7 Z" fill="#f39200" transform="scale(0.8) translate(10, 0)" />
                            <path d="M 65,10 L 66,13 L 69,13 L 67,15 L 68,18 L 65,16 L 62,18 L 63,15 L 61,13 L 64,13 Z" fill="#005fa9" transform="scale(0.6) translate(40, 5)" />
                        </g>
                        <text x="110" y="80" font-family="'Outfit', sans-serif" font-weight="700" font-size="64" fill="#005fa9">Super</text>
                        <text x="290" y="80" font-family="'Outfit', sans-serif" font-weight="700" font-size="64" fill="#f39200">Lab</text>
                        <text x="170" y="130" font-family="'Outfit', sans-serif" font-weight="400" font-size="28" fill="#1e293b">by</text>
                        <text x="210" y="130" font-family="'Outfit', sans-serif" font-style="italic" font-weight="700" font-size="32" fill="#d61a21">Phlebee</text>
                    </svg>
                </div>
            </div>

            <div class="welcome-msg my-4">
                <h1>Precision Diagnostics, Trusted Care.</h1>
                <p>Welcome to the SuperLab Portal. Manage clinical records, diagnostics catalogs, website content, and role-based actions securely from one centralized dashboard.</p>
            </div>

            <div class="footer-note">
                &copy; 2026 SuperLab Diagnostics. All rights reserved.
            </div>
        </div>

        <!-- Form Login Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Please enter your credentials to access the admin panel.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="form-floating mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="email" autofocus>
                    <label for="email"><i class="fa-solid fa-envelope me-2 text-muted"></i>Email Address</label>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-floating mb-3">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                    <label for="password"><i class="fa-solid fa-lock me-2 text-muted"></i>Password</label>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Remember Me and Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary" for="remember" style="font-size: 0.9rem;">
                            Remember Me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #0d9488; font-size: 0.9rem; font-weight: 500;">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-teal w-100 mb-3 border-0">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
