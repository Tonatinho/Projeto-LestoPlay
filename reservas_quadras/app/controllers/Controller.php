<?php

class Controller {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function loadModel($modelName) {
        $path = ROOT_PATH . "/app/models/" . $modelName . ".php";
        if (file_exists($path)) {
            require_once $path;
            return new $modelName($this->db);
        } else {
            throw new Exception("Model " . $modelName . " not found.");
        }
    }

    protected function render($viewName, $data = []) {
        extract($data);
        $path = ROOT_PATH . "/app/views/" . $viewName . ".php";
        if (file_exists($path)) {
            require_once $path;
        } else {
            throw new Exception("View " . $viewName . " not found.");
        }
    }
}


