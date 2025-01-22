<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Əgər artıq login olubsa, dashboard-a yönləndir
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                return $this->view('auth/login', [
                    'error' => 'İstifadəçi adı və şifrə tələb olunur',
                    'username' => $username
                ]);
            }

            $user = $this->userModel->findByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_active']) {
                    return $this->view('auth/login', [
                        'error' => 'Hesabınız deaktiv edilib',
                        'username' => $username
                    ]);
                }

                // Session-u təmizlə və yenidən başlat
                session_unset();
                session_destroy();
                session_start();
                session_regenerate_id(true);

                // Session məlumatlarını təyin et
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();

                // Uğurlu login mesajı
                $_SESSION['flash_message'] = 'Sistemə uğurla daxil oldunuz';
                $_SESSION['flash_type'] = 'success';
                
                // Rola görə yönləndirmə
                if ($user['role'] === 'super_admin') {
                    header('Location: /dashboard');
                } else if ($user['role'] === 'school_admin') {
                    header('Location: /dashboard');
                } else {
                    header('Location: /dashboard');
                }
                exit;
            } else {
                return $this->view('auth/login', [
                    'error' => 'İstifadəçi adı və ya şifrə səhvdir',
                    'username' => $username
                ]);
            }
        }

        // GET sorğusu üçün login səhifəsini göstər
        return $this->view('auth/login');
    }

    public function logout() {
        // Session-u təmizlə
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
        $this->redirect('/login');
    }

    // Session timeout yoxlaması
    private function checkSessionTimeout() {
        $timeout = 30 * 60; // 30 dəqiqə

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }
}