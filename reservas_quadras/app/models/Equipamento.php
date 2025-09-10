<?php

class Equipamento {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM EQUIPAMENTO ORDER BY NOME");
        return $stmt->fetchAll();
    }
}


