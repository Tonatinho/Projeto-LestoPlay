<?php
require_once dirname(__DIR__, 2) . 
'/config.php';
require_once ROOT_PATH . 
'/includes/db.php';

// Iniciar sessão para mensagens e redirecionamento
session_start();

// Verificar se é uma requisição POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../reserva.php");
    exit;
}

// Receber dados do formulário
$nome_cliente = trim($_POST["nome_cliente"] ?? "");
$email_cliente = trim($_POST["email_cliente"] ?? "");
$telefone_cliente = trim($_POST["telefone_cliente"] ?? "");
$senha_cliente = trim($_POST["senha_cliente"] ?? "");

$id_quadra = (int)($_POST["id_quadra"] ?? 0);
$id_esporte = (int)($_POST["id_esporte"] ?? 0);
$id_equip = (int)($_POST["id_equip"] ?? 0);
$data_reserva = $_POST["data_reserva"] ?? "";
$horario_reserva = $_POST["horario_reserva"] ?? "";
$preco_reserva = (float)($_POST["preco_reserva"] ?? 0.0);

$erros = [];

// Validações de cliente
if (empty($nome_cliente)) {
    $erros[] = "Nome é obrigatório";
}
if (empty($email_cliente) || !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Email válido é obrigatório";
}
if (empty($telefone_cliente)) {
    $erros[] = "Telefone é obrigatório";
}
if (empty($senha_cliente)) {
    $erros[] = "Senha é obrigatória";
}

// Validações de reserva
if (empty($id_quadra)) {
    $erros[] = "Selecione uma quadra";
}
if (empty($id_esporte)) {
    $erros[] = "Selecione um tipo de esporte";
}
if (empty($id_equip)) {
    $erros[] = "Selecione um equipamento";
}
if (empty($data_reserva)) {
    $erros[] = "Data é obrigatória";
}
if (empty($horario_reserva)) {
    $erros[] = "Horário é obrigatório";
}
if ($preco_reserva <= 0) {
    $erros[] = "Preço da reserva deve ser maior que zero";
}

// Validar data não pode ser no passado
if ($data_reserva && $data_reserva < date("Y-m-d")) {
    $erros[] = "Data não pode ser no passado";
}

// Se não há erros até aqui, verificar disponibilidade no banco
if (empty($erros)) {
    try {
        $pdo = conectarDB();
        
        // Verificar se a quadra existe
        $stmt = $pdo->prepare("SELECT id FROM quadras WHERE id = :id");
        $stmt->bindParam(":id", $id_quadra);
        $stmt->execute();
        if (!$stmt->fetch()) {
            $erros[] = "Quadra não encontrada.";
        }

        // Verificar se o tipo de esporte existe
        $stmt = $pdo->prepare("SELECT IDESPORTE FROM TIPOESPORTE WHERE IDESPORTE = :id");
        $stmt->bindParam(":id", $id_esporte);
        $stmt->execute();
        if (!$stmt->fetch()) {
            $erros[] = "Tipo de esporte não encontrado.";
        }

        // Verificar se o equipamento existe
        $stmt = $pdo->prepare("SELECT IDEQUIP FROM EQUIPAMENTO WHERE IDEQUIP = :id");
        $stmt->bindParam(":id", $id_equip);
        $stmt->execute();
        if (!$stmt->fetch()) {
            $erros[] = "Equipamento não encontrado.";
        }

        // Verificar disponibilidade da quadra no horário
        if (!verificarDisponibilidade($id_quadra, $data_reserva, $horario_reserva)) {
            $erros[] = "Horário não disponível para esta quadra.";
        }

    } catch (Exception $e) {
        $erros[] = "Erro ao verificar dados: " . $e->getMessage();
    }
}

// Se ainda não há erros, salvar a reserva
if (empty($erros)) {
    try {
        $pdo->beginTransaction();

        // 1. Verificar ou criar cliente
        $stmt = $pdo->prepare("SELECT IDCLIENTE FROM CLIENTE WHERE EMAIL = :email");
        $stmt->bindParam(":email", $email_cliente);
        $stmt->execute();
        $cliente = $stmt->fetch();

        $id_cliente = null;
        if ($cliente) {
            $id_cliente = $cliente["IDCLIENTE"];
        } else {
            $stmt = $pdo->prepare("INSERT INTO CLIENTE (NOME, EMAIL, SENHA, TELEFONE) VALUES (:nome, :email, :senha, :telefone)");
            $stmt->execute([
                ":nome" => $nome_cliente,
                ":email" => $email_cliente,
                ":senha" => $senha_cliente,
                ":telefone" => $telefone_cliente
            ]);
            $id_cliente = $pdo->lastInsertId();
        }

        // 2. Inserir reserva
        $stmt = $pdo->prepare("
            INSERT INTO RESERVAS (IDCLIENTE, IDQUADRA, IDESPORTE, IDEQUIP, DATA, HORARIO, PRECO, STATUS) 
            VALUES (:id_cliente, :id_quadra, :id_esporte, :id_equip, :data, :horario, :preco, 'ativa')
        ");
        
        $resultado = $stmt->execute([
            ":id_cliente" => $id_cliente,
            ":id_quadra" => $id_quadra,
            ":id_esporte" => $id_esporte,
            ":id_equip" => $id_equip,
            ":data" => $data_reserva,
            ":horario" => $horario_reserva,
            ":preco" => $preco_reserva
        ]);
        
        if ($resultado) {
            $id_reserva = $pdo->lastInsertId();
            $pdo->commit();
            
            // Buscar nome da quadra para a página de sucesso
            $stmt_quadra = $pdo->prepare("SELECT nome FROM quadras WHERE id = :id_quadra");
            $stmt_quadra->bindParam(":id_quadra", $id_quadra);
            $stmt_quadra->execute();
            $quadra_nome = $stmt_quadra->fetchColumn();

            $_SESSION["mensagem"] = "Reserva realizada com sucesso!";
            $_SESSION["tipo_mensagem"] = "success";
            $_SESSION["detalhes_reserva"] = [
                "id" => $id_reserva,
                "quadra" => $quadra_nome,
                "data" => $data_reserva,
                "horario" => $horario_reserva,
                "preco" => $preco_reserva
            ];
            
            header("Location: ../sucesso_reserva.php");
            exit;
        } else {
            $erros[] = "Erro ao salvar reserva no banco de dados.";
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $erros[] = "Erro ao salvar reserva: " . $e->getMessage();
    }
}

// Se chegou até aqui, houve erros - redirecionar de volta
$_SESSION["mensagem"] = implode("<br>", $erros);
$_SESSION["tipo_mensagem"] = "error";
$_SESSION["dados_formulario"] = $_POST; // Manter dados preenchidos

header("Location: ../reserva.php");
exit;
?>

