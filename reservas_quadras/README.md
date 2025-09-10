# Sistema de Reservas de Quadras de Areia

Sistema completo para gerenciamento de reservas de quadras de areia desenvolvido em PHP com MySQL.

## 📋 Funcionalidades

- **Página Inicial**: Listagem de todas as quadras disponíveis
- **Sistema de Reservas**: Formulário completo para fazer reservas, incluindo seleção de esporte e equipamento
- **Consulta de Reservas**: Busca de reservas por email do cliente
- **Painel Administrativo**: Gerenciamento de quadras e visualização de todas as reservas
- **Cancelamento**: Possibilidade de cancelar reservas
- **Design Responsivo**: Funciona em desktop e mobile

## 🚀 Instalação no XAMPP

### 1. Pré-requisitos
- XAMPP instalado (Apache + MySQL + PHP)
- Navegador web

### 2. Configuração do Banco de Dados

1. Inicie o XAMPP e ative Apache e MySQL
2. Acesse o phpMyAdmin: `http://localhost/phpmyadmin`
3. Execute o script SQL do arquivo `database.sql` para criar o banco e tabelas:
   ```sql
   CREATE DATABASE lestoplay2025;

   CREATE TABLE CLIENTE (
   	IDCLIENTE INT PRIMARY KEY AUTO_INCREMENT,
   	NOME VARCHAR (50),
   	EMAIL VARCHAR (50),
   	SENHA VARCHAR (50),
   	TELEFONE VARCHAR (20)
   );

   CREATE TABLE TIPOESPORTE (
   	IDESPORTE INT PRIMARY KEY AUTO_INCREMENT,
   	MODALIDADE VARCHAR (50)
   );

   CREATE TABLE EQUIPAMENTO (
   	IDEQUIP INT PRIMARY KEY AUTO_INCREMENT,
   	NOME VARCHAR (50),
   	QUANTIDADE INT
   );

   CREATE TABLE quadras (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(100) NOT NULL,
       localizacao VARCHAR(200) NOT NULL
   );

   CREATE TABLE RESERVAS (
   	IDRESERVA INT PRIMARY KEY AUTO_INCREMENT,
   	HORARIO TIME,
   	DATA DATE,
   	PRECO DECIMAL (10, 2),
   	STATUS VARCHAR (20),
   	IDCLIENTE INT,
   	IDESPORTE INT,
   	IDEQUIP INT,
   	IDQUADRA INT,
   	FOREIGN KEY (IDCLIENTE) REFERENCES CLIENTE(IDCLIENTE),
   	FOREIGN KEY (IDESPORTE) REFERENCES TIPOESPORTE(IDESPORTE),
   	FOREIGN KEY (IDEQUIP) REFERENCES EQUIPAMENTO(IDEQUIP),
   	FOREIGN KEY (IDQUADRA) REFERENCES quadras(id)
   );

   -- Inserir dados de exemplo para as novas tabelas
   INSERT INTO CLIENTE (NOME, EMAIL, SENHA, TELEFONE) VALUES
   ("João Silva", "joao@example.com", "12345", "(11) 98765-4321"),
   ("Maria Souza", "maria@example.com", "abcde", "(21) 99876-5432");

   INSERT INTO TIPOESPORTE (MODALIDADE) VALUES
   ("Futebol de Areia"),
   ("Vôlei de Praia"),
   ("Beach Tennis");

   INSERT INTO EQUIPAMENTO (NOME, QUANTIDADE) VALUES
   ("Bola de Vôlei", 5),
   ("Rede de Beach Tennis", 3),
   ("Kit de Futebol de Areia", 2);

   INSERT INTO quadras (nome, localizacao) VALUES
   ("Quadra Principal", "Rua da Praia, 100"),
   ("Quadra Secundária", "Avenida do Sol, 250");

   -- Inserir algumas reservas de exemplo (ajuste os IDs conforme o auto_increment)
   INSERT INTO RESERVAS (HORARIO, DATA, PRECO, STATUS, IDCLIENTE, IDESPORTE, IDEQUIP, IDQUADRA) VALUES
   ("10:00:00", "2025-09-01", 50.00, "ativa", 1, 1, 1, 1),
   ("14:00:00", "2025-09-01", 60.00, "ativa", 2, 2, 2, 2),
   ("16:00:00", "2025-09-02", 55.00, "ativa", 1, 3, 3, 1);
   ```

4. O banco será criado automaticamente com dados de exemplo

### 3. Configuração dos Arquivos

1. Copie todos os arquivos para a pasta `htdocs` do XAMPP:
   ```
   C:\xampp\htdocs\reservas_quadras\
   ```

2. Verifique as configurações de banco em `includes/db.php`:
   ```php
   define("DB_HOST", "localhost");
   define("DB_USER", "root");
   define("DB_PASS", "");
   define("DB_NAME", "lestoplay2025");
   ```

### 4. Acesso ao Sistema

- **Página Principal**: `http://localhost/reservas_quadras/`
- **Nova Reserva**: `http://localhost/reservas_quadras/reserva.php`
- **Minhas Reservas**: `http://localhost/reservas_quadras/minhas_reservas.php`
- **Painel Admin**: `http://localhost/reservas_quadras/admin.php`

## 📁 Estrutura de Arquivos

```
reservas_quadras/
├── index.php              # Página inicial
├── reserva.php            # Formulário de reserva
├── minhas_reservas.php    # Consulta de reservas
├── admin.php              # Painel administrativo
├── sucesso_reserva.php    # Página de confirmação
├── style.css              # Estilos CSS
├── database.sql           # Script do banco de dados
├── README.md              # Este arquivo
├── includes/
│   ├── db.php            # Conexão com banco
│   ├── header.php        # Cabeçalho das páginas
│   └── footer.php        # Rodapé das páginas
└── processa/
    ├── processa_reserva.php  # Processar nova reserva
    └── cancelar_reserva.php  # Cancelar reserva
```

## 🎯 Como Usar

### Para Clientes

1. **Fazer Reserva**:
   - Acesse a página inicial
   - Clique em "Reservar" na quadra desejada
   - Preencha o formulário com seus dados, selecione o esporte e o equipamento
   - Confirme a reserva

2. **Consultar Reservas**:
   - Acesse "Minhas Reservas"
   - Digite seu email
   - Visualize e gerencie suas reservas

3. **Cancelar Reserva**:
   - Na lista de reservas, clique em "Cancelar"
   - Confirme o cancelamento

### Para Administradores

1. **Gerenciar Quadras**:
   - Acesse o painel administrativo
   - Adicione, edite ou exclua quadras
   - Configure nome e localização

2. **Visualizar Reservas**:
   - Veja estatísticas do sistema
   - Consulte todas as reservas
   - Monitore faturamento

## 🛠️ Configurações Avançadas

### Personalização

- **Logo**: Edite o arquivo `includes/header.php`
- **Cores**: Modifique as variáveis CSS em `style.css`
- **Informações de Contato**: Atualize `includes/footer.php`

### Banco de Dados

- **Host**: Altere em `includes/db.php` se necessário
- **Credenciais**: Configure usuário e senha do MySQL
- **Backup**: Use phpMyAdmin para exportar dados

### Funcionalidades Extras

- **Email de Confirmação**: Adicione função de envio de email
- **Pagamento Online**: Integre gateway de pagamento
- **Relatórios**: Implemente relatórios detalhados

## 🔧 Solução de Problemas

### Erro de Conexão com Banco
- Verifique se o MySQL está rodando no XAMPP
- Confirme as credenciais em `includes/db.php`
- Execute o script `database.sql` novamente

### Página em Branco
- Verifique se o PHP está ativo no XAMPP
- Consulte os logs de erro do Apache
- Verifique permissões dos arquivos

### Estilos não Carregam
- Confirme se o arquivo `style.css` está na pasta correta
- Verifique o caminho no `includes/header.php`
- Limpe o cache do navegador

## 📱 Recursos do Sistema

- ✅ Design responsivo (mobile-friendly)
- ✅ Validação de formulários
- ✅ Verificação de disponibilidade
- ✅ Cálculo automático de valores
- ✅ Interface administrativa
- ✅ Sistema de alertas
- ✅ Confirmação de ações
- ✅ Formatação de dados brasileira

## 🎨 Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Custom (responsivo)
- **Ícones**: Font Awesome 6.0
- **Servidor**: Apache (XAMPP)

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique este README
2. Consulte os comentários no código
3. Teste em ambiente local primeiro
4. Verifique logs de erro do servidor

---

**Desenvolvido para XAMPP/localhost** 🚀

