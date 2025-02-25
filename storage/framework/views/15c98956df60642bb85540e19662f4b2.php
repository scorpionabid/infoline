<nav class="navbar navbar-expand-lg navbar-light bg-light">
   <div class="container-fluid">
       <!-- Brand -->
       <a class="navbar-brand" href="<?php echo e(route('dashboard.index')); ?>">
           <?php echo e(config('app.name')); ?>

       </a>

       <!-- Left Side -->
       <div class="navbar-nav me-auto">
           <!-- Dashboard -->
           <a class="nav-link <?php echo e(request()->routeIs('dashboard.*') ? 'active' : ''); ?>" 
              href="<?php echo e(route('dashboard.index')); ?>">
               <i class="fas fa-home"></i> Dashboard
           </a>
           
           <!-- Settings Dropdown -->
           <?php if(auth()->user()->hasRole('super')): ?>
           <div class="nav-item dropdown">
               <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>" 
                  href="#" role="button" data-bs-toggle="dropdown">
                   <i class="fas fa-cog"></i> Ayarlar
               </a>
               <ul class="dropdown-menu">
                   <li>
                       <a class="dropdown-item <?php echo e(request()->routeIs('settings.table.*') ? 'active' : ''); ?>" 
                          href="<?php echo e(route('settings.table.index')); ?>">
                           <i class="fas fa-table"></i> Cədvəl Ayarları
                       </a>
                   </li>
                   <li>
                       <a class="dropdown-item <?php echo e(request()->routeIs('settings.personal.*') ? 'active' : ''); ?>" 
                          href="<?php echo e(route('settings.personal.index')); ?>">
                           <i class="fas fa-users"></i> Personal
                       </a>
                   </li>
               </ul>
           </div>
           <?php endif; ?>
       </div>

       <!-- Center - User Context -->
       <div class="navbar-text text-center mx-auto">
           <?php switch(true):
               case (auth()->user()->hasRole('super')): ?>
                   <i class="fas fa-globe"></i> 
                   <?php echo e(auth()->user()->region->name ?? 'Bütün regionlar'); ?>

                   <?php break; ?>
               <?php case (auth()->user()->hasRole('sector')): ?>  
                   <i class="fas fa-building"></i>
                   <?php echo e(auth()->user()->sector->name ?? 'Sektor'); ?>

                   <?php break; ?>
               <?php case (auth()->user()->hasRole('school')): ?>
                   <i class="fas fa-school"></i>
                   <?php echo e(auth()->user()->school->name ?? 'Məktəb'); ?>

                   <?php break; ?>
           <?php endswitch; ?>
       </div>

       <!-- Right Side -->
       <div class="navbar-nav ms-auto">
           <div class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                   <i class="fas fa-user-circle"></i> 
                   <?php echo e(auth()->user()->name); ?>

               </a>
               <ul class="dropdown-menu dropdown-menu-end">
                   <li>
                       <a class="dropdown-item <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>"
                          href="<?php echo e(route('profile.index')); ?>">
                           <i class="fas fa-user"></i> Profil
                       </a>
                   </li>
                   <li><hr class="dropdown-divider"></li>
                   <li>
                       <form method="POST" action="<?php echo e(route('logout')); ?>">
                           <?php echo csrf_field(); ?>
                           <button type="submit" class="dropdown-item text-danger">
                               <i class="fas fa-sign-out-alt"></i> Çıxış
                           </button>
                       </form>
                   </li>
               </ul>
           </div>
       </div>
   </div>
</nav><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/partials/navbar.blade.php ENDPATH**/ ?>