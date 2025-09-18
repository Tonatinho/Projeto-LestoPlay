<?php

class Quadra {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT q.id, q.nome, q.localizacao, q.preco_hora,
                   COALESCE(AVG(a.NOTA), 0) as media_avaliacao,
                   COUNT(a.IDAVALIACAO) as total_avaliacoes
            FROM quadras q
            LEFT JOIN AVALIACOES a ON q.id = a.IDQUADRA
            GROUP BY q.id, q.nome, q.localizacao, q.preco_hora
            ORDER BY q.nome
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, nome, localizacao, preco_hora FROM quadras WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}

