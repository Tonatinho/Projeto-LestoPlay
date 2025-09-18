<?php

require_once dirname(__DIR__, 2) . '/config.php';
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/Cliente.php';
require_once ROOT_PATH . '/app/models/Reserva.php';
require_once ROOT_PATH . '/app/models/Quadra.php';
require_once ROOT_PATH . '/app/models/TipoEsporte.php';
// A CORREÇÃO FOI FEITA AQUI: o ';' estava dentro das aspas
require_once ROOT_PATH . '/app/models/Equipamento.php';
require_once ROOT_PATH . '/app/models/WhatsAppNotification.php';

class ReservaController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
        $this->clienteModel = $this->loadModel('Cliente');
        $this->reservaModel = $this->loadModel('Reserva');
        $this->quadraModel = $this->loadModel('Quadra');
        $this->tipoEsporteModel = $this->loadModel('TipoEsporte');
        $this->equipamentoModel = $this->loadModel('Equipamento');
        $this->whatsappModel = new WhatsAppNotification($this->db);
    }

    public function index() {
        $quadra_selecionada = isset($_GET['quadra']) ? (int)$_GET['quadra'] : null;
        $mensagem = '';
        $tipo_mensagem = '';
        $erros = [];

        try {
            $quadras = $this->quadraModel->getAll();
            $tipos_esporte = $this->tipoEsporteModel->getAll();
            $equipamentos = $this->equipamentoModel->getAll();
        } catch (Exception $e) {
            $mensagem = "Erro ao carregar dados: " . $e->getMessage();
            $tipo_mensagem = "error";
            $quadras = [];
            $tipos_esporte = [];
            $equipamentos = [];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome_cliente = trim($_POST['nome_cliente']);
            $email_cliente = trim($_POST['email_cliente']);
            $telefone_cliente = trim($_POST['telefone_cliente']);
            $senha_cliente = trim($_POST['senha_cliente']);

            $id_quadra = (int)$_POST['id_quadra'];
            $id_esporte = (int)$_POST['id_esporte'];
            $id_equip = (int)$_POST['id_equip'];
            $data_reserva = $_POST['data_reserva'];
            $horario_reserva = $_POST['horario_reserva'];
            $quadra_info = $this->quadraModel->getById($id_quadra);
            if (!$quadra_info) {
                $erros[] = "Quadra selecionada não encontrada.";
            }
            $preco_reserva = $quadra_info['preco_hora'] ?? 0.0;

            // Validações de cliente
            if (empty($nome_cliente)) $erros[] = "Nome é obrigatório";
            if (empty($email_cliente) || !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) $erros[] = "Email válido é obrigatório";
            if (empty($telefone_cliente)) $erros[] = "Telefone é obrigatório";
            if (empty($senha_cliente)) $erros[] = "Senha é obrigatória";

            // Validações de reserva
            if (empty($id_quadra)) $erros[] = "Selecione uma quadra";
            if (empty($id_esporte)) $erros[] = "Selecione um tipo de esporte";
            if (empty($id_equip)) $erros[] = "Selecione um equipamento";
            if (empty($data_reserva)) $erros[] = "Data é obrigatória";
            if (empty($horario_reserva)) $erros[] = "Horário é obrigatório";


            // Validar data não pode ser no passado
            if ($data_reserva && $data_reserva < date('Y-m-d')) {
                $erros[] = "Data não pode ser no passado";
            }

            // Verificar disponibilidade
            if (empty($erros) && !$this->reservaModel->verificarDisponibilidade($id_quadra, $data_reserva, $horario_reserva)) {
                $erros[] = "Horário não disponível para esta quadra";
            }

            if (empty($erros)) {
                try {
                    $this->db->beginTransaction();

                    // 1. Verificar ou criar cliente
                    $cliente = $this->clienteModel->getByEmail($email_cliente);
                    $id_cliente = null;
                    if ($cliente) {
                        $id_cliente = $cliente['IDCLIENTE'];
                    } else {
                        $id_cliente = $this->clienteModel->create($nome_cliente, $email_cliente, $senha_cliente, $telefone_cliente);
                    }

                    // 2. Inserir reserva
                    $id_reserva = $this->reservaModel->create($id_cliente, $id_quadra, $id_esporte, $id_equip, $data_reserva, $horario_reserva, $preco_reserva);

                    $this->db->commit();

                    // Enviar notificação WhatsApp
                    $quadra_nome = $quadra_info['nome'];
                    $this->whatsappModel->enviarConfirmacaoReserva(
                        $telefone_cliente,
                        $nome_cliente,
                        $quadra_nome,
                        $data_reserva,
                        $horario_reserva,
                        $preco_reserva
                    );

                    $mensagem = "Reserva realizada com sucesso!";
                    $tipo_mensagem = "success";

                    session_start();
                    $_SESSION['mensagem'] = $mensagem;
                    $_SESSION['tipo_mensagem'] = $tipo_mensagem;
                    $_SESSION['detalhes_reserva'] = [
                        'id' => $id_reserva,
                        'quadra' => $this->quadraModel->getAll()[array_search($id_quadra, array_column($quadras, 'id'))]['nome'],
                        'data' => $data_reserva,
                        'horario' => $horario_reserva,
                        'preco' => $preco_reserva
                    ];

                    header('Location: sucesso_reserva.php');
                    exit;

                } catch (Exception $e) {
                    $this->db->rollBack();
                    $erros[] = "Erro ao salvar reserva: " . $e->getMessage();
                }
            }

            if (!empty($erros)) {
                $mensagem = implode('<br>', $erros);
                $tipo_mensagem = "error";
            }
        }

        $this->render('reservas/index', [
            'quadra_selecionada' => $quadra_selecionada,
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'erros' => $erros,
            'quadras' => $quadras,
            'tipos_esporte' => $tipos_esporte,
            'equipamentos' => $equipamentos,
            'nome_cliente' => $_POST['nome_cliente'] ?? '',
            'email_cliente' => $_POST['email_cliente'] ?? '',
            'telefone_cliente' => $_POST['telefone_cliente'] ?? '',
            'id_quadra' => $_POST['id_quadra'] ?? '',
            'id_esporte' => $_POST['id_esporte'] ?? '',
            'id_equip' => $_POST['id_equip'] ?? '',
            'data_reserva' => $_POST['data_reserva'] ?? '',
            'horario_reserva' => $_POST['horario_reserva'] ?? '',
            'preco_reserva' => $_POST['preco_reserva'] ?? '',
            'observacoes' => $_POST['observacoes'] ?? ''
        ]);
    }
}