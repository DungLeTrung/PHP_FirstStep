<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">BUMYAYA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" aria-current="page" href="/">Home</a>
                    </li>
                    @auth
                    @if (Auth::user()->role === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users') ? 'active' : '' }}" href="/users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products') ? 'active' : '' }}" href="/products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('categories') ? 'active' : '' }}" href="/categories">Categories</a>
                    </li>
                    @endif
                    @if (Auth::user()->role === 'User')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users') ? 'active' : '' }}" href="/orders/create">Order</a>
                    </li>
                    @endif
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="#">Contact</a>
                    </li>
                    @guest
                    <li class="nav-item" style="background-color: rgb(230, 14, 28); border-radius: 5%; margin-right: 10px; height: 40px;">
                        <a class="padding: 5px; nav-link d-flex align-items-center justify-content-center" href="/login" style="height: 100%;">
                            <label style="margin: 0; cursor: pointer; font-weight: bold; color: white;">Sign In</label>
                        </a>
                    </li>
                        <li class="nav-item" style="background-color: aqua; border-radius: 5%; ">
                            <a class="padding: 5px; nav-link d-flex align-items-center justify-content-center" href="/register" style="height: 100%;">
                                <label style="margin: 0; cursor: pointer; font-weight: bold; color: black;">Sign Up</label>
                            </a>
                        </li>
                    @endguest
                    @auth
                        <li class="nav-item" style="background-color: greenyellow; border-radius: 5%;">
                            <a class="nav-link padding: 5px; nav-link d-flex align-items-center justify-content-center" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <label style="margin: 0; cursor: pointer; font-weight: bold; color: black;">Log out</label>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
