<?php include __DIR__ . 
'../../../includes/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-cogs"></i> Painel Administrativo</h1>
    <p>Gerencie reservas, quadras e outros dados do sistema.</p>
</div>

<div class="admin-dashboard">
    <div class="dashboard-card">
        <h3><i class="fas fa-calendar-alt"></i> Reservas Recentes</h3>
        <?php if (empty($reservas)): ?>
            <p>Nenhuma reserva recente.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Quadra</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['IDRESERVA']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['cliente_nome']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['quadra_nome']); ?></td>
                            <td><?php echo htmlspecialchars(formatarDataBR($reserva['DATA'])); ?></td>
                            <td><?php echo htmlspecialchars(formatarHora($reserva['HORARIO'])); ?></td>
                            <td><?php echo htmlspecialchars($reserva['STATUS']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="admin.php" class="btn btn-primary">Ver Todas as Reservas</a>
    </div>

    <div class="dashboard-card">
        <h3><i class="fas fa-volleyball-ball"></i> Gerenciar Quadras</h3>
        <p>Adicione, edite ou remova quadras.</p>
        <a href="admin.php?action=gerenciarQuadras" class="btn btn-secondary">Ir para Gerenciamento de Quadras</a>
    </div>
</div>

<?php include __DIR__ . 
'../../../includes/footer.php'; ?>

