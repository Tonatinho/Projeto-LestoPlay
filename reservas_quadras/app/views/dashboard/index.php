<?php
include_once __DIR__ . '/../../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Dashboard - Estatísticas</h1>
    <p>Visão geral do desempenho das quadras</p>
</div>

<div class="dashboard-container">
    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($totalReservas, 0, ',', '.'); ?></h3>
                <p>Total de Reservas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <h3>R$ <?php echo number_format($receitaTotal, 2, ',', '.'); ?></h3>
                <p>Receita Total</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($totalClientes, 0, ',', '.'); ?></h3>
                <p>Total de Clientes</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format($mediaAvaliacoes, 1, ',', '.'); ?></h3>
                <p>Média de Avaliações</p>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <!-- Quadras Mais Populares -->
        <div class="chart-card">
            <h3><i class="fas fa-trophy"></i> Quadras Mais Populares</h3>
            <div class="chart-content">
                <?php if (!empty($quadrasMaisPopulares)): ?>
                    <?php foreach ($quadrasMaisPopulares as $quadra): ?>
                        <div class="bar-item">
                            <div class="bar-label"><?php echo htmlspecialchars($quadra['nome']); ?></div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?php echo ($quadra['total_reservas'] / max(array_column($quadrasMaisPopulares, 'total_reservas'))) * 100; ?>%"></div>
                                <span class="bar-value"><?php echo $quadra['total_reservas']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Horários de Pico -->
        <div class="chart-card">
            <h3><i class="fas fa-clock"></i> Horários de Pico</h3>
            <div class="chart-content">
                <?php if (!empty($horariosPico)): ?>
                    <?php foreach ($horariosPico as $horario): ?>
                        <div class="bar-item">
                            <div class="bar-label"><?php echo sprintf('%02d:00', $horario['hora']); ?></div>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?php echo ($horario['total'] / max(array_column($horariosPico, 'total'))) * 100; ?>%"></div>
                                <span class="bar-value"><?php echo $horario['total']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reservas por Mês -->
        <div class="chart-card full-width">
            <h3><i class="fas fa-chart-line"></i> Reservas por Mês (Últimos 6 meses)</h3>
            <div class="chart-content">
                <?php if (!empty($reservasPorMes)): ?>
                    <div class="line-chart">
                        <?php foreach ($reservasPorMes as $mes): ?>
                            <div class="line-item">
                                <div class="line-label"><?php echo date('M/Y', strtotime($mes['mes'] . '-01')); ?></div>
                                <div class="line-bar" style="height: <?php echo ($mes['total'] / max(array_column($reservasPorMes, 'total'))) * 100; ?>%"></div>
                                <div class="line-value"><?php echo $mes['total']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Receita por Mês -->
        <div class="chart-card full-width">
            <h3><i class="fas fa-chart-area"></i> Receita por Mês (Últimos 6 meses)</h3>
            <div class="chart-content">
                <?php if (!empty($receitaPorMes)): ?>
                    <div class="line-chart">
                        <?php foreach ($receitaPorMes as $mes): ?>
                            <div class="line-item">
                                <div class="line-label"><?php echo date('M/Y', strtotime($mes['mes'] . '-01')); ?></div>
                                <div class="line-bar receita" style="height: <?php echo ($mes['receita'] / max(array_column($receitaPorMes, 'receita'))) * 100; ?>%"></div>
                                <div class="line-value">R$ <?php echo number_format($mes['receita'], 0, ',', '.'); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-data">Nenhum dado disponível</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="admin.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Admin
        </a>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-home"></i> Ir para o Site
        </a>
    </div>
</div>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    background: #007bff;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
}

.stat-content h3 {
    margin: 0;
    font-size: 1.8em;
    color: #333;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 0.9em;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chart-card.full-width {
    grid-column: 1 / -1;
}

.chart-card h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.1em;
}

.bar-item {
    margin-bottom: 15px;
}

.bar-label {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 5px;
}

.bar-container {
    position: relative;
    background: #f8f9fa;
    height: 25px;
    border-radius: 12px;
    overflow: hidden;
}

.bar-fill {
    background: linear-gradient(90deg, #007bff, #0056b3);
    height: 100%;
    border-radius: 12px;
    transition: width 0.3s ease;
}

.bar-value {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8em;
    color: #333;
    font-weight: bold;
}

.line-chart {
    display: flex;
    align-items: end;
    gap: 15px;
    height: 200px;
    padding: 20px 0;
}

.line-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.line-bar {
    background: linear-gradient(180deg, #007bff, #0056b3);
    width: 30px;
    min-height: 10px;
    border-radius: 4px 4px 0 0;
    transition: height 0.3s ease;
}

.line-bar.receita {
    background: linear-gradient(180deg, #28a745, #1e7e34);
}

.line-label {
    font-size: 0.8em;
    color: #666;
    text-align: center;
}

.line-value {
    font-size: 0.8em;
    color: #333;
    font-weight: bold;
    text-align: center;
}

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}

.dashboard-actions {
    text-align: center;
    margin-top: 30px;
}

.dashboard-actions .btn {
    margin: 0 10px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .line-chart {
        height: 150px;
    }
    
    .line-bar {
        width: 20px;
    }
}
</style>

<?php include_once ROOT_PATH . '/includes/footer.php'; ?>

