<?php
include_once __DIR__ . '/../../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-star"></i> Avaliações - <?php echo htmlspecialchars($quadra['nome']); ?></h1>
    <p>Veja o que nossos clientes dizem sobre esta quadra</p>
</div>

<div class="avaliacoes-container">
    <div class="quadra-info-card">
        <h3><?php echo htmlspecialchars($quadra['nome']); ?></h3>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($quadra['localizacao']); ?></p>
        <p><i class="fas fa-money-bill-wave"></i> R$ <?php echo number_format($quadra['preco_hora'], 2, ',', '.'); ?>/hora</p>
        
        <div class="media-avaliacoes">
            <?php if ($mediaAvaliacoes['total'] > 0): ?>
                <div class="estrelas">
                    <?php 
                    $media = round($mediaAvaliacoes['media'], 1);
                    for ($i = 1; $i <= 5; $i++): 
                        if ($i <= $media): ?>
                            <i class="fas fa-star estrela-preenchida"></i>
                        <?php else: ?>
                            <i class="far fa-star estrela-vazia"></i>
                        <?php endif;
                    endfor; ?>
                </div>
                <span class="media-numero"><?php echo number_format($media, 1, ',', '.'); ?></span>
                <span class="total-avaliacoes">(<?php echo $mediaAvaliacoes['total']; ?> avaliações)</span>
            <?php else: ?>
                <p class="sem-avaliacoes">Ainda não há avaliações para esta quadra.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($avaliacoes)): ?>
        <div class="lista-avaliacoes">
            <h3>Comentários dos Clientes</h3>
            
            <?php foreach ($avaliacoes as $avaliacao): ?>
                <div class="avaliacao-item">
                    <div class="avaliacao-header">
                        <div class="cliente-info">
                            <strong><?php echo htmlspecialchars($avaliacao['nome_cliente']); ?></strong>
                            <span class="data-avaliacao">
                                <?php echo date('d/m/Y H:i', strtotime($avaliacao['DATA_AVALIACAO'])); ?>
                            </span>
                        </div>
                        <div class="nota-estrelas">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $avaliacao['NOTA']): ?>
                                    <i class="fas fa-star estrela-preenchida"></i>
                                <?php else: ?>
                                    <i class="far fa-star estrela-vazia"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="avaliacao-comentario">
                        <p><?php echo nl2br(htmlspecialchars($avaliacao['COMENTARIO'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="acoes">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar às Quadras
        </a>
        <a href="reserva.php?quadra=<?php echo $quadra['id']; ?>" class="btn btn-primary">
            <i class="fas fa-calendar-plus"></i> Reservar Esta Quadra
        </a>
    </div>
</div>

<style>
.avaliacoes-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.quadra-info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    border-left: 4px solid #007bff;
}

.media-avaliacoes {
    margin-top: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.estrelas {
    display: flex;
    gap: 2px;
}

.estrela-preenchida {
    color: #ffc107;
}

.estrela-vazia {
    color: #dee2e6;
}

.media-numero {
    font-size: 1.2em;
    font-weight: bold;
    color: #007bff;
}

.total-avaliacoes {
    color: #6c757d;
}

.sem-avaliacoes {
    color: #6c757d;
    font-style: italic;
}

.lista-avaliacoes h3 {
    margin-bottom: 20px;
    color: #333;
}

.avaliacao-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.avaliacao-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.cliente-info strong {
    color: #333;
}

.data-avaliacao {
    color: #6c757d;
    font-size: 0.9em;
    margin-left: 10px;
}

.nota-estrelas {
    display: flex;
    gap: 2px;
}

.avaliacao-comentario p {
    margin: 0;
    line-height: 1.6;
    color: #555;
}

.acoes {
    margin-top: 30px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

@media (max-width: 768px) {
    .avaliacao-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .acoes {
        flex-direction: column;
    }
}
</style>

<?php include_once ROOT_PATH . '/includes/footer.php'; ?>

