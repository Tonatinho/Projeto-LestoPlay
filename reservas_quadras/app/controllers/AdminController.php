<?php

require_once dirname(__DIR__, 2) . '/config.php';
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/Reserva.php';
require_once ROOT_PATH . '/app/models/Quadra.php';
require_once ROOT_PATH . '/app/models/TipoEsporte.php';
require_once ROOT_PATH . '/app/models/Equipamento.php';

class AdminController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
        $this->reservaModel = $this->loadModel('Reserva');
        $this->quadraModel = $this->loadModel('Quadra');
        $this->tipoEsporteModel = $this->loadModel('TipoEsporte');
        $this->equipamentoModel = $this->loadModel('Equipamento');
    }

    public function index() {
        // página de administração
        // Por enquanto carrega as reservas
        try {
            $reservas = $this->db->query("SELECT r.*, c.NOME as cliente_nome, q.nome as quadra_nome FROM RESERVAS r JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE JOIN quadras q ON r.IDQUADRA = q.id ORDER BY r.DATA DESC, r.HORARIO DESC")->fetchAll();
            $this->render('admin/index', ['reservas' => $reservas]);
        } catch (Exception $e) {
            $this->render('error', ['message' => 'Erro ao carregar reservas: ' . $e->getMessage()]);
        }
    }

    public function gerenciarQuadras() {
        try {
            $quadras = $this->quadraModel->getAll();
            $this->render('admin/gerenciar_quadras', ['quadras' => $quadras]);
        } catch (Exception $e) {
            $this->render('error', ['message' => 'Erro ao carregar quadras: ' . $e->getMessage()]);
        }
    }

    public function adicionarQuadra() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome']);
            $localizacao = trim($_POST['localizacao']);

            if (empty($nome) || empty($localizacao)) {
                $this->render('admin/adicionar_quadra', ['error' => 'Nome e localização são obrigatórios.']);
                return;
            }

            try {
                $stmt = $this->db->prepare("INSERT INTO quadras (nome, localizacao) VALUES (:nome, :localizacao)");
                $stmt->execute([':nome' => $nome, ':localizacao' => $localizacao]);
                header('Location: admin.php?action=gerenciarQuadras');
                exit;
            } catch (Exception $e) {
                $this->render('admin/adicionar_quadra', ['error' => 'Erro ao adicionar quadra: ' . $e->getMessage()]);
            }
        }
        $this->render('admin/adicionar_quadra');
    }

    public function editarQuadra() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            header('Location: admin.php?action=gerenciarQuadras');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome']);
            $localizacao = trim($_POST['localizacao']);

            if (empty($nome) || empty($localizacao)) {
                $this->render('admin/editar_quadra', ['error' => 'Nome e localização são obrigatórios.', 'quadra' => ['id' => $id, 'nome' => $nome, 'localizacao' => $localizacao]]);
                return;
            }

            try {
                $stmt = $this->db->prepare("UPDATE quadras SET nome = :nome, localizacao = :localizacao WHERE id = :id");
                $stmt->execute([':nome' => $nome, ':localizacao' => $localizacao, ':id' => $id]);
                header('Location: admin.php?action=gerenciarQuadras');
                exit;
            } catch (Exception $e) {
                $this->render('admin/editar_quadra', ['error' => 'Erro ao editar quadra: ' . $e->getMessage(), 'quadra' => ['id' => $id, 'nome' => $nome, 'localizacao' => $localizacao]]);
            }
        } else {
            try {
                $stmt = $this->db->prepare("SELECT * FROM quadras WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $quadra = $stmt->fetch();
                if (!$quadra) {
                    header('Location: admin.php?action=gerenciarQuadras');
                    exit;
                }
                $this->render('admin/editar_quadra', ['quadra' => $quadra]);
            } catch (Exception $e) {
                $this->render('error', ['message' => 'Erro ao carregar quadra: ' . $e->getMessage()]);
            }
        }
    }

    public function excluirQuadra() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            header('Location: admin.php?action=gerenciarQuadras');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM quadras WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: admin.php?action=gerenciarQuadras');
            exit;
        } catch (Exception $e) {
            $this->render('error', ['message' => 'Erro ao excluir quadra: ' . $e->getMessage()]);
        }
    }
}


