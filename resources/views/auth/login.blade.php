<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <!-- Logo və başlıq -->
                <div class="text-center mb-4">
                    <h4 class="card-title text-primary">İnfoLine</h4>
                    <p class="text-muted">Məktəb İdarəetmə Sistemi</p>
                </div>

                <!-- Error mesajları -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Login formu -->
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <!-- Məktəb axtarışı -->
                    <div class="form-group mb-3">
                        <label class="form-label">
                            <i class="fas fa-school"></i> Məktəb Axtarışı
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="schoolSearch" 
                               placeholder="Məktəbin adını daxil edin">
                        <div id="schoolResults" class="list-group mt-2 shadow-sm"></div>
                    </div>

                    <!-- Giriş növü seçimi -->
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="loginType" id="emailLogin" checked>
                        <label class="btn btn-outline-primary" for="emailLogin">
                            <i class="fas fa-envelope"></i> Email
                        </label>

                        <input type="radio" class="btn-check" name="loginType" id="usernameLogin">
                        <label class="btn btn-outline-primary" for="usernameLogin">
                            <i class="fas fa-user"></i> İstifadəçi adı
                        </label>
                    </div>

                    <!-- Email/İstifadəçi adı -->
                    <div class="form-group mb-3">
                        <label for="login" class="form-label" id="loginLabel">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="login" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Şifrə -->
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Şifrə
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Məni xatırla -->
                    <div class="form-check mb-3">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Məni xatırla
                        </label>
                    </div>

                    <!-- Daxil ol düyməsi -->
                    <button type="submit" class="btn btn-primary w-100" id="loginButton">
                        <i class="fas fa-sign-in-alt"></i> Daxil ol
                    </button>

                    <!-- Şifrəni unutmusunuz? -->
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="text-muted">
                            <small>Şifrəni unutmusunuz?</small>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const loginInput = document.getElementById('login');
    const loginLabel = document.getElementById('loginLabel');
    const emailLogin = document.getElementById('emailLogin');
    const usernameLogin = document.getElementById('usernameLogin');
    const schoolSearch = document.getElementById('schoolSearch');
    const schoolResults = document.getElementById('schoolResults');
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    // Login növünü dəyiş
    function updateLoginType(isEmail) {
        loginInput.type = isEmail ? 'email' : 'text';
        loginInput.name = isEmail ? 'email' : 'username';
        loginInput.placeholder = isEmail ? 'Email daxil edin' : 'İstifadəçi adını daxil edin';
        loginLabel.innerHTML = `<i class="fas fa-${isEmail ? 'envelope' : 'user'}"></i> ${isEmail ? 'Email' : 'İstifadəçi adı'}`;
    }

    emailLogin.addEventListener('change', () => updateLoginType(true));
    usernameLogin.addEventListener('change', () => updateLoginType(false));

    // Şifrəni göstər/gizlət
    togglePassword.addEventListener('click', function() {
        const type = password.type === 'password' ? 'text' : 'password';
        password.type = type;
        this.innerHTML = `<i class="fas fa-eye${type === 'password' ? '' : '-slash'}"></i>`;
    });

    // Məktəb axtarışı
    let searchTimeout;
    schoolSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        if (this.value.length < 3) {
            schoolResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/schools/search?query=${this.value}`);
                const schools = await response.json();
                
                schoolResults.innerHTML = schools.length ? schools.map(school => `
                    <button type="button" 
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-username="${school.admin_username}">
                        <span>${school.name}</span>
                        <small class="text-muted">${school.admin_username}</small>
                    </button>
                `).join('') : '<div class="list-group-item text-muted">Nəticə tapılmadı</div>';
            } catch (error) {
                console.error('Axtarış xətası:', error);
                schoolResults.innerHTML = '<div class="list-group-item text-danger">Xəta baş verdi</div>';
            }
        }, 300);
    });

    // Məktəb seçimi
    schoolResults.addEventListener('click', function(e) {
        const button = e.target.closest('button');
        if (button) {
            const username = button.dataset.username;
            usernameLogin.checked = true;
            updateLoginType(false);
            loginInput.value = username;
            schoolResults.innerHTML = '';
            schoolSearch.value = button.querySelector('span').textContent;
        }
    });

    // Click-dən kənar bağla
    document.addEventListener('click', function(e) {
        if (!schoolSearch.contains(e.target) && !schoolResults.contains(e.target)) {
            schoolResults.innerHTML = '';
        }
    });

    // Form submit
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yüklənir...';
    });
});
</script>
@endpush