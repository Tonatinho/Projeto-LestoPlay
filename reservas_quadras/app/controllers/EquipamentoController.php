<?php

require_once ROOT_PATH . "/app/models/Equipamento.php";

class EquipamentoController {
    private $equipamentoModel;

    public function __construct($db) {
        $this->equipamentoModel = new Equipamento($db);
    }

    public function gerenciarEquipamentos() {
        $equipamentos = $this->equipamentoModel->getAll();
        
        $acao = $_GET["acao"] ?? ";
        $equip_id = $_GET["id"] ?? null;
        $equipamento_editando = null;

        if ($acao === "editar" && $equip_id) {
            $equipamento_editando = $this->equipamentoModel->getById($equip_id);
        }

        include ROOT_PATH . "/app/views/admin/equipamentos/index.php";
    }

    public function processarEquipamento() {
        $acao = $_POST["acao"] ?? ";
        $mensagem = ";
        $tipo_mensagem = ";

        switch ($acao) {
            case "adicionar_equipamento":
                $nome = trim($_POST["nome"]);
                $quantidade = (int)($_POST["quantidade"] ?? 0);

                if (!empty($nome) && $quantidade >= 0) {
                    if ($this->equipamentoModel->add($nome, $quantidade)) {
                        $mensagem = "Equipamento adicionado com sucesso!";
                        $tipo_mensagem = "success";
                    } else {
                        $mensagem = "Erro ao adicionar equipamento.";
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "Preencha todos os campos obrigatórios e a quantidade deve ser um número positivo.";
                    $tipo_mensagem = "error";
                }
                break;

            case "editar_equipamento":
                $id = (int)$_POST["id"];
                $nome = trim($_POST["nome"]);
                $quantidade = (int)($_POST["quantidade"] ?? 0);

                if ($id > 0 && !empty($nome) && $quantidade >= 0) {
                    if ($this->equipamentoModel->update($id, $nome, $quantidade)) {
                        $mensagem = "Equipamento atualizado com sucesso!";
                        $tipo_mensagem = "success";
                    } else {
                        $mensagem = "Erro ao atualizar equipamento.";
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "Dados inválidos para atualização.";
                    $tipo_mensagem = "error";
                }
                break;

            case "excluir_equipamento":
                $id = (int)$_POST["id"];

                if ($id > 0) {
                    // TODO: Verificar se há reservas futuras que dependem deste equipamento
                    // Por enquanto, apenas exclui
                    if ($this->equipamentoModel->delete($id)) {
                        $mensagem = "Equipamento excluído com sucesso!";
                        $tipo_mensagem = "success";
                    } else {
                        $mensagem = "Erro ao excluir equipamento.";
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "ID do equipamento inválido.";
                    $tipo_mensagem = "error";
                }
                break;
        }
        
        // Redirecionar de volta para a página de gerenciamento com a mensagem
        $_SESSION["mensagem"] = $mensagem;
        $_SESSION["tipo_mensagem"] = $tipo_mensagem;
        header("Location: admin.php?acao=equipamentos");
        exit();
    }
}


