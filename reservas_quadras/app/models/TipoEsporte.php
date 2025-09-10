<?php

class TipoEsporte {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM TIPOESPORTE ORDER BY MODALIDADE");
        return $stmt->fetchAll();
    }
}


