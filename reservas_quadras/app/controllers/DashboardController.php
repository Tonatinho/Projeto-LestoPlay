<?php

require_once dirname(__DIR__, 2) . '/config.php';
require_once ROOT_PATH . '/app/controllers/Controller.php';

class DashboardController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
    }

    public function index() {
        try {
            // Estatísticas gerais
            $totalReservas = $this->getTotalReservas();
            $receitaTotal = $this->getReceitaTotal();
            $totalClientes = $this->getTotalClientes();
            $mediaAvaliacoes = $this->getMediaGeralAvaliacoes();

            // Dados para gráficos
            $reservasPorMes = $this->getReservasPorMes();
            $quadrasMaisPopulares = $this->getQuadrasMaisPopulares();
            $receitaPorMes = $this->getReceitaPorMes();
            $horariosPico = $this->getHorariosPico();

        } catch (Exception $e) {
            $totalReservas = 0;
            $receitaTotal = 0;
            $totalClientes = 0;
            $mediaAvaliacoes = 0;
            $reservasPorMes = [];
            $quadrasMaisPopulares = [];
            $receitaPorMes = [];
            $horariosPico = [];
        }

        $this->render('dashboard/index', [
            'totalReservas' => $totalReservas,
            'receitaTotal' => $receitaTotal,
            'totalClientes' => $totalClientes,
            'mediaAvaliacoes' => $mediaAvaliacoes,
            'reservasPorMes' => $reservasPorMes,
            'quadrasMaisPopulares' => $quadrasMaisPopulares,
            'receitaPorMes' => $receitaPorMes,
            'horariosPico' => $horariosPico
        ]);
    }

    private function getTotalReservas() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM RESERVAS WHERE STATUS = 'ativa'");
        $result = $stmt->fetch();
        return $result['total'];
    }

    private function getReceitaTotal() {
        $stmt = $this->db->query("SELECT SUM(PRECO) as total FROM RESERVAS WHERE STATUS = 'ativa'");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    private function getTotalClientes() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM CLIENTE");
        $result = $stmt->fetch();
        return $result['total'];
    }

    private function getMediaGeralAvaliacoes() {
        $stmt = $this->db->query("SELECT AVG(NOTA) as media FROM AVALIACOES");
        $result = $stmt->fetch();
        return round($result['media'] ?? 0, 1);
    }

    private function getReservasPorMes() {
        $stmt = $this->db->query("
            SELECT 
                DATE_FORMAT(DATA, '%Y-%m') as mes,
                COUNT(*) as total
            FROM RESERVAS 
            WHERE STATUS = 'ativa' 
            AND DATA >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(DATA, '%Y-%m')
            ORDER BY mes
        ");
        return $stmt->fetchAll();
    }

    private function getQuadrasMaisPopulares() {
        $stmt = $this->db->query("
            SELECT 
                q.nome,
                COUNT(r.IDRESERVA) as total_reservas
            FROM quadras q
            LEFT JOIN RESERVAS r ON q.id = r.IDQUADRA AND r.STATUS = 'ativa'
            GROUP BY q.id, q.nome
            ORDER BY total_reservas DESC
            LIMIT 5
        ");
        return $stmt->fetchAll();
    }

    private function getReceitaPorMes() {
        $stmt = $this->db->query("
            SELECT 
                DATE_FORMAT(DATA, '%Y-%m') as mes,
                SUM(PRECO) as receita
            FROM RESERVAS 
            WHERE STATUS = 'ativa' 
            AND DATA >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(DATA, '%Y-%m')
            ORDER BY mes
        ");
        return $stmt->fetchAll();
    }

    private function getHorariosPico() {
        $stmt = $this->db->query("
            SELECT 
                HOUR(HORARIO) as hora,
                COUNT(*) as total
            FROM RESERVAS 
            WHERE STATUS = 'ativa'
            GROUP BY HOUR(HORARIO)
            ORDER BY total DESC
            LIMIT 5
        ");
        return $stmt->fetchAll();
    }
}

