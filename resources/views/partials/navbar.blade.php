<nav class="navbar navbar-expand-lg navbar-light bg-light">
   <div class="container-fluid">
       <!-- Brand -->
       <a class="navbar-brand" href="{{ route('dashboard.index') }}">
           {{ config('app.name') }}
       </a>

       <!-- Left Side -->
       <div class="navbar-nav me-auto">
           <!-- Dashboard -->
           <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" 
              href="{{ route('dashboard.index') }}">
               <i class="fas fa-home"></i> Dashboard
           </a>
           
           <!-- Settings Dropdown -->
           @if(auth()->user()->hasRole('super'))
           <div class="nav-item dropdown">
               <a class="nav-link dropdown-toggle {{ request()->routeIs('settings.*') ? 'active' : '' }}" 
                  href="#" role="button" data-bs-toggle="dropdown">
                   <i class="fas fa-cog"></i> Ayarlar
               </a>
               <ul class="dropdown-menu">
                   <li>
                       <a class="dropdown-item {{ request()->routeIs('settings.table.*') ? 'active' : '' }}" 
                          href="{{ route('settings.table.index') }}">
                           <i class="fas fa-table"></i> Cədvəl Ayarları
                       </a>
                   </li>
                   <li>
                       <a class="dropdown-item {{ request()->routeIs('settings.personal.*') ? 'active' : '' }}" 
                          href="{{ route('settings.personal.index') }}">
                           <i class="fas fa-users"></i> Personal
                       </a>
                   </li>
               </ul>
           </div>
           @endif
       </div>

       <!-- Center - User Context -->
       <div class="navbar-text text-center mx-auto">
           @switch(true)
               @case(auth()->user()->hasRole('super'))
                   <i class="fas fa-globe"></i> 
                   {{ auth()->user()->region->name ?? 'Bütün regionlar' }}
                   @break
               @case(auth()->user()->hasRole('sector'))  
                   <i class="fas fa-building"></i>
                   {{ auth()->user()->sector->name ?? 'Sektor' }}
                   @break
               @case(auth()->user()->hasRole('school'))
                   <i class="fas fa-school"></i>
                   {{ auth()->user()->school->name ?? 'Məktəb' }}
                   @break
           @endswitch
       </div>

       <!-- Right Side -->
       <div class="navbar-nav ms-auto">
           <div class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                   <i class="fas fa-user-circle"></i> 
                   {{ auth()->user()->name }}
               </a>
               <ul class="dropdown-menu dropdown-menu-end">
                   <li>
                       <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                          href="{{ route('profile.index') }}">
                           <i class="fas fa-user"></i> Profil
                       </a>
                   </li>
                   <li><hr class="dropdown-divider"></li>
                   <li>
                       <form method="POST" action="{{ route('logout') }}">
                           @csrf
                           <button type="submit" class="dropdown-item text-danger">
                               <i class="fas fa-sign-out-alt"></i> Çıxış
                           </button>
                       </form>
                   </li>
               </ul>
           </div>
       </div>
   </div>
</nav>