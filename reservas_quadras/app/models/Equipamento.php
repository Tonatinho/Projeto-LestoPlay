<?php

class Equipamento {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM EQUIPAMENTO ORDER BY NOME");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM EQUIPAMENTO WHERE IDEQUIP = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($nome, $quantidade) {
        $stmt = $this->db->prepare("INSERT INTO EQUIPAMENTO (NOME, QUANTIDADE) VALUES (:nome, :quantidade)");
        return $stmt->execute([':nome' => $nome, ':quantidade' => $quantidade]);
    }

    public function update($id, $nome, $quantidade) {
        $stmt = $this->db->prepare("UPDATE EQUIPAMENTO SET NOME = :nome, QUANTIDADE = :quantidade WHERE IDEQUIP = :id");
        return $stmt->execute([':nome' => $nome, ':quantidade' => $quantidade, ':id' => $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM EQUIPAMENTO WHERE IDEQUIP = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateQuantity($id, $change) {
        $stmt = $this->db->prepare("UPDATE EQUIPAMENTO SET QUANTIDADE = QUANTIDADE + :change WHERE IDEQUIP = :id");
        return $stmt->execute([':change' => $change, ':id' => $id]);
    }
}


