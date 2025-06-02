CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enunciado TEXT NOT NULL,
    resposta_correta CHAR(1) NOT NULL,
    area_id INT NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id)
);

CREATE TABLE options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    letra CHAR(1) NOT NULL,
    texto TEXT NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

CREATE TABLE resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tempo_total TIME NOT NULL,
    pontuacao INT NOT NULL,
    data_realizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE respostas_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resultado_id INT NOT NULL,
    question_id INT NOT NULL,
    resposta CHAR(1) NOT NULL,
    acertou BOOLEAN NOT NULL,
    tempo_gasto TIME NOT NULL,
    FOREIGN KEY (resultado_id) REFERENCES resultados(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Atualização da tabela resultados para incluir user_id
ALTER TABLE resultados ADD COLUMN user_id INT;
ALTER TABLE resultados ADD FOREIGN KEY (user_id) REFERENCES users(id);

-- Tabela para histórico de desempenho por área
CREATE TABLE user_area_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    area_id INT NOT NULL,
    total_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    last_attempt TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (area_id) REFERENCES areas(id),
    UNIQUE KEY (user_id, area_id)
);

CREATE TABLE simulado_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    areas TEXT NOT NULL, -- JSON array das áreas selecionadas
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    time_spent INT NOT NULL, -- em segundos
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE simulado_area_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simulado_id INT NOT NULL,
    area_id INT NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    FOREIGN KEY (simulado_id) REFERENCES simulado_results(id) ON DELETE CASCADE,
    FOREIGN KEY (area_id) REFERENCES areas(id)
);