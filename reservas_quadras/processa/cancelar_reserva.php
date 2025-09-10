<?php
require_once dirname(__DIR__, 2) . 
'/config.php';
require_once ROOT_PATH . 
'/includes/db.php';

// Iniciar sessão para mensagens e redirecionamento
session_start();

// Verificar se os parâmetros necessários foram fornecidos
$reserva_id = $_GET["id"] ?? null;
$email = $_GET["email"] ?? null;

if (!$reserva_id || !$email) {
    $_SESSION["mensagem"] = "Parâmetros inválidos para cancelamento.";
    $_SESSION["tipo_mensagem"] = "error";
    header("Location: ../minhas_reservas.php");
    exit;
}

try {
    $pdo = conectarDB();
    
    // Buscar a reserva para verificar se existe e pertence ao email
    $stmt = $pdo->prepare("
        SELECT r.IDRESERVA, r.DATA, r.HORARIO, r.STATUS, c.EMAIL 
        FROM RESERVAS r 
        JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE 
        WHERE r.IDRESERVA = :id AND c.EMAIL = :email AND r.STATUS = 'ativa'
    ");
    $stmt->execute([":id" => $reserva_id, ":email" => $email]);
    $reserva = $stmt->fetch();
    
    if (!$reserva) {
        $_SESSION["mensagem"] = "Reserva não encontrada, já cancelada ou não pertence a este email.";
        $_SESSION["tipo_mensagem"] = "error";
        header("Location: ../minhas_reservas.php");
        exit;
    }
    
    // Verificar se a reserva pode ser cancelada (pelo menos 2 horas antes)
    $data_hora_reserva_str = $reserva["DATA"] . " " . $reserva["HORARIO"];
    $data_hora_reserva = new DateTime($data_hora_reserva_str);
    $agora = new DateTime();
    
    // Se a reserva já passou ou está muito próxima (menos de 2 horas)
    if ($data_hora_reserva <= $agora || $agora->diff($data_hora_reserva)->h < 2) {
        $_SESSION["mensagem"] = "Não é possível cancelar esta reserva. O prazo mínimo de 2 horas antes do horário já expirou.";
        $_SESSION["tipo_mensagem"] = "error";
        header("Location: ../minhas_reservas.php");
        exit;
    }
    
    // Cancelar a reserva
    $stmt = $pdo->prepare("UPDATE RESERVAS SET STATUS = 'cancelada' WHERE IDRESERVA = :id");
    $resultado = $stmt->execute([":id" => $reserva_id]);
    
    if ($resultado) {
        $_SESSION["mensagem"] = "Reserva cancelada com sucesso!";
        $_SESSION["tipo_mensagem"] = "success";
    } else {
        $_SESSION["mensagem"] = "Erro ao cancelar reserva. Tente novamente.";
        $_SESSION["tipo_mensagem"] = "error";
    }
    
} catch (Exception $e) {
    $_SESSION["mensagem"] = "Erro interno ao cancelar reserva: " . $e->getMessage();
    $_SESSION["tipo_mensagem"] = "error";
    error_log("Erro ao cancelar reserva {$reserva_id}: " . $e->getMessage());
}

// Redirecionar de volta para minhas reservas
header("Location: ../minhas_reservas.php");
exit;
?>

