<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfoLine - Məktəb Məlumatları</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <?php if ($_SESSION['role'] === 'school_admin' && isset($_SESSION['school_name'])): ?>
                    <?php echo htmlspecialchars($_SESSION['school_name']); ?>
                <?php else: ?>
                    <i class="fas fa-chart-line"></i> InfoLine
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : ''; ?>" href="/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <?php if($_SESSION['role'] === 'super_admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/settings' ? 'active' : ''; ?>" href="/settings">
                            <i class="fas fa-cog"></i> Ayarlar
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/profile">
                                    <i class="fas fa-user"></i> Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/logout">
                                    <i class="fas fa-sign-out-alt"></i> Çıxış
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if(isset($_SESSION['flash_message'])): ?>
    <div class="container">
        <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show">
            <?php echo $_SESSION['flash_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php 
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    endif; 
    ?>
    <?php endif; ?>