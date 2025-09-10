<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo . ' - ' : ''; ?>Reservas de Quadras de Areia</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-volleyball-ball"></i> LestoPlay Arenas</h1>
            </div>
            <nav class="nav">
                <ul>
                    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Início
                    </a></li>
                    <li><a href="reserva.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reserva.php' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-plus"></i> Nova Reserva
                    </a></li>
                    <li><a href="minhas_reservas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'minhas_reservas.php' ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> Minhas Reservas
                    </a></li>
                    <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' || basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> Admin
                    </a></li>
                </ul>
            </nav>
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <main class="main-content">
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem ?? 'info'; ?>">
                <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : ($tipo_mensagem == 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="container"><?php // Conteúdo da página será inserido aqui ?>

