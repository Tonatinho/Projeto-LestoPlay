<?php
// Incluir o header do admin
include_once ROOT_PATH . "/includes/header_admin.php";

// Exibir mensagens de feedback
if (isset($_SESSION["mensagem"])) {
    echo "<div class=\"alert alert-" . $_SESSION["tipo_mensagem"] . "\">" . $_SESSION["mensagem"] . "</div>";
    unset($_SESSION["mensagem"]);
    unset($_SESSION["tipo_mensagem"]);
}
?>

<div class="admin-content">
    <div class="section-header">
        <h2><i class="fas fa-dumbbell"></i> Gerenciar Equipamentos</h2>
        <?php if ($acao !== "adicionar" && $acao !== "editar"): ?>
            <a href="?acao=equipamentos&subacao=adicionar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Adicionar Equipamento
            </a>
        <?php endif; ?>
    </div>

    <?php if ($acao === "adicionar" || $acao === "editar"): ?>
        <!-- Formulário de Adição/Edição de Equipamento -->
        <div class="admin-form-container">
            <h3><?php echo ($acao === "adicionar") ? "Adicionar Novo Equipamento" : "Editar Equipamento"; ?></h3>
            <form action="admin.php?acao=processar_equipamento" method="POST" class="admin-form">
                <input type="hidden" name="acao" value="<?php echo ($acao === "adicionar") ? "adicionar_equipamento" : "editar_equipamento"; ?>">
                <?php if ($acao === "editar" && $equipamento_editando): ?>
                    <input type="hidden" name="id" value="<?php echo $equipamento_editando["IDEQUIP"]; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nome">Nome do Equipamento *</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?php echo ($acao === "editar" && $equipamento_editando) ? htmlspecialchars($equipamento_editando["NOME"]) : ""; ?>">
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade Disponível *</label>
                    <input type="number" id="quantidade" name="quantidade" min="0" required 
                           value="<?php echo ($acao === "editar" && $equipamento_editando) ? htmlspecialchars($equipamento_editando["QUANTIDADE"]) : "0"; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo ($acao === "adicionar") ? "Salvar Equipamento" : "Salvar Alterações"; ?>
                    </button>
                    <a href="admin.php?acao=equipamentos" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Lista de Equipamentos -->
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipamentos)): ?>
                        <tr>
                            <td colspan="4">Nenhum equipamento cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipamentos as $equipamento): ?>
                            <tr>
                                <td><?php echo $equipamento["IDEQUIP"]; ?></td>
                                <td><?php echo htmlspecialchars($equipamento["NOME"]); ?></td>
                                <td><?php echo htmlspecialchars($equipamento["QUANTIDADE"]); ?></td>
                                <td class="actions">
                                    <a href="?acao=equipamentos&subacao=editar&id=<?php echo $equipamento["IDEQUIP"]; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" action="admin.php?acao=processar_equipamento" style="display:inline-block;" onsubmit="return confirm(\'Tem certeza que deseja excluir este equipamento? Esta ação não pode ser desfeita.\');">
                                        <input type="hidden" name="acao" value="excluir_equipamento">
                                        <input type="hidden" name="id" value="<?php echo $equipamento["IDEQUIP"]; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
// Incluir o footer do admin
include_once ROOT_PATH . "/includes/footer_admin.php";
?>

