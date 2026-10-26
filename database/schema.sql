-- Compatível com MariaDB 10.4 e MySQL 5.7/8.x.
-- Reimportar não apaga dados nem atualiza estruturas já existentes.
CREATE DATABASE IF NOT EXISTS db_foco
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_foco;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(254) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL COMMENT 'Hash gerado por password_hash; nunca senha em texto puro',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tarefas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT UNSIGNED NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NULL,
    prazo DATE NULL,
    prioridade ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    categoria ENUM('studies', 'work', 'personal') NULL DEFAULT NULL,
    status ENUM('pending', 'in-progress', 'completed') NOT NULL DEFAULT 'pending',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tarefas_usuario_id (usuario_id),
    CONSTRAINT fk_tarefas_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
