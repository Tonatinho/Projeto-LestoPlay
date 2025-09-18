<?php
include_once __DIR__ . 
'/../../../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-tags"></i> Gerenciar Características das Quadras</h1>
    <p>Adicione, edite ou remova características que podem ser associadas às quadras.</p>
</div>

<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-plus"></i> Adicionar Nova Característica</h2>
    </div>
    
    <form method="POST" class="admin-form">
        <input type="hidden" name="acao" value="adicionar">
        
        <div class="form-group">
            <label for="nome_caracteristica">Nome da Característica *</label>
            <input type="text" id="nome_caracteristica" name="nome" required placeholder="Ex: Wi-Fi, Lanchonete, Estacionamento Gratuito">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Característica
            </button>
        </div>
    </form>
</div>

<div class="admin-section">
    <div class="section-header">
        <h2><i class="fas fa-list"></i> Características Existentes</h2>
    </div>
    
    <?php if (!empty($caracteristicas)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caracteristicas as $caracteristica): ?>
                        <tr>
                            <td><?php echo $caracteristica["IDCARACTERISTICA"]; ?></td>
                            <td><?php echo htmlspecialchars($caracteristica["NOME"]); ?></td>
                            <td class="actions">
                                <button class="btn btn-sm btn-primary" onclick="editarCaracteristica(<?php echo $caracteristica["IDCARACTERISTICA"]; ?>, '<?php echo htmlspecialchars($caracteristica["NOME"], ENT_QUOTES); ?>')">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Tem certeza que deseja excluir esta característica?');">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?php echo $caracteristica["IDCARACTERISTICA"]; ?>">
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
    <?php else: ?>
        <p class="no-data">Nenhuma característica cadastrada ainda.</p>
    <?php endif; ?>
</div>

<!-- Modal de Edição -->
<div id="modalEditarCaracteristica" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Característica</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="formEditarCaracteristica">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id" id="edit_id_caracteristica">
                <div class="form-group">
                    <label for="edit_nome_caracteristica">Nome da Característica *</label>
                    <input type="text" id="edit_nome_caracteristica" name="nome" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarCaracteristica(id, nome) {
    document.getElementById('edit_id_caracteristica').value = id;
    document.getElementById('edit_nome_caracteristica').value = nome;
    document.getElementById('modalEditarCaracteristica').style.display = 'block';
}

function fecharModal() {
    document.getElementById('modalEditarCaracteristica').style.display = 'none';
}

document.querySelector('#modalEditarCaracteristica .close').addEventListener('click', fecharModal);
window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('modalEditarCaracteristica')) {
        fecharModal();
    }
});
</script>

<style>
.modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.4); 
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.modal-header .close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.modal-header .close:hover,
.modal-header .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-body .form-group {
    margin-bottom: 15px;
}

.modal-body label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.modal-body input[type="text"] {
    width: calc(100% - 22px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.modal-body .form-actions {
    text-align: right;
    margin-top: 20px;
}

.modal-body .btn {
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9em;
}

.modal-body .btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
}

.modal-body .btn-primary:hover {
    background-color: #0056b3;
}

.modal-body .btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    margin-left: 10px;
}

.modal-body .btn-secondary:hover {
    background-color: #5a6268;
}
</style>

<?php include_once ROOT_PATH . 
'/includes/footer.php'; ?>

