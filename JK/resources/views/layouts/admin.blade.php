<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'SuperLab') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #94a3b8;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        #sidebar .sidebar-header h3 {
            color: #f8fafc;
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #sidebar ul.components {
            padding: 15px 0;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border-left: 3px solid transparent;
        }

        #sidebar ul li a:hover {
            color: #f39200;
            background: rgba(255,255,255,0.03);
            border-left-color: #f39200;
        }

        #sidebar ul li.active > a {
            color: #f8fafc;
            background: rgba(0, 95, 169, 0.15);
            border-left-color: #f39200;
            font-weight: 500;
        }

        #sidebar ul li a i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        #sidebar ul li div.collapse ul li a {
            padding: 8px 20px 8px 45px;
            font-size: 0.88rem;
            color: #94a3b8;
            border-left: 3px solid transparent;
        }
        #sidebar ul li div.collapse ul li a:hover {
            color: #f39200;
            background: rgba(255,255,255,0.02);
            border-left-color: #f39200;
        }
        #sidebar ul li div.collapse ul li.active > a {
            color: #f8fafc;
            background: rgba(0, 95, 169, 0.1);
            border-left-color: #f39200;
            font-weight: 500;
        }

        /* Navbar & Main Content */
        #content {
            width: 100%;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .main-container {
            padding: 30px;
            flex-grow: 1;
        }

        /* Cards & Buttons */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            font-weight: 600;
        }

        .card-body {
            padding: 24px;
        }

        .btn-primary {
            background-color: #005fa9;
            border-color: #005fa9;
        }

        .btn-primary:hover {
            background-color: #004780;
            border-color: #004780;
        }

        .btn-teal {
            background-color: #f39200;
            border-color: #f39200;
            color: #ffffff;
        }

        .btn-teal:hover {
            background-color: #d67f00;
            border-color: #d67f00;
            color: #ffffff;
        }

        /* Badge Customization */
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
        }
        .badge-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animated-fade {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
                position: fixed;
                height: 100vh;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
            }
            .main-navbar {
                padding: 15px 15px;
            }
            .main-container {
                padding: 15px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header py-3 bg-white">
                <!-- SVG Brand Logo -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 150" width="100%" height="45">
                    <!-- Abstract Figure -->
                    <g>
                        <!-- Blue Upper Body & Head -->
                        <path d="M 45,35 C 38,35 34,42 34,50 C 34,58 39,63 43,68 C 45,71 42,75 38,76 C 30,78 22,86 22,96 C 22,108 30,116 38,124 C 40,126 44,124 43,121 C 40,112 36,98 42,88 C 44,85 48,88 47,91 C 44,102 46,112 49,122 C 50,125 54,124 53,120 C 49,106 50,88 58,74 C 64,63 67,52 64,40 C 62,37 57,40 58,44 C 61,54 58,63 53,70 C 50,74 46,71 47,67 C 49,58 48,48 51,39 C 52,35 48,35 45,35 Z" fill="#005fa9" />
                        <circle cx="45" cy="22" r="8" fill="#005fa9" />
                        <!-- Orange Swooshes -->
                        <path d="M 28,68 C 18,78 12,90 12,106 C 12,122 20,132 26,140 C 28,142 31,140 29,137 C 23,128 18,114 24,100 C 27,94 31,98 29,102 C 24,114 25,126 29,137 C 30,140 34,138 33,134 C 28,118 31,98 39,84 C 41,80 34,75 32,77 C 30,79 29,74 28,68 Z" fill="#f39200" />
                        <!-- Small Stars -->
                        <path d="M 45,2 L 47,7 L 52,7 L 48,10 L 50,15 L 45,12 L 40,15 L 42,10 L 38,7 L 43,7 Z" fill="#f39200" transform="scale(0.8) translate(10, 0)" />
                        <path d="M 65,10 L 66,13 L 69,13 L 67,15 L 68,18 L 65,16 L 62,18 L 63,15 L 61,13 L 64,13 Z" fill="#005fa9" transform="scale(0.6) translate(40, 5)" />
                    </g>
                    <!-- Text: Super Lab -->
                    <text x="110" y="80" font-family="'Outfit', sans-serif" font-weight="700" font-size="64" fill="#005fa9">Super</text>
                    <text x="290" y="80" font-family="'Outfit', sans-serif" font-weight="700" font-size="64" fill="#f39200">Lab</text>
                    <!-- Subtext: by Phlebee -->
                    <text x="170" y="130" font-family="'Outfit', sans-serif" font-weight="400" font-size="28" fill="#1e293b">by</text>
                    <text x="210" y="130" font-family="'Outfit', sans-serif" font-style="italic" font-weight="700" font-size="32" fill="#d61a21">Phlebee</text>
                </svg>
            </div>

            <ul class="list-unstyled components">
                @can('view dashboard')
                <li class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>
                @endcan

                @if(auth()->user()->can('manage users') || auth()->user()->can('manage roles'))
                <li>
                    <a href="#userMgmtSubmenu" data-bs-toggle="collapse" aria-expanded="{{ Request::routeIs('admin.users.*') || Request::routeIs('admin.roles.*') ? 'true' : 'false' }}" class="dropdown-toggle d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-users-gear"></i> User Management</span>
                    </a>
                    <div class="collapse {{ Request::routeIs('admin.users.*') || Request::routeIs('admin.roles.*') ? 'show' : '' }}" id="userMgmtSubmenu">
                        <ul class="list-unstyled">
                            @can('manage users')
                            <li class="{{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.users.index') }}"><i class="fa-solid fa-circle-user" style="font-size:0.8rem;"></i> Users</a>
                            </li>
                            @endcan
                            @can('manage roles')
                            <li class="{{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.roles.index') }}"><i class="fa-solid fa-user-shield" style="font-size:0.8rem;"></i> Roles</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif

                @can('manage customers')
                <li class="{{ Request::routeIs('admin.customers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.customers.index') }}">
                        <i class="fa-solid fa-hospital-user"></i> Customers
                    </a>
                </li>
                @endcan

                @can('manage categories')
                <li class="{{ Request::routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fa-solid fa-tags"></i> Categories
                    </a>
                </li>
                @endcan

                @can('manage services')
                <li class="{{ Request::routeIs('admin.services.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.services.index') }}">
                        <i class="fa-solid fa-microscope"></i> Tests
                    </a>
                </li>
                @endcan

                @can('manage packages')
                <li class="{{ Request::routeIs('admin.packages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.packages.index') }}">
                        <i class="fa-solid fa-cubes"></i> Packages
                    </a>
                </li>
                @endcan

                @can('manage payment types')
                <li class="{{ Request::routeIs('admin.payment-types.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment-types.index') }}">
                        <i class="fa-solid fa-credit-card"></i> Payment Types
                    </a>
                </li>
                @endcan

                @can('manage coupons')
                <li class="{{ Request::routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}">
                        <i class="fa-solid fa-ticket"></i> Coupons
                    </a>
                </li>
                @endcan

                @can('manage bookings')
                <li class="{{ Request::routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.bookings.index') }}">
                        <i class="fa-solid fa-calendar-check"></i> Booking History
                    </a>
                </li>
                @endcan

                @can('manage payments')
                <li class="{{ Request::routeIs('admin.payments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payments.index') }}">
                        <i class="fa-solid fa-receipt"></i> Payment History
                    </a>
                </li>
                @endcan

                @can('manage payment gateways')
                <li class="{{ Request::routeIs('admin.payment-gateways.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payment-gateways.index') }}">
                        <i class="fa-solid fa-network-wired"></i> Payment Gateways
                    </a>
                </li>
                @endcan

                @can('manage pages')
                <li class="{{ Request::routeIs('admin.pages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pages.index') }}">
                        <i class="fa-solid fa-file-lines"></i> Pages
                    </a>
                </li>
                @endcan

                @can('manage blogs')
                <li class="{{ Request::routeIs('admin.blogs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.blogs.index') }}">
                        <i class="fa-solid fa-newspaper"></i> Blog/News
                    </a>
                </li>
                @endcan

                @can('manage gallery')
                <li class="{{ Request::routeIs('admin.gallery.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.gallery.index') }}">
                        <i class="fa-solid fa-images"></i> Gallery
                    </a>
                </li>
                @endcan

                @can('manage enquiries')
                <li class="{{ Request::routeIs('admin.enquiries.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.enquiries.index') }}">
                        <i class="fa-solid fa-envelope-open-text"></i> Contact Enquiries
                    </a>
                </li>
                @endcan

                @can('manage testimonials')
                <li class="{{ Request::routeIs('admin.testimonials.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.testimonials.index') }}">
                        <i class="fa-solid fa-star-half-stroke"></i> Testimonials
                    </a>
                </li>
                @endcan

                @can('manage service reviews')
                <li class="{{ Request::routeIs('admin.service-reviews.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.service-reviews.index') }}">
                        <i class="fa-solid fa-comments"></i> Test Reviews
                    </a>
                </li>
                @endcan

                @can('manage locations')
                <li class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.locations.index') }}">
                        <i class="fa-solid fa-location-dot"></i> Locations
                    </a>
                </li>
                @endcan

                @can('manage footer locations')
                <li class="{{ Request::routeIs('admin.footer-locations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.footer-locations.index') }}">
                        <i class="fa-solid fa-map-location-dot"></i> Footer Locations
                    </a>
                </li>
                @endcan

                @can('manage settings')
                <li class="{{ Request::routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="fa-solid fa-gears"></i> Settings
                    </a>
                </li>
                @endcan
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Header/Navbar -->
            <header class="main-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary me-3 d-md-none">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2 class="h5 mb-0 fw-semibold">
                        @yield('page_title', 'Dashboard')
                    </h2>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none dropdown-toggle text-secondary" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-regular fa-circle-user fa-lg"></i> {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" aria-labelledby="userMenu">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                <i class="fa-solid fa-id-card me-2"></i> My Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </header>

            <!-- Main Body Container -->
            <main class="main-container animated-fade">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif



                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const sidebarCollapse = document.getElementById("sidebarCollapse");
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener("click", function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle("active");
                });
            }
            // Close sidebar when clicking outside on mobile
            document.addEventListener("click", function (e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains("active")) {
                    if (!sidebar.contains(e.target) && e.target !== sidebarCollapse) {
                        sidebar.classList.remove("active");
                    }
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
