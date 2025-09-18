<?php
require_once __DIR__ . '/config.php';
require_once ROOT_PATH . '/includes/db.php';
session_start();

// Verifica se o usuário está logado como administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$titulo = 'Painel Administrativo';

// Verificar se é uma ação específica
$acao = $_GET['acao'] ?? '';
$quadra_id = $_GET['id'] ?? null;

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'adicionar_quadra':
                $nome = trim($_POST["nome"]);
                $localizacao = trim($_POST["localizacao"]);
                $preco_hora = (float)($_POST["preco_hora"] ?? 0.0);
                
                if (!empty($nome) && !empty($localizacao) && $preco_hora >= 0) {
                    try {
                        $pdo = conectarDB();
                        $stmt = $pdo->prepare("INSERT INTO quadras (nome, localizacao, preco_hora) VALUES (:nome, :localizacao, :preco_hora)");
                        $stmt->execute([":nome" => $nome, ":localizacao" => $localizacao, ":preco_hora" => $preco_hora]);
                        $mensagem = "Quadra adicionada com sucesso!";
                        $tipo_mensagem = "success";
                    } catch (Exception $e) {
                        $mensagem = "Erro ao adicionar quadra: " . $e->getMessage();
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "Preencha todos os campos obrigatórios.";
                    $tipo_mensagem = "error";
                }
                break;
                
            case 'editar_quadra':
                $id = (int)$_POST['id'];
                $nome = trim($_POST['nome']);
                $localizacao = trim($_POST['localizacao']);
                $preco_hora = (float)($_POST['preco_hora'] ?? 0.0);
                
                if (!empty($nome) && !empty($localizacao) && $preco_hora >= 0) {
                    try {
                        $pdo = conectarDB();
                        $stmt = $pdo->prepare("UPDATE quadras SET nome = :nome, localizacao = :localizacao, preco_hora = :preco_hora WHERE id = :id");
                        $stmt->execute([':nome' => $nome, ':localizacao' => $localizacao, ':preco_hora' => $preco_hora, ':id' => $id]);
                        $mensagem = "Quadra atualizada com sucesso!";
                        $tipo_mensagem = "success";
                        $acao = ''; // Voltar para lista
                    } catch (Exception $e) {
                        $mensagem = "Erro ao atualizar quadra: " . $e->getMessage();
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "Preencha todos os campos obrigatórios.";
                    $tipo_mensagem = "error";
                }
                break;
                
            case 'excluir_quadra':
                $id = (int)$_POST['id'];
                
                if ($id > 0) {
                    try {
                        $pdo = conectarDB();
                        
                        // Verificar se há reservas ativas para esta quadra
                        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM RESERVAS WHERE IDQUADRA = :id AND STATUS = 'ativa' AND DATA >= CURDATE()");
                        $stmt->execute([':id' => $id]);
                        $reservas_ativas = $stmt->fetch()['total'];
                        
                        if ($reservas_ativas > 0) {
                            $mensagem = "Não é possível excluir esta quadra pois há {$reservas_ativas} reserva(s) ativa(s) para ela.";
                            $tipo_mensagem = "error";
                        } else {
                            // Excluir a quadra (as reservas antigas serão mantidas para histórico)
                            $stmt = $pdo->prepare("DELETE FROM quadras WHERE id = :id");
                            $stmt->execute([':id' => $id]);
                            $mensagem = "Quadra excluída com sucesso!";
                            $tipo_mensagem = "success";
                        }
                    } catch (Exception $e) {
                        $mensagem = "Erro ao excluir quadra: " . $e->getMessage();
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "ID da quadra inválido.";
                    $tipo_mensagem = "error";
                }
                break;
                
            case 'excluir_reserva':
                $id = (int)$_POST['id'];
                
                if ($id > 0) {
                    try {
                        $pdo = conectarDB();
                        
                        // Buscar dados da reserva antes de excluir para notificação
                        $stmt = $pdo->prepare("
                            SELECT r.*, c.NOME as nome_cliente, c.TELEFONE as telefone_cliente, q.nome as nome_quadra
                            FROM RESERVAS r 
                            JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE
                            JOIN quadras q ON r.IDQUADRA = q.id
                            WHERE r.IDRESERVA = :id
                        ");
                        $stmt->execute([':id' => $id]);
                        $reserva = $stmt->fetch();
                        
                        if ($reserva) {
                            // Excluir a reserva
                            $stmt = $pdo->prepare("DELETE FROM RESERVAS WHERE IDRESERVA = :id");
                            $stmt->execute([':id' => $id]);
                            
                            // Enviar notificação de cancelamento via WhatsApp (se disponível)
                            if (class_exists('WhatsAppNotification')) {
                                require_once ROOT_PATH . '/app/models/WhatsAppNotification.php';
                                $whatsapp = new WhatsAppNotification($pdo);
                                $whatsapp->enviarCancelamento(
                                    $reserva['telefone_cliente'],
                                    $reserva['nome_cliente'],
                                    $reserva['nome_quadra'],
                                    $reserva['DATA'],
                                    $reserva['HORARIO']
                                );
                            }
                            
                            $mensagem = "Reserva excluída com sucesso!";
                            $tipo_mensagem = "success";
                        } else {
                            $mensagem = "Reserva não encontrada.";
                            $tipo_mensagem = "error";
                        }
                    } catch (Exception $e) {
                        $mensagem = "Erro ao excluir reserva: " . $e->getMessage();
                        $tipo_mensagem = "error";
                    }
                } else {
                    $mensagem = "ID da reserva inválido.";
                    $tipo_mensagem = "error";
                }
                break;
        }
    }
}

// Buscar dados para exibição
try {
    $pdo = conectarDB();
    
    // Estatísticas gerais
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM quadras");
    $total_quadras = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM RESERVAS WHERE STATUS = 'ativa' AND DATA >= CURDATE()");
    $reservas_ativas = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM RESERVAS WHERE DATA = CURDATE() AND STATUS = 'ativa'");
    $reservas_hoje = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT SUM(PRECO) as total FROM RESERVAS WHERE STATUS = 'ativa' AND MONTH(DATA) = MONTH(CURDATE()) AND YEAR(DATA) = YEAR(CURDATE())");
    $faturamento_mes = $stmt->fetch()['total'] ?? 0;
    
    // Buscar quadras
    $stmt = $pdo->query("SELECT * FROM quadras ORDER BY nome");
    $quadras = $stmt->fetchAll();
    
    // Buscar reservas recentes
    $stmt = $pdo->query("
        SELECT r.*, q.nome as nome_quadra, c.NOME as nome_cliente, te.MODALIDADE as nome_esporte, e.NOME as nome_equipamento
        FROM RESERVAS r 
        JOIN quadras q ON r.IDQUADRA = q.id 
        JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE
        JOIN TIPOESPORTE te ON r.IDESPORTE = te.IDESPORTE
        JOIN EQUIPAMENTO e ON r.IDEQUIP = e.IDEQUIP
        ORDER BY r.DATA DESC, r.HORARIO DESC 
        LIMIT 10
    ");
    $reservas_recentes = $stmt->fetchAll();
    
    // Se está editando uma quadra específica
    $quadra_editando = null;
    if ($acao === 'editar' && $quadra_id) {
        $stmt = $pdo->prepare("SELECT * FROM quadras WHERE id = :id");
        $stmt->bindParam(':id', $quadra_id);
        $stmt->execute();
        $quadra_editando = $stmt->fetch();
    }
    
} catch (Exception $e) {
    $mensagem = "Erro ao carregar dados: " . $e->getMessage();
    $tipo_mensagem = "error";
    $quadras = [];
    $reservas_recentes = [];
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cog"></i> Painel Administrativo</h1>
    <p>Gerencie quadras e visualize reservas</p>
</div>

<?php if ($acao === '' || $acao === 'dashboard'): ?>
    <!-- Dashboard Principal -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-volleyball-ball"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $total_quadras; ?></div>
                <div class="stat-label">Total de Quadras</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $reservas_ativas; ?></div>
                <div class="stat-label">Reservas Futuras</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $reservas_hoje; ?></div>
                <div class="stat-label">Reservas Hoje</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">R$ <?php echo number_format($faturamento_mes, 2, ',', '.'); ?></div>
                <div class="stat-label">Faturamento do Mês</div>
            </div>
        </div>
    </div>
    
    <div class="admin-actions">
        <a href="?acao=adicionar" class="btn btn-primary">
            <i class="fas fa-plus"></i> Adicionar Quadra
        </a>
        <a href="dashboard.php" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> Dashboard
        </a>
        <a href="?acao=quadras" class="btn btn-secondary">
            <i class="fas fa-list"></i> Gerenciar Quadras
        </a>
        <a href="?acao=reservas" class="btn btn-secondary">
            <i class="fas fa-calendar"></i> Ver Todas Reservas
        </a>
        <a href="?acao=equipamentos" class="btn btn-secondary">
            <i class="fas fa-dumbbell"></i> Gerenciar Equipamentos
        </a>
    </div>
    
    <div class="admin-sections">
        <div class="section">
            <h3><i class="fas fa-clock"></i> Reservas Recentes</h3>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Quadra</th>
                            <th>Esporte</th>
                            <th>Equipamento</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Status</th>
                            <th>Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas_recentes as $reserva): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reserva['nome_cliente']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['nome_quadra']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['nome_esporte']); ?></td>
                                <td><?php echo htmlspecialchars($reserva['nome_equipamento']); ?></td>
                                <td><?php echo formatarDataBR($reserva['DATA']); ?></td>
                                <td><?php echo formatarHora($reserva['HORARIO']); ?></td>
                                <td><span class="status status-<?php echo $reserva['STATUS']; ?>"><?php echo ucfirst($reserva['STATUS']); ?></span></td>
                                <td>R$ <?php echo number_format($reserva['PRECO'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($acao === 'adicionar'): ?>
    <!-- Formulário Adicionar Quadra -->
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-plus"></i> Adicionar Nova Quadra</h2>
            <a href="admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        
        <form method="POST" class="admin-form">
            <input type="hidden" name="acao" value="adicionar_quadra">
            
            <div class="form-group">
                <label for="nome">Nome da Quadra *</label>
                <input type="text" id="nome" name="nome" required placeholder="Ex: Quadra Principal">
            </div>
            
            <div class="form-group">
                <label for="localizacao">Localização *</label>
                <input type="text" id="localizacao" name="localizacao" required placeholder="Ex: Rua da Praia, 100">
            </div>
            
            <div class="form-group">
                <label for="preco_hora">Preço por Hora *</label>
                <input type="number" id="preco_hora" name="preco_hora" step="0.01" min="0" value="0.00" required placeholder="Ex: 50.00">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Quadra
                </button>
                <a href="admin.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

<?php elseif ($acao === 'editar' && $quadra_editando): ?>
    <!-- Formulário Editar Quadra -->
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Editar Quadra</h2>
            <a href="?acao=quadras" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        
        <form method="POST" class="admin-form">
            <input type="hidden" name="acao" value="editar_quadra">
            <input type="hidden" name="id" value="<?php echo $quadra_editando['id']; ?>">
            
            <div class="form-group">
                <label for="nome">Nome da Quadra *</label>
                <input type="text" id="nome" name="nome" required 
                       value="<?php echo htmlspecialchars($quadra_editando['nome']); ?>">
            </div>
            
            <div class="form-group">
                <label for="localizacao">Localização *</label>
                <input type="text" id="localizacao" name="localizacao" required 
                       value="<?php echo htmlspecialchars($quadra_editando["localizacao"]); ?>">
            </div>
            
            <div class="form-group">
                <label for="preco_hora">Preço por Hora *</label>
                <input type="number" id="preco_hora" name="preco_hora" step="0.01" min="0" required 
                       value="<?php echo htmlspecialchars($quadra_editando["preco_hora"]); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="?acao=quadras" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

<?php elseif ($acao === 'quadras'): ?>
    <!-- Lista de Quadras -->
    <div class="admin-section">
        <div class="section-header">
            <h2><i class="fas fa-volleyball-ball"></i> Gerenciar Quadras</h2>
            <a href="?acao=adicionar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Adicionar Quadra
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Localização</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quadras as $quadra): ?>
                        <tr>
                            <td><?php echo $quadra['id']; ?></td>
                            <td><?php echo htmlspecialchars($quadra['nome']); ?></td>
                            <td><?php echo htmlspecialchars($quadra['localizacao'] ?: '-'); ?></td>
                            <td class="actions">
                                <a href="?acao=editar&id=<?php echo $quadra['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir esta quadra? Esta ação não pode ser desfeita.');">
                                    <input type="hidden" name="acao" value="excluir_quadra">
                                    <input type="hidden" name="id" value="<?php echo $quadra['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($acao === 'reservas'): ?>
    <!-- Lista de Todas as Reservas -->
    <?php
    // Buscar todas as reservas
    $stmt = $pdo->query("
        SELECT r.*, q.nome as nome_quadra, c.NOME as nome_cliente, c.EMAIL as email_cliente, c.TELEFONE as telefone_cliente, te.MODALIDADE as nome_esporte, e.NOME as nome_equipamento
        FROM RESERVAS r 
        JOIN quadras q ON r.IDQUADRA = q.id 
        JOIN CLIENTE c ON r.IDCLIENTE = c.IDCLIENTE
        JOIN TIPOESPORTE te ON r.IDESPORTE = te.IDESPORTE
        JOIN EQUIPAMENTO e ON r.IDEQUIP = e.IDEQUIP
        ORDER BY r.DATA DESC, r.HORARIO DESC
        LIMIT 50
    ");
    $todas_reservas = $stmt->fetchAll();
    ?>
    
    <div class="admin-section">
        <div class="section-header">
            <h2><i class="fas fa-calendar"></i> Todas as Reservas</h2>
            <a href="admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Quadra</th>
                        <th>Esporte</th>
                        <th>Equipamento</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Status</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todas_reservas as $reserva): ?>
                        <tr>
                            <td><?php echo $reserva['IDRESERVA']; ?></td>
                            <td><?php echo htmlspecialchars($reserva['nome_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['email_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['telefone_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['nome_quadra']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['nome_esporte']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['nome_equipamento']); ?></td>
                            <td><?php echo formatarDataBR($reserva['DATA']); ?></td>
                            <td><?php echo formatarHora($reserva['HORARIO']); ?></td>
                            <td><span class="status status-<?php echo $reserva['STATUS']; ?>"><?php echo ucfirst($reserva['STATUS']); ?></span></td>
                            <td>R$ <?php echo number_format($reserva['PRECO'], 2, ',', '.'); ?></td>
                            <td class="actions">
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir esta reserva? Esta ação não pode ser desfeita.');">
                                    <input type="hidden" name="acao" value="excluir_reserva">
                                    <input type="hidden" name="id" value="<?php echo $reserva['IDRESERVA']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php elseif ($acao === 'equipamentos'): ?>
    <?php
    require_once ROOT_PATH . '/app/controllers/EquipamentoController.php';
    $equipamentoController = new EquipamentoController(conectarDB());
    $equipamentoController->gerenciarEquipamentos();
    ?>
<?php elseif ($acao === 'processar_equipamento'): ?>
    <?php
    require_once ROOT_PATH . '/app/controllers/EquipamentoController.php';
    $equipamentoController = new EquipamentoController(conectarDB());
    $equipamentoController->processarEquipamento();
    ?>
<?php

include ROOT_PATH . 
'/includes/footer.php';

?>