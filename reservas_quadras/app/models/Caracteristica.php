<?php

class Caracteristica {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM CARACTERISTICAS ORDER BY NOME");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM CARACTERISTICAS WHERE IDCARACTERISTICA = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($nome) {
        $stmt = $this->db->prepare("INSERT INTO CARACTERISTICAS (NOME) VALUES (:nome)");
        return $stmt->execute([':nome' => $nome]);
    }

    public function update($id, $nome) {
        $stmt = $this->db->prepare("UPDATE CARACTERISTICAS SET NOME = :nome WHERE IDCARACTERISTICA = :id");
        return $stmt->execute([':nome' => $nome, ':id' => $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM CARACTERISTICAS WHERE IDCARACTERISTICA = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getByQuadra($idQuadra) {
        $stmt = $this->db->prepare("
            SELECT c.IDCARACTERISTICA, c.NOME 
            FROM CARACTERISTICAS c
            JOIN QUADRA_CARACTERISTICAS qc ON c.IDCARACTERISTICA = qc.IDCARACTERISTICA
            WHERE qc.IDQUADRA = :idQuadra
            ORDER BY c.NOME
        ");
        $stmt->bindParam(":idQuadra", $idQuadra, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function associateWithQuadra($idQuadra, $caracteristicasIds) {
        // Primeiro, remove todas as associações existentes para esta quadra
        $stmt = $this->db->prepare("DELETE FROM QUADRA_CARACTERISTICAS WHERE IDQUADRA = :idQuadra");
        $stmt->bindParam(":idQuadra", $idQuadra, PDO::PARAM_INT);
        $stmt->execute();

        // Em seguida, insere as novas associações
        if (!empty($caracteristicasIds)) {
            $sql = "INSERT INTO QUADRA_CARACTERISTICAS (IDQUADRA, IDCARACTERISTICA) VALUES ";
            $values = [];
            foreach ($caracteristicasIds as $idCaracteristica) {
                $values[] = "({$idQuadra}, {$idCaracteristica})";
            }
            $sql .= implode(", ", $values);
            return $this->db->exec($sql);
        }
        return true;
    }
}

