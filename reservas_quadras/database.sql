CREATE DATABASE IF NOT EXISTS lestoplay2025;
USE lestoplay2025;

CREATE TABLE IF NOT EXISTS CLIENTE (
	IDCLIENTE INT PRIMARY KEY AUTO_INCREMENT,
	NOME VARCHAR (50),
	EMAIL VARCHAR (50),
	SENHA VARCHAR (50),
	TELEFONE VARCHAR (20)
);



CREATE TABLE IF NOT EXISTS TIPOESPORTE (
	IDESPORTE INT PRIMARY KEY AUTO_INCREMENT,
	MODALIDADE VARCHAR (50)CREATE TABLE IF NOT EXISTS EQUIPAMENTO (
    IDEQUIP INT PRIMARY KEY AUTO_INCREMENT,
    NOME VARCHAR(100) NOT NULL UNIQUE,
    QUANTIDADE INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS quadras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    localizacao VARCHAR(200) NOT NULL,
    preco_hora DECIMAL(10, 2) NOT NULL DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS RESERVAS (
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

INSERT INTO quadras (nome, localizacao, preco_hora) VALUES
("Quadra Principal", "Rua da Praia, 100", 50.00),
("Quadra Secundária", "Avenida do Sol, 250", 60.00);

-- Inserir algumas reservas de exemplo (ajuste os IDs conforme o auto_increment)
INSERT INTO RESERVAS (HORARIO, DATA, PRECO, STATUS, IDCLIENTE, IDESPORTE, IDEQUIP, IDQUADRA) VALUES
("10:00:00", "2025-09-01", 50.00, "ativa", 1, 1, 1, 1),
("14:00:00", "2025-09-01", 60.00, "ativa", 2, 2, 2, 2),
("16:00:00", "2025-09-02", 55.00, "ativa", 1, 3, 3, 1);



-- Tabela para avaliações das quadras
CREATE TABLE IF NOT EXISTS AVALIACOES (
    IDAVALIACAO INT PRIMARY KEY AUTO_INCREMENT,
    IDCLIENTE INT,
    IDQUADRA INT,
    IDRESERVA INT,
    NOTA INT CHECK (NOTA >= 1 AND NOTA <= 5),
    COMENTARIO TEXT,
    DATA_AVALIACAO TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IDCLIENTE) REFERENCES CLIENTE(IDCLIENTE),
    FOREIGN KEY (IDQUADRA) REFERENCES quadras(id),
    FOREIGN KEY (IDRESERVA) REFERENCES RESERVAS(IDRESERVA),
    UNIQUE KEY unique_avaliacao_reserva (IDRESERVA)
);

-- Inserir algumas avaliações de exemplo
INSERT INTO AVALIACOES (IDCLIENTE, IDQUADRA, IDRESERVA, NOTA, COMENTARIO) VALUES
(1, 1, 1, 5, "Excelente quadra! Muito bem cuidada e com ótima iluminação."),
(2, 2, 2, 4, "Boa quadra, mas poderia ter melhor vestiário."),
(1, 1, 3, 5, "Sempre uma experiência incrível jogar aqui!");


-- Tabela para log de mensagens WhatsApp
CREATE TABLE IF NOT EXISTS LOG_WHATSAPP (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    telefone VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    status ENUM('enviado', 'erro', 'pendente') DEFAULT 'pendente',
    erro TEXT,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adicionar alguns logs de exemplo
INSERT INTO LOG_WHATSAPP (telefone, mensagem, status) VALUES
('(11) 98765-4321', 'Reserva confirmada para João Silva', 'enviado'),
('(21) 99876-5432', 'Lembrete de reserva para Maria Souza', 'enviado');

