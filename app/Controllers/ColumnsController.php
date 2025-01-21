<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Column;

class ColumnsController extends Controller {
    private $columnModel;

    public function __construct() {
        parent::__construct();
        $this->columnModel = new Column();
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
            header('Location: /login');
            exit;
        }

        $columns = $this->columnModel->findAll();
        
        $this->view('columns/index', [
            'columns' => $columns
        ]);
    }
}