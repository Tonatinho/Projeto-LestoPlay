<?php
require_once __DIR__ . '/config.php';
require_once ROOT_PATH . '/includes/db.php';

$titulo = 'Minhas Reservas';

$reservas = [];
$email_busca = '';

// Processar busca por email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_busca'])) {
    $email_busca = trim($_POST['email_busca']);
    
    if (!empty($email_busca)) {
        try {
            $pdo = conectarDB();
            $stmt = $pdo->prepare("
                SELECT r.*, q.nome as nome_quadra, c.NOME as nome_cliente, c.TELEFONE as telefone_cliente, te.MODALIDADE as nome_esporte, e.NOME as nome_equipamento
                FROM RESERVAS r 
                JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE
                JOIN quadras q ON r.IDQUADRA = q.id
                JOIN TIPOESPORTE te ON r.IDESPORTE = te.IDESPORTE
                JOIN EQUIPAMENTO e ON r.IDEQUIP = e.IDEQUIP
                WHERE c.EMAIL = :email 
                ORDER BY r.DATA DESC, r.HORARIO DESC
            ");
            $stmt->bindParam(':email', $email_busca);
            $stmt->execute();
            $reservas = $stmt->fetchAll();
            
            if (empty($reservas)) {
                $mensagem = "Nenhuma reserva encontrada para este email.";
                $tipo_mensagem = "info";
            }
        } catch (Exception $e) {
            $mensagem = "Erro ao buscar reservas: " . $e->getMessage();
            $tipo_mensagem = "error";
        }
    } else {
        $mensagem = "Por favor, informe um email para buscar.";
        $tipo_mensagem = "error";
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-list"></i> Minhas Reservas</h1>
    <p>Consulte e gerencie suas reservas</p>
</div>

<div class="busca-container">
    <form method="POST" class="busca-form">
        <div class="form-group">
            <label for="email_busca">Digite seu email para consultar suas reservas:</label>
            <div class="input-group">
                <input type="email" id="email_busca" name="email_busca" required 
                       value="<?php echo htmlspecialchars($email_busca); ?>"
                       placeholder="seu@email.com">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($reservas)): ?>
    <div class="reservas-container">
        <div class="reservas-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($reservas); ?></div>
                <div class="stat-label">Total de Reservas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($reservas, function($r) { return $r['STATUS'] == 'ativa'; })); ?></div>
                <div class="stat-label">Reservas Ativas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($reservas, function($r) { return $r['STATUS'] == 'cancelada'; })); ?></div>
                <div class="stat-label">Canceladas</div>
            </div>
        </div>
        
        <div class="reservas-lista">
            <?php foreach ($reservas as $reserva): 
                $valor_total = $reserva['PRECO']; // Preço já está na tabela RESERVAS
                $data_reserva = new DateTime($reserva['DATA']);
                $hoje = new DateTime();
                $pode_cancelar = $reserva['STATUS'] == 'ativa' && $data_reserva >= $hoje;
            ?>
                <div class="reserva-card <?php echo $reserva['STATUS']; ?>">
                    <div class="reserva-header">
                        <div class="reserva-info">
                            <h3><?php echo htmlspecialchars($reserva['nome_quadra']); ?></h3>
                            <span class="status status-<?php echo $reserva['STATUS']; ?>">
                                <i class="fas fa-<?php echo $reserva['STATUS'] == 'ativa' ? 'check-circle' : 'times-circle'; ?>"></i>
                                <?php echo ucfirst($reserva['STATUS']); ?>
                            </span>
                        </div>
                        <div class="reserva-valor">
                            <span class="valor">R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <div class="reserva-body">
                        <div class="reserva-detalhes">
                            <div class="detalhe">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($reserva['nome_cliente']); ?></span>
                            </div>
                            <div class="detalhe">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo formatarDataBR($reserva['DATA']); ?></span>
                            </div>
                            <div class="detalhe">
                                <i class="fas fa-clock"></i>
                                <span><?php echo formatarHora($reserva['HORARIO']); ?></span>
                            </div>
                            <div class="detalhe">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($reserva['telefone_cliente']); ?></span>
                            </div>
                            <div class="detalhe">
                                <i class="fas fa-futbol"></i>
                                <span><?php echo htmlspecialchars($reserva['nome_esporte']); ?></span>
                            </div>
                            <div class="detalhe">
                                <i class="fas fa-tennis-ball"></i>
                                <span><?php echo htmlspecialchars($reserva['nome_equipamento']); ?></span>
                            </div>
                        </div>
                        
                        <?php /* Campo OBSERVACOES não existe no novo esquema, removido */ ?>
                        
                        <div class="reserva-meta">
                            <small>Reserva ID: <?php echo htmlspecialchars($reserva['IDRESERVA']); ?></small>
                        </div>
                    </div>
                    
                    <?php if ($pode_cancelar): ?>
                        <div class="reserva-actions">
                            <a href="processa/cancelar_reserva.php?id=<?php echo $reserva['IDRESERVA']; ?>&email=<?php echo urlencode($email_busca); ?>" 
                               class="btn btn-danger btn-cancelar">
                                <i class="fas fa-times"></i> Cancelar Reserva
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>Nenhuma reserva encontrada</h3>
        <p>Não encontramos reservas para o email informado.</p>
        <a href="reserva.php" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Fazer Nova Reserva
        </a>
    </div>
    
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-search"></i>
        <h3>Consulte suas reservas</h3>
        <p>Digite seu email no campo acima para visualizar suas reservas.</p>
    </div>
<?php endif; ?>

<div class="info-box">
    <h3><i class="fas fa-info-circle"></i> Informações sobre Cancelamento</h3>
    <ul>
        <li>Reservas podem ser canceladas até 2 horas antes do horário marcado</li>
        <li>Cancelamentos feitos com antecedência não geram cobrança</li>
        <li>Para cancelamentos de última hora, entre em contato conosco</li>
        <li>Para dúvidas, ligue para (11) 9999-9999</li>
    </ul>
</div>

<script>
// Confirmar cancelamento
document.querySelectorAll(".btn-cancelar").forEach(function(btn) {
    btn.addEventListener("click", function(e) {
        if (!confirm("Tem certeza que deseja cancelar esta reserva?\n\nEsta ação não pode ser desfeita.")) {
            e.preventDefault();
        }
    });
});

// Auto-focus no campo de email se estiver vazio
document.addEventListener("DOMContentLoaded", function() {
    const emailField = document.getElementById("email_busca");
    if (!emailField.value) {
        emailField.focus();
    }
});
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>

