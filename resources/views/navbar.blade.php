<nav class="navbar bg-body-tertiary {{ Auth::check() ? 'bg-primary' : 'bg-warning' }} bg-gradient">
  <div class="container-fluid row">
    <div class="navbar-brand text-white col">
      <a class="navbar-brand text-white fs-2">Technical Publication</a>
      {{-- @auth    
      <a href="{{ route('login') }}" class="text-white fst-italic fs-6">{{ Auth::check() ? Auth::user()->name : 'Login' }}</a>
      @else
      @endauth --}}
      {{-- <a href="/dashboard" class="text-white fst-italic fs-6">{{ Auth::check() ? Auth::user()->name : 'Login' }}</a> --}}
      {{-- <a class="btn dropdown-toggle text-white fs-6 dropdown-toggle" role="button" data-bs-toggle="dropdown">
        {{ Auth::check() ? Auth::user()->name : 'Login' }}
      </a> --}}
      @auth
      <div class="btn-group">
        <button type="button" class="btn text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::check() ? Auth::user()->name : 'Login' }}</button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
          <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
          <li>
            {{-- <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">Logout</button>
            </form> --}}
            <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
          </li>
        </ul>
      </div>
      @else
      <a href="{{ route('login') }}" class="text-white">{{ Auth::check() ? Auth::user()->name : 'Login' }}</a>
      @endauth
    </div>

    <div class="col d-flex justify-content-center">
      <a class="navbar-brand text-white" href="/">Home</a>
      <a class="navbar-brand text-white">About</a>
      <a class="navbar-brand text-white">Contact</a>
    </div>

    <form class="d-flex col" role="search">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-light text-white bg" type="submit">Search</button>
    </form>
  </div>
</nav>
