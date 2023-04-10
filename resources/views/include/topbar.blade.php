{{-- <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
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
</nav> --}}


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light navbar-white sticky-header">
    <div class="container">
      <a href="/dashboard" class="navbar-brand">
        <i class="fa-solid fa-torii-gate"></i>
        <span class="brand-text font-weight-light">Logbook</span>
      </a>
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="/dashboard" class="nav-link"><i class="fa-solid fa-house"></i> Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="/log-list" class="nav-link"><i class="fa-solid fa-file-lines"></i> Log</a>
        </li>
        @if (Auth::user()->supervisor == 0)
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="/user"><i class="fa-solid fa-users-gear"></i> User Management</a>
            </li>
        @endif
      </ul>
      <!-- SEARCH FORM -->
      {{-- <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form> --}}
      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Profile Dropdown Menu -->
        <li class="nav-item dropdown open">
            <a class="nav-link" data-toggle="dropdown" id="profile-btn" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ Auth::user()->profile_path }}" class="round-prof mr-3 img-circle" alt="">
                {{-- <i class="fa-solid fa-user"></i> --}}
            </a>
            <div class="dropdown-menu" aria-labelledby="profile-btn">
                <a class="dropdown-item" href="/user/profile"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="/user/logout" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->