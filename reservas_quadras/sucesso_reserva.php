<?php
require_once __DIR__ . '/config.php';
require_once ROOT_PATH . '/includes/db.php';

$titulo = 'Reserva Confirmada';

session_start();

// Verificar se há dados da reserva na sessão
$detalhes = $_SESSION['detalhes_reserva'] ?? null;
$mensagem = $_SESSION['mensagem'] ?? null;
$tipo_mensagem = $_SESSION['tipo_mensagem'] ?? null;

// Limpar dados da sessão
unset($_SESSION['detalhes_reserva']);
unset($_SESSION['mensagem']);
unset($_SESSION['tipo_mensagem']);

// Se não tem nada entao redireciona
if (!$detalhes) {
    header('Location: index.php');
    exit;
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Reserva Confirmada!</h1>
        <p class="success-message">Sua reserva foi realizada com sucesso. Confira os detalhes abaixo:</p>
        
        <div class="reserva-detalhes">
            <div class="detalhe-item">
                <div class="detalhe-label">Número da Reserva:</div>
                <div class="detalhe-valor">#<?php echo str_pad($detalhes['id'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>
            
            <div class="detalhe-item">
                <div class="detalhe-label">Quadra:</div>
                <div class="detalhe-valor"><?php echo htmlspecialchars($detalhes['quadra']); ?></div>
            </div>
            
            <div class="detalhe-item">
                <div class="detalhe-label">Data:</div>
                <div class="detalhe-valor"><?php echo formatarDataBR($detalhes['data']); ?></div>
            </div>
            
            <div class="detalhe-item">
                <div class="detalhe-label">Horário:</div>
                <div class="detalhe-valor"><?php echo formatarHora($detalhes['horario']); ?></div>
            </div>
            
            <div class="detalhe-item destaque">
                <div class="detalhe-label">Valor Total:</div>
                <div class="detalhe-valor">R$ <?php echo number_format($detalhes['preco'], 2, ',', '.'); ?></div>
            </div>
        </div>
        
        <div class="success-actions">
            <a href="minhas_reservas.php" class="btn btn-primary">
                <i class="fas fa-list"></i> Ver Minhas Reservas
            </a>
            <a href="reserva.php" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Nova Reserva
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Página Inicial
            </a>
        </div>
    </div>
    
    <div class="info-importante">
        <h3><i class="fas fa-exclamation-triangle"></i> Informações Importantes</h3>
        <ul>
            <li><strong>Chegue com 15 minutos de antecedência</strong> para liberar a quadra</li>
            <li><strong>Traga documento com foto</strong> para confirmar a reserva</li>
            <li><strong>Cancelamentos</strong> podem ser feitos até 2 horas antes do horário</li>
            <li><strong>Pagamento</strong> deve ser feito na recepção antes do jogo</li>
            <li><strong>Dúvidas?</strong> Entre em contato: (11) 9999-9999</li>
        </ul>
    </div>
    
    <div class="compartilhar">
        <h4>Compartilhe com seus amigos:</h4>
        <div class="share-buttons">
            <a href="https://wa.me/?text=Reservei uma quadra na Arena Sports! Data: <?php echo formatarDataBR($detalhes['data']); ?> às <?php echo formatarHora($detalhes['horario']); ?>" 
               target="_blank" class="btn btn-whatsapp">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <button onclick="copiarDetalhes()" class="btn btn-copy">
                <i class="fas fa-copy"></i> Copiar Detalhes
            </button>
        </div>
    </div>
</div>

<script>
function copiarDetalhes() {
    const detalhes = `
Reserva Confirmada - Arena Sports
Número: #<?php echo str_pad($detalhes['id'], 6, '0', STR_PAD_LEFT); ?>
Quadra: <?php echo htmlspecialchars($detalhes['quadra']); ?>
Data: <?php echo formatarDataBR($detalhes['data']); ?>
Horário: <?php echo formatarHora($detalhes['horario']); ?>
Valor: R$ <?php echo number_format($detalhes['preco'], 2, ',', '.'); ?>
    `.trim();
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(detalhes).then(function() {
            alert('Detalhes copiados para a área de transferência!');
        });
    } else {
        // Fallback para navegadores mais antigos
        const textArea = document.createElement('textarea');
        textArea.value = detalhes;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Detalhes copiados para a área de transferência!');
    }
}

// Auto-redirecionar após 2 minutos
setTimeout(function() {
    if (confirm('Deseja ir para a página inicial?')) {
        window.location.href = 'index.php';
    }
}, 120000); // 2 minutos
</script>

<?php include ROOT_PATH . '/includes/footer.php'; ?>

