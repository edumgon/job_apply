CREATE TABLE IF NOT EXISTS users (
	    id INT AUTO_INCREMENT PRIMARY KEY,
	    name VARCHAR(255) NOT NULL,
	    email VARCHAR(255) UNIQUE NOT NULL,
	    session_token VARCHAR(255) DEFAULT NULL,
	    session_last_active DATETIME DEFAULT NULL,
	    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	);

    DELIMITER //

    CREATE EVENT invalidate_sessions
    ON SCHEDULE EVERY 1 MINUTE
    DO
    BEGIN
        -- Atualiza as sessões inativas (mais de 5 minutos)
        UPDATE users
        SET session_token = NULL,
            session_last_active = NULL
        WHERE session_last_active IS NOT NULL
        AND TIMESTAMPDIFF(MINUTE, session_last_active, NOW()) > 5;
    END //

    DELIMITER ;

	INSERT INTO users (name, email) VALUES
	('John Doe', 'teste@teste.com'),
	('Jane Smith', 'teste2@teste.com');

-- Criação do banco de dados, se não existir
-- CREATE DATABASE IF NOT EXISTS my_database;
-- USE my_database;

-- Criação da tabela `applications`
CREATE TABLE IF NOT EXISTS applications (
    job_link_hash VARCHAR(32) PRIMARY KEY,          -- Link da vaga será a chave primária
    job_link VARCHAR(2083) NOT NULL,          -- Link da vaga será a chave primária
    company_name VARCHAR(255) NOT NULL,          -- Nome da empresa
    job_title VARCHAR(255) NOT NULL,             -- Nome da vaga
    application_date DATE NOT NULL,              -- Data da candidatura
    status ENUM('inicial', 'entrevista', 'proposta', 'negada', 'aprovado') DEFAULT 'inicial', -- Status
    return_date DATE,                            -- Data de retorno
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data de criação do registro
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Última atualização
);

DELIMITER //

CREATE TRIGGER before_insert_users
BEFORE INSERT ON applications
FOR EACH ROW
BEGIN
    SET NEW.job_link_hash = MD5(NEW.job_link);
END //

DELIMITER ;

