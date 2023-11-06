<nav class="navbar bg-body-tertiary {{ Auth::check() ? 'bg-primary' : 'bg-warning' }} bg-gradient">
  <div class="container-fluid row">
    <div class="navbar-brand text-white col">
      <a class="navbar-brand text-white fs-2">Technical Publication</a>
      <span type="button" class="text-white fst-italic fs-6" data-bs-toggle="modal" data-bs-target="#loginModal">
        {{ Auth::check() ? Auth::user()->name : 'Login' }}
      </span>
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

<!-- Modal -->
{{-- <form method="POST" action="{{ Auth::check() ? route('register') : route('login'); }}">
  @csrf --}}
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="loginModalLabel">Login Form</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        @guest
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="modal-body">
            {{-- login form --}}
            <!-- Email -->
            <div class="mb-3">
              <x-input-label for="email" :value="__('Email')" />
              {{-- <div class="input-group">
                <input type="text" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                <span class="input-group-text">@indonesian-aerospace.com</span>
              </div> --}}
              <x-text-input id="email" class="form-control" type="email" name="email" required autocomplete="email" />
              <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <!-- Password -->
            <div class="mb-3">
              <x-input-label for="password" :value="__('Password')" />
              <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="password" />
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            {{-- <div class="input-group has-validation">
              <span class="input-group-text">@</span>
              <div class="form-floating is-invalid">
                <input type="password" class="form-control is-invalid" id="password" placeholder="Password" name="password" required>
                <label for="password">Password</label>
              </div>
              <div class="invalid-feedback">
                Please choose a username.
              </div>
            </div> --}}
            <!-- end of login form -->
          </div>
          {{-- Button Login --}}
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Login</button>
          </div>  
        </form>

        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="loginModalLabel">Registration Form</h1>
          </div>
          <div class="modal-body">
            <!-- registration form -->
            <!-- Name -->
            <div class="mb-3">
              <x-input-label for="name_register" :value="('Name')"/>
              <x-text-input id="name_register" class="form-control" type="text" name="name_register" :value="old('name_register')" required autocomplete="name" />
              <x-input-error :messages="$errors->get('name_register')" class="mt-2" />
            </div>
            <!-- Email -->
            <div class="mb-3">
              <x-input-label for="email_register" :value="__('Email')" />
              {{-- <div class="input-group">
                <input type="text" class="form-control" type="email" name="email_register" value="{{ old('email_register') }}" required autocomplete="email">
                <span class="input-group-text">@indonesian-aerospace.com</span>
              </div> --}}
              <x-text-input id="email_register" class="form-control" type="email" name="email_register" required autocomplete="email" />
              <x-input-error :messages="$errors->get('email_register')" class="mt-2" />
            </div>
            <!-- Password -->
            <div class="mb-3">
              <x-input-label for="password_register" :value="__('Password')" />
              <x-text-input id="password_register" class="form-control" type="password" name="password_register" required autocomplete="new-password" />
              <x-input-error :messages="$errors->get('passwor_registerd')" class="mt-2" />
            </div>
            <!-- Confirm Password -->
            <div class="mb-3">
              {{-- Password Confirmation
              <div class="input-group has-validation">
                <input type="password" class="form-control" placeholder="password" aria-label="Password">
              </div> --}}
              <x-input-label for="password_register_confirmation" :value="__('Confirm Password')" />
              <x-text-input id="password_register_confirmation" class="form-control" type="password" name="password_register_confirmation" required />
              <x-input-error :messages="$errors->get('password_register_confirmation')" class="mt-2" />
    
            </div>
            <!-- end of registration form -->
          </div>
          {{-- Button Registration --}}
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>
        @endguest

        @auth
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          {{-- Button Logout --}}
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Logout</button>
          </div>  
        </form>
        @endauth
      </div>
    </div>
  </div>
{{-- </form> --}}