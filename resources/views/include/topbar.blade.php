<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/dashboard">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/log-list">Log</a>
                </li>
                @if (Auth::user()->supervisor == 0)
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="/user">User Management</a>
                </li>
                @endif
            </ul>
            <div class="d-flex align-items-center my-2 my-lg-0">
                <a href="/user/profile" class="text-decoration-none me-3">
                    @if (Auth::user()->profile_path)
                        <img src="{!! Auth::user()->profile_path !!}" alt="" class="round-prof">
                    @else
                        Profile
                    @endif
                </a>
                <a href="/user/logout" class="text-decoration-none text-color-black">Logout <i class="bi bi-door-open"></i></a>
            </div>
        </div>
    </div>
</nav>
