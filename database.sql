-- Vitexa Database Schema
-- MySQL 8.0+

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS vitexa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vitexa_db;

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    height INT NOT NULL,
    goal VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);

-- Tabela de planos (treino e dieta)
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('treino', 'dieta') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content JSON NOT NULL,
    status ENUM('ativo', 'inativo', 'concluido') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_type (user_id, type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Tabela de refeições (para planos de dieta detalhados)
CREATE TABLE meals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('cafe_manha', 'lanche_manha', 'almoco', 'lanche_tarde', 'jantar', 'ceia') NOT NULL,
    ingredients JSON NOT NULL,
    calories INT,
    proteins DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fats DECIMAL(5,2),
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    INDEX idx_plan_type (plan_id, type),
    INDEX idx_calories (calories)
);

-- Tabela de mensagens do chat
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    response TEXT,
    type ENUM('user', 'bot') NOT NULL,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_type (type)
);

-- Tabela de progresso do usuário
CREATE TABLE progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    body_fat DECIMAL(4,2),
    muscle_mass DECIMAL(5,2),
    notes TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_user_date (user_id, date),
    INDEX idx_date (date)
);

-- Tabela de exercícios (para planos de treino detalhados)
CREATE TABLE exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    muscle_group VARCHAR(50) NOT NULL,
    sets INT NOT NULL,
    reps VARCHAR(20) NOT NULL,
    weight DECIMAL(5,2),
    rest_time INT, -- em segundos
    instructions TEXT,
    day_of_week TINYINT NOT NULL, -- 1=Segunda, 2=Terça, etc.
    order_in_day TINYINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    INDEX idx_plan_day (plan_id, day_of_week),
    INDEX idx_muscle_group (muscle_group)
);

-- Tabela de lembretes
CREATE TABLE reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('treino', 'dieta', 'pesagem', 'custom') NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    time TIME NOT NULL,
    days_of_week JSON NOT NULL, -- [1,2,3,4,5] para seg-sex
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_active (user_id, is_active),
    INDEX idx_type_time (type, time)
);

-- Tabela de cache (para otimização)
CREATE TABLE cache (
    id VARCHAR(191) PRIMARY KEY,
    data LONGTEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expires (expires_at)
);

-- Tabela de sessões (opcional, para sessões em banco)
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    data TEXT NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at),
    INDEX idx_last_activity (last_activity)
);

-- Garantir que a tabela users use InnoDB
ALTER TABLE users ENGINE=InnoDB;

-- Tabela de recuperação de senha
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alterar a tabela de mensagens do chat
ALTER TABLE messages ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Inserir dados de exemplo para desenvolvimento
INSERT INTO users (name, email, password_hash, age, weight, height, goal) VALUES
('João Silva', 'joao@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 28, 75.5, 175, 'ganhar_massa'),
('Maria Santos', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 25, 62.0, 165, 'perder_peso');

-- Inserir progresso de exemplo
INSERT INTO progress (user_id, weight, body_fat, date) VALUES
(1, 75.5, 15.2, CURDATE() - INTERVAL 30 DAY),
(1, 76.2, 14.8, CURDATE() - INTERVAL 15 DAY),
(1, 76.8, 14.5, CURDATE()),
(2, 62.0, 22.5, CURDATE() - INTERVAL 30 DAY),
(2, 61.2, 21.8, CURDATE() - INTERVAL 15 DAY),
(2, 60.5, 21.2, CURDATE());

-- Inserir lembretes de exemplo
INSERT INTO reminders (user_id, type, title, message, time, days_of_week) VALUES
(1, 'treino', 'Hora do Treino!', 'Não esqueça do seu treino de hoje. Vamos lá!', '07:00:00', '[1,2,3,4,5]'),
(1, 'pesagem', 'Pesagem Semanal', 'Hora de registrar seu peso e acompanhar o progresso.', '06:30:00', '[1]'),
(2, 'treino', 'Treino da Manhã', 'Bom dia! Que tal começar o dia com energia?', '06:00:00', '[1,3,5]'),
(2, 'dieta', 'Lanche da Tarde', 'Não esqueça do seu lanche saudável!', '15:30:00', '[1,2,3,4,5,6,7]');