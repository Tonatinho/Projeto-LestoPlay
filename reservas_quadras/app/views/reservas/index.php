<?php
// __DIR__ é uma constante mágica que sempre retorna o caminho do diretório do arquivo atual.
// É a forma mais segura de montar caminhos de arquivos.
include_once __DIR__ . '/../../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-calendar-plus"></i> Nova Reserva</h1>
    <p>Preencha os dados abaixo para fazer sua reserva</p>
</div>

<div class="form-container">
    <form method="POST" class="reserva-form">
        <div class="form-section">
            
            <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nome_cliente">Nome Completo *</label>
                    <input type="text" id="nome_cliente" name="nome_cliente" required 
                           value="<?php echo htmlspecialchars($nome_cliente ?? ''); ?>"
                           placeholder="Seu nome completo">
                </div>
                
                <div class="form-group">
                    <label for="email_cliente">Email *</label>
                    <input type="email" id="email_cliente" name="email_cliente" required 
                           value="<?php echo htmlspecialchars($email_cliente ?? ''); ?>"
                           placeholder="seu@email.com">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="telefone_cliente">Telefone *</label>
                    <input type="tel" id="telefone_cliente" name="telefone_cliente" required 
                           value="<?php echo htmlspecialchars($telefone_cliente ?? ''); ?>"
                           placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label for="senha_cliente">Senha *</label>
                    <input type="password" id="senha_cliente" name="senha_cliente" required 
                           placeholder="Sua senha">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-volleyball-ball"></i> Dados da Reserva</h3>
            
            <div class="form-group">
                <label for="id_quadra">Quadra *</label>
                <select id="id_quadra" name="id_quadra" required>
                    <option value="">Selecione uma quadra</option>
                    <?php foreach ($quadras as $quadra): ?>
                        <option value="<?php echo $quadra['id']; ?>" 
                                <?php echo ($quadra_selecionada == $quadra['id'] || (isset($id_quadra) && $id_quadra == $quadra['id'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($quadra['nome']); ?> (<?php echo htmlspecialchars($quadra['localizacao']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_esporte">Tipo de Esporte *</label>
                <select id="id_esporte" name="id_esporte" required>
                    <option value="">Selecione o esporte</option>
                    <?php foreach ($tipos_esporte as $esporte): ?>
                        <option value="<?php echo $esporte['IDESPORTE']; ?>"
                                <?php echo (isset($id_esporte) && $id_esporte == $esporte['IDESPORTE']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($esporte['MODALIDADE']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_equip">Equipamento *</label>
                <select id="id_equip" name="id_equip" required>
                    <option value="">Selecione o equipamento</option>
                    <?php foreach ($equipamentos as $equip): ?>
                        <option value="<?php echo $equip["IDEQUIP"]; ?>"
                                <?php echo (isset($id_equip) && $id_equip == $equip["IDEQUIP"]) ? "selected" : ""; ?>
                                <?php echo ($equip["QUANTIDADE"] <= 0) ? "disabled" : ""; ?>>
                            <?php echo htmlspecialchars($equip["NOME"]); ?> 
                            <?php echo ($equip["QUANTIDADE"] <= 0) ? "(Esgotado)" : "(" . $equip["QUANTIDADE"] . " disponíveis)"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="data_reserva">Data *</label>
                    <input type="date" id="data_reserva" name="data_reserva" required 
                           min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo htmlspecialchars($data_reserva ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="horario_reserva">Horário *</label>
                    <input type="time" id="horario_reserva" name="horario_reserva" required 
                           value="<?php echo htmlspecialchars($horario_reserva ?? ''); ?>">
                </div>

            </div>
            
            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea id="observacoes" name="observacoes" rows="3" 
                          placeholder="Informações adicionais (opcional)"><?php echo htmlspecialchars($observacoes ?? ''); ?></textarea>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-check"></i> Confirmar Reserva
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </form>
</div>

<div class="info-box">
    <h3><i class="fas fa-info-circle"></i> Informações Importantes</h3>
    <ul>
        <li>Reservas podem ser feitas com até 30 dias de antecedência</li>
        <li>Horário mínimo de reserva: 1 hora</li>
        <li>Horário máximo de reserva: 4 horas consecutivas</li>
        <li>Funcionamento: Segunda a Sexta das 6h às 23h, Sábado e Domingo das 7h às 22h</li>
        <li>Cancelamentos podem ser feitos até 2 horas antes do horário reservado</li>
    </ul>
</div>

<script>
// Máscara para telefone
document.getElementById('telefone_cliente').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    e.target.value = value;
});
</script>

<?php include __DIR__ . 
'../../../includes/footer.php'; ?>

