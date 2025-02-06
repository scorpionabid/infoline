<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>İnfoLine - Məktəb İdarəetmə Sistemi</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
</head>
<body>
    <?php if(auth()->guard()->check()): ?>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <?php echo e(config('app.name')); ?>

                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('settings')); ?>">
                                <i class="fas fa-cog"></i> Ayarlar
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo e(auth()->user()->name); ?>

                                <?php if(auth()->user()->school): ?>
                                    (<?php echo e(auth()->user()->school->name); ?>)
                                <?php elseif(auth()->user()->sector): ?>
                                    (<?php echo e(auth()->user()->sector->name); ?>)
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('profile')); ?>">
                                        <i class="fas fa-user-cog"></i> Profil
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
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/layouts/app.blade.php ENDPATH**/ ?>