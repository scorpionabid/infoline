<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("Profile Controller - Session data: " . print_r($_SESSION, true));

        if (!isset($_SESSION['user_id'])) {
            error_log("Profile Controller - No user_id in session");
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        error_log("Profile Controller - User ID: " . $userId);
        
        $user = $this->userModel->getById($userId);
        error_log("Profile Controller - User data: " . print_r($user, true));

        if (!$user) {
            error_log("Profile Controller - User not found in database");
            header('Location: /login');
            exit;
        }

        return $this->view('profile/index', [
            'user' => $user,
            'title' => 'Profil Səhifəsi'
        ]);
    }
}