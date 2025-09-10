<?php

class Quadra {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM quadras ORDER BY nome");
        return $stmt->fetchAll();
    }
}


