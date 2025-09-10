<?php

class Reserva {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function verificarDisponibilidade($id_quadra, $data, $horario, $id_reserva = null) {
        $sql = "SELECT COUNT(*) as total FROM RESERVAS 
                WHERE IDQUADRA = :id_quadra 
                AND DATA = :data 
                AND STATUS = 'ativa'
                AND HORARIO = :horario";
        
        if ($id_reserva) {
            $sql .= " AND IDRESERVA != :id_reserva";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_quadra', $id_quadra);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':horario', $horario);
        
        if ($id_reserva) {
            $stmt->bindParam(':id_reserva', $id_reserva);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch();
        
        return $resultado['total'] == 0;
    }

    public function create($id_cliente, $id_quadra, $id_esporte, $id_equip, $data, $horario, $preco) {
        $stmt = $this->db->prepare("
            INSERT INTO RESERVAS (IDCLIENTE, IDQUADRA, IDESPORTE, IDEQUIP, DATA, HORARIO, PRECO, STATUS) 
            VALUES (:id_cliente, :id_quadra, :id_esporte, :id_equip, :data, :horario, :preco, 'ativa')
        ");

        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':id_quadra' => $id_quadra,
            ':id_esporte' => $id_esporte,
            ':id_equip' => $id_equip,
            ':data' => $data,
            ':horario' => $horario,
            ':preco' => $preco
        ]);

        return $this->db->lastInsertId();
    }
}


