<?php

require_once dirname(__DIR__, 2) . 
'/config.php';
require_once ROOT_PATH . 
'/app/controllers/Controller.php';
require_once ROOT_PATH . 
'/app/models/Caracteristica.php';

class CaracteristicaController extends Controller {
    public function __construct($db) {
        parent::__construct($db);
        $this->caracteristicaModel = $this->loadModel('Caracteristica');
    }

    public function index() {
        $mensagem = '';
        $tipo_mensagem = '';
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? '';
            $nome = trim($_POST['nome'] ?? '');
            $id = (int)($_POST['id'] ?? 0);

            if (empty($nome)) {
                $erros[] = "O nome da característica é obrigatório.";
            }

            if (empty($erros)) {
                try {
                    if ($acao === 'adicionar') {
                        $this->caracteristicaModel->create($nome);
                        $mensagem = "Característica adicionada com sucesso!";
                        $tipo_mensagem = "success";
                    } elseif ($acao === 'editar') {
                        $this->caracteristicaModel->update($id, $nome);
                        $mensagem = "Característica atualizada com sucesso!";
                        $tipo_mensagem = "success";
                    } elseif ($acao === 'excluir') {
                        $this->caracteristicaModel->delete($id);
                        $mensagem = "Característica excluída com sucesso!";
                        $tipo_mensagem = "success";
                    }
                } catch (Exception $e) {
                    $erros[] = "Erro ao processar característica: " . $e->getMessage();
                }
            }

            if (!empty($erros)) {
                $mensagem = implode('<br>', $erros);
                $tipo_mensagem = "error";
            }
        }

        $caracteristicas = $this->caracteristicaModel->getAll();

        $this->render('admin/caracteristicas', [
            'caracteristicas' => $caracteristicas,
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'erros' => $erros
        ]);
    }
}

