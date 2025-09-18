<?php

class Avaliacao {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($idCliente, $idQuadra, $idReserva, $nota, $comentario) {
        $stmt = $this->db->prepare("
            INSERT INTO AVALIACOES (IDCLIENTE, IDQUADRA, IDRESERVA, NOTA, COMENTARIO) 
            VALUES (:idCliente, :idQuadra, :idReserva, :nota, :comentario)
        ");
        
        return $stmt->execute([
            ':idCliente' => $idCliente,
            ':idQuadra' => $idQuadra,
            ':idReserva' => $idReserva,
            ':nota' => $nota,
            ':comentario' => $comentario
        ]);
    }

    public function getByQuadra($idQuadra) {
        $stmt = $this->db->prepare("
            SELECT a.*, c.NOME as nome_cliente 
            FROM AVALIACOES a 
            JOIN CLIENTE c ON a.IDCLIENTE = c.IDCLIENTE 
            WHERE a.IDQUADRA = :idQuadra 
            ORDER BY a.DATA_AVALIACAO DESC
        ");
        $stmt->bindParam(':idQuadra', $idQuadra);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMediaAvaliacoes($idQuadra) {
        $stmt = $this->db->prepare("
            SELECT AVG(NOTA) as media, COUNT(*) as total 
            FROM AVALIACOES 
            WHERE IDQUADRA = :idQuadra
        ");
        $stmt->bindParam(':idQuadra', $idQuadra);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function verificarPodeAvaliar($idCliente, $idReserva) {
        // Verifica se a reserva existe, pertence ao cliente e já foi realizada
        $stmt = $this->db->prepare("
            SELECT r.IDRESERVA 
            FROM RESERVAS r 
            WHERE r.IDRESERVA = :idReserva 
            AND r.IDCLIENTE = :idCliente 
            AND r.STATUS = 'ativa'
            AND CONCAT(r.DATA, ' ', r.HORARIO) < NOW()
            AND NOT EXISTS (
                SELECT 1 FROM AVALIACOES a WHERE a.IDRESERVA = :idReserva
            )
        ");
        
        $stmt->execute([
            ':idReserva' => $idReserva,
            ':idCliente' => $idCliente
        ]);
        
        return $stmt->fetch() !== false;
    }
}

