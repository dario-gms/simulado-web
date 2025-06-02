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