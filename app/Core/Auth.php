<?php
namespace App\Core;

use App\Models\User;

class Auth {
    private $user = null;
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
        $this->checkSession();
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->user = $this->userModel->getById($_SESSION['user_id']);
            if ($this->user) {
                // Convert role names to match our expectations
                if ($this->user['role'] === 'superadmin') {
                    $this->user['role'] = 'super_admin';
                    $_SESSION['role'] = 'super_admin';
                } else {
                    $_SESSION['role'] = $this->user['role'];
                }
                
                if ($this->user['role'] === 'school_admin') {
                    // Get school_id for school admin
                    $school = $this->userModel->getSchoolByAdminId($this->user['id']);
                    if ($school) {
                        $this->user['school_id'] = $school['id'];
                        $_SESSION['school_id'] = $school['id'];
                        $_SESSION['school_name'] = $school['name'];
                    }
                }
            }
        }
    }

    public function attempt($username, $password) {
        $user = $this->userModel->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $this->user = $user;
            
            // Convert role names to match our expectations
            if ($this->user['role'] === 'superadmin') {
                $this->user['role'] = 'super_admin';
                $_SESSION['role'] = 'super_admin';
            } else {
                $_SESSION['role'] = $this->user['role'];
            }
            
            if ($this->user['role'] === 'school_admin') {
                // Get school_id for school admin
                $school = $this->userModel->getSchoolByAdminId($this->user['id']);
                if ($school) {
                    $this->user['school_id'] = $school['id'];
                    $_SESSION['school_id'] = $school['id'];
                    $_SESSION['school_name'] = $school['name'];
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function check() {
        return $this->user !== null;
    }

    public function user() {
        return $this->user;
    }

    public function logout() {
        session_destroy();
        $this->user = null;
    }

    public function isSuperAdmin() {
        return $this->user && $this->user['role'] === 'super_admin';
    }

    public function isSchoolAdmin() {
        return $this->user && $this->user['role'] === 'school_admin';
    }
}