CREATE DATABASE api_cadastro;

USE api_cadastro;

CREATE TABLE pessoas (
    codigo INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    idade INT(3) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    email VARCHAR(200) NOT NULL,
    foto VARCHAR(200)
);
