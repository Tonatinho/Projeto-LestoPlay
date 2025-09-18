<?php

require_once dirname(__DIR__, 2) . '/config.php';
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/Avaliacao.php';
require_once ROOT_PATH . '/app/models/Quadra.php';
require_once ROOT_PATH . '/app/models/Reserva.php';

class AvaliacaoController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
        $this->avaliacaoModel = $this->loadModel('Avaliacao');
        $this->quadraModel = $this->loadModel('Quadra');
        $this->reservaModel = $this->loadModel('Reserva');
    }

    public function index() {
        $idQuadra = isset($_GET['quadra']) ? (int)$_GET['quadra'] : null;
        $mensagem = '';
        $tipo_mensagem = '';

        if (!$idQuadra) {
            header('Location: index.php');
            exit;
        }

        try {
            $quadra = $this->quadraModel->getById($idQuadra);
            if (!$quadra) {
                throw new Exception("Quadra não encontrada.");
            }

            $avaliacoes = $this->avaliacaoModel->getByQuadra($idQuadra);
            $mediaAvaliacoes = $this->avaliacaoModel->getMediaAvaliacoes($idQuadra);

        } catch (Exception $e) {
            $mensagem = "Erro ao carregar avaliações: " . $e->getMessage();
            $tipo_mensagem = "error";
            $avaliacoes = [];
            $mediaAvaliacoes = ['media' => 0, 'total' => 0];
        }

        $this->render('avaliacoes/index', [
            'quadra' => $quadra,
            'avaliacoes' => $avaliacoes,
            'mediaAvaliacoes' => $mediaAvaliacoes,
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem
        ]);
    }

    public function criar() {
        $idReserva = isset($_GET['reserva']) ? (int)$_GET['reserva'] : null;
        $mensagem = '';
        $tipo_mensagem = '';
        $erros = [];

        if (!$idReserva) {
            header('Location: minhas_reservas.php');
            exit;
        }

        // Simular cliente logado (em um sistema real, viria da sessão)
        $idCliente = 1; // Temporário para demonstração

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nota = (int)$_POST['nota'];
            $comentario = trim($_POST['comentario']);

            // Validações
            if ($nota < 1 || $nota > 5) {
                $erros[] = "Nota deve estar entre 1 e 5";
            }
            if (empty($comentario)) {
                $erros[] = "Comentário é obrigatório";
            }

            // Verificar se pode avaliar
            if (empty($erros) && !$this->avaliacaoModel->verificarPodeAvaliar($idCliente, $idReserva)) {
                $erros[] = "Você não pode avaliar esta reserva";
            }

            if (empty($erros)) {
                try {
                    // Buscar dados da reserva
                    $reserva = $this->reservaModel->getById($idReserva);
                    
                    $resultado = $this->avaliacaoModel->create(
                        $idCliente, 
                        $reserva['IDQUADRA'], 
                        $idReserva, 
                        $nota, 
                        $comentario
                    );

                    if ($resultado) {
                        $mensagem = "Avaliação enviada com sucesso!";
                        $tipo_mensagem = "success";
                        
                        // Redirecionar após sucesso
                        header('Location: minhas_reservas.php?msg=avaliacao_enviada');
                        exit;
                    } else {
                        $erros[] = "Erro ao salvar avaliação";
                    }

                } catch (Exception $e) {
                    $erros[] = "Erro ao salvar avaliação: " . $e->getMessage();
                }
            }

            if (!empty($erros)) {
                $mensagem = implode('<br>', $erros);
                $tipo_mensagem = "error";
            }
        }

        try {
            $reserva = $this->reservaModel->getById($idReserva);
            if (!$reserva) {
                throw new Exception("Reserva não encontrada.");
            }

            $quadra = $this->quadraModel->getById($reserva['IDQUADRA']);

        } catch (Exception $e) {
            header('Location: minhas_reservas.php');
            exit;
        }

        $this->render('avaliacoes/criar', [
            'reserva' => $reserva,
            'quadra' => $quadra,
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'nota' => $_POST['nota'] ?? '',
            'comentario' => $_POST['comentario'] ?? ''
        ]);
    }
}

