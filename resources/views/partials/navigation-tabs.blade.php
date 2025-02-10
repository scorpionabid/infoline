// resources/views/partials/navigation-tabs.blade.php

<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}" 
                   href="{{ route('dashboard') }}">
                    Dashboard
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('settings*') ? 'active' : '' }}" 
                   data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    Ayarlar
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('settings.table') ? 'active' : '' }}" 
                           href="{{ route('settings.table') }}">
                            Cədvəl
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('settings.personal') ? 'active' : '' }}" 
                           href="{{ route('settings.personal') }}">
                            Personal
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
