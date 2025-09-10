_<?php

class Cliente {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM CLIENTE WHERE EMAIL = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($nome, $email, $senha, $telefone) {
        $stmt = $this->db->prepare("INSERT INTO CLIENTE (NOME, EMAIL, SENHA, TELEFONE) VALUES (:nome, :email, :senha, :telefone)");
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
            ':telefone' => $telefone
        ]);
        return $this->db->lastInsertId();
    }
}


