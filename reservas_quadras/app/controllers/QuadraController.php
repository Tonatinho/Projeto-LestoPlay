<?php

require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/Quadra.php';

class QuadraController extends Controller {
    public function index() {
        try {
            $quadraModel = $this->loadModel('Quadra');
            $quadras = $quadraModel->getAll();
            $this->render('quadras/index', ['quadras' => $quadras]);
        } catch (Exception $e) {
            $this->render('error', ['message' => 'Erro ao carregar quadras: ' . $e->getMessage()]);
        }
    }
}


