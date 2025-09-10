<?php
include_once __DIR__ . '/../../../config.php';
include_once ROOT_PATH . '/includes/header.php';
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Bem-vindo ao LestoPlay Arena</h1>
        <p>Reserve sua quadra de areia de forma rápida e fácil</p>
        <a href="reserva.php" class="btn btn-primary btn-large">
            <i class="fas fa-calendar-plus"></i> Fazer Reserva
        </a>
    </div>
</div>

<section class="quadras-section">
    <h2><i class="fas fa-volleyball-ball"></i> Nossas Quadras</h2>
    
    <?php if (empty($quadras)): ?>
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Nenhuma quadra disponível</h3>
            <p>No momento não há quadras disponíveis para reserva.</p>
        </div>
    <?php else: ?>
        <div class="quadras-grid">
            <?php foreach ($quadras as $quadra): ?>
                <div class="quadra-card">
                    <div class="quadra-header">
                        <h3><?php echo htmlspecialchars($quadra['nome']); ?></h3>
                        <span class="preco"></span>
                    </div>
                    
                    <div class="quadra-body">
                        <p class="descricao">
                            Localização: <?php echo htmlspecialchars($quadra['localizacao'] ?: 'Não informada'); ?>
                        </p>
                        
                        <div class="quadra-features">
                            <span class="feature"><i class="fas fa-lightbulb"></i> Iluminação</span>
                            <span class="feature"><i class="fas fa-shower"></i> Vestiário</span>
                            <span class="feature"><i class="fas fa-parking"></i> Estacionamento</span>
                        </div>
                    </div>
                    
                    <div class="quadra-footer">
                        <a href="reserva.php?quadra=<?php echo $quadra['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Reservar
                        </a>
                        <button class="btn btn-secondary" onclick="verDisponibilidade(<?php echo $quadra['id']; ?>)">
                            <i class="fas fa-clock"></i> Ver Horários
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="info-section">
    <div class="info-grid">
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-clock"></i>
            </div>
            <h3>Horário Flexível</h3>
            <p>Funcionamos das 6h às 23h durante a semana e das 7h às 22h nos fins de semana.</p>
        </div>
        
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Segurança</h3>
            <p>Ambiente seguro com monitoramento 24h e equipamentos de primeira qualidade.</p>
        </div>
        
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3>Fácil Reserva</h3>
            <p>Sistema online simples e rápido para fazer suas reservas a qualquer hora.</p>
        </div>
        
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Para Todos</h3>
            <p>Quadras adequadas para futebol, vôlei e outros esportes de areia.</p>
        </div>
    </div>
</section>

<!-- Modal para ver disponibilidade -->
<div id="modalDisponibilidade" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Disponibilidade da Quadra</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div id="disponibilidadeContent">
                <p>Carregando...</p>
            </div>
        </div>
    </div>
</div>

<script>
function verDisponibilidade(quadraId) {
    const modal = document.getElementById('modalDisponibilidade');
    const content = document.getElementById('disponibilidadeContent');
    
    modal.style.display = 'block';
    content.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Carregando disponibilidade...</p>';
    
    // Simular carregamento de disponibilidade
    setTimeout(() => {
        const hoje = new Date();
        let html = '<h4>Próximos 7 dias:</h4><div class="disponibilidade-lista">';
        
        for (let i = 0; i < 7; i++) {
            const data = new Date(hoje);
            data.setDate(hoje.getDate() + i);
            const dataStr = data.toLocaleDateString('pt-BR');
            const diaSemana = data.toLocaleDateString('pt-BR', { weekday: 'long' });
            
            html += `\n                <div class="dia-disponibilidade">\n                    <h5>${diaSemana}, ${dataStr}</h5>\n                    <div class="horarios">\n                        <span class="horario disponivel">08:00</span>\n                        <span class="horario ocupado">10:00</span>\n                        <span class="horario disponivel">14:00</span>\n                        <span class="horario disponivel">16:00</span>\n                        <span class="horario ocupado">18:00</span>\n                        <span class="horario disponivel">20:00</span>\n                    </div>\n                </div>\n            `;
        }
        
        html += '</div><p><a href="reserva.php?quadra=' + quadraId + '" class="btn btn-primary">Fazer Reserva</a></p>';
        content.innerHTML = html;
    }, 1000);
}

// Fechar modal
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('modalDisponibilidade').style.display = 'none';
});

window.addEventListener('click', function(e) {
    const modal = document.getElementById('modalDisponibilidade');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
</script>

<?php include_once ROOT_PATH . '/includes/footer.php'; ?>

