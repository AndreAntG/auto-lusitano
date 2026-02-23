-- Car Stand Virtual Database
-- Database for online car buying, selling, and renting system

DROP DATABASE IF EXISTS car_stand;
CREATE DATABASE car_stand CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE car_stand;

-- Customer table (customers who can be buyers, sellers, or renters)
CREATE TABLE customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) NOT NULL DEFAULT 1
) DEFAULT CHARSET utf8mb4;

-- Cars table (inventory of cars available for sale or rent)
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_filename VARCHAR(255), -- Filename of the car's image
    status ENUM('available', 'sold', 'rented') DEFAULT 'available',
    owner_id INT, -- Customer who owns the car (for customer-listed cars)
    is_for_sale BOOLEAN DEFAULT FALSE,
    is_for_rent BOOLEAN DEFAULT FALSE,
    daily_rent_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES customer(id) ON DELETE SET NULL
) DEFAULT CHARSET utf8mb4;

-- Sales table (records of car purchases)
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    seller_id INT NOT NULL,
    buyer_id INT NOT NULL,
    sale_price DECIMAL(10,2) NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id),
    FOREIGN KEY (seller_id) REFERENCES customer(id),
    FOREIGN KEY (buyer_id) REFERENCES customer(id)
) DEFAULT CHARSET utf8mb4;

-- Rentals table (records of car rentals)
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    renter_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id),
    FOREIGN KEY (renter_id) REFERENCES customer(id)
) DEFAULT CHARSET utf8mb4;

-- Users table (for application authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Will store hashed passwords
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'user') DEFAULT 'user',
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) DEFAULT CHARSET utf8mb4;

-- Reset auto-increment counters before inserting data
ALTER TABLE customer AUTO_INCREMENT = 1;
ALTER TABLE cars AUTO_INCREMENT = 1;
ALTER TABLE sales AUTO_INCREMENT = 1;
ALTER TABLE rentals AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;

-- Insert some sample data
INSERT INTO customer (name, email, phone, address, status) VALUES
('João Silva', 'joao@example.com', '123456789', 'Rua A, 123', true),
('Maria Santos', 'maria@example.com', '987654321', 'Rua B, 456', false),
('Auto Lusitano', 'info@autolusitano.pt', '213456789', 'Avenida da Liberdade, 150, Lisboa', true),
('Carlos Ferreira', 'carlos.ferreira@email.pt', '912345678', 'Rua das Flores, 45, Porto', true),
('Ana Rodrigues', 'ana.rodrigues@email.pt', '917654321', 'Praça do Comércio, 25, Lisboa', true),
('Pedro Almeida', 'pedro.almeida@email.pt', '925678901', 'Avenida dos Aliados, 200, Porto', true),
('Sofia Costa', 'sofia.costa@email.pt', '936789012', 'Rua do Carmo, 10, Coimbra', true),
('Miguel Pereira', 'miguel.pereira@email.pt', '947890123', 'Largo do Rossio, 5, Lisboa', true),
('Inês Oliveira', 'ines.oliveira@email.pt', '958901234', 'Rua Santa Catarina, 150, Porto', true),
('Tiago Sousa', 'tiago.sousa@email.pt', '969012345', 'Avenida Almirante Reis, 75, Lisboa', true),
('Beatriz Martins', 'beatriz.martins@email.pt', '971234567', 'Rua Formosa, 30, Coimbra', true),
('Rui Gonçalves', 'rui.goncalves@email.pt', '982345678', 'Praça da República, 50, Porto', true),
('Luís Fernandes', 'luis.fernandes@email.pt', '993456789', 'Rua dos Clérigos, 15, Porto', true),
('Catarina Lima', 'catarina.lima@email.pt', '914567890', 'Avenida da Boavista, 300, Porto', true),
('André Martins', 'andre.martins@email.pt', '925678901', 'Rua do Almada, 25, Coimbra', true),
('Filipa Silva', 'filipa.silva@email.pt', '936789012', 'Largo da Porta Férrea, 10, Coimbra', true),
('João Pereira', 'joao.pereira@email.pt', '947890123', 'Rua Ferreira Borges, 50, Coimbra', true),
('Mariana Costa', 'mariana.costa@email.pt', '958901234', 'Avenida Sá da Bandeira, 100, Coimbra', true),
('Ricardo Santos', 'ricardo.santos@email.pt', '969012345', 'Praça 8 de Maio, 20, Coimbra', true),
('Carla Oliveira', 'carla.oliveira@email.pt', '971123456', 'Rua da Sofia, 30, Coimbra', true),
('Hugo Rodrigues', 'hugo.rodrigues@email.pt', '982234567', 'Avenida Emídio Navarro, 75, Coimbra', true),
('Patrícia Ferreira', 'patricia.ferreira@email.pt', '993345678', 'Rua do Brasil, 45, Coimbra', true),
('Diogo Almeida', 'diogo.almeida@email.pt', '914456789', 'Praça da Canção, 15, Coimbra', true),
('Sandra Pereira', 'sandra.pereira@email.pt', '925567890', 'Rua do Penedo da Saudade, 80, Coimbra', true),
('Bruno Costa', 'bruno.costa@email.pt', '936678901', 'Avenida Fernão de Magalhães, 200, Coimbra', true),
('Teresa Sousa', 'teresa.sousa@email.pt', '947789012', 'Rua das Azevinhas, 35, Coimbra', true),
('Fábio Martins', 'fabio.martins@email.pt', '958890123', 'Praça do Município, 5, Coimbra', true),
('Susana Silva', 'susana.silva@email.pt', '969901234', 'Avenida Bissaya Barreto, 150, Coimbra', true),
('Gonçalo Pereira', 'goncalo.pereira@email.pt', '971012345', 'Rua do Comércio, 25, Coimbra', true),
('Helena Costa', 'helena.costa@email.pt', '982123456', 'Largo do Poço, 10, Coimbra', true),
('Daniel Rodrigues', 'daniel.rodrigues@email.pt', '993234567', 'Rua Visconde da Luz, 40, Coimbra', true),
('Isabel Fernandes', 'isabel.fernandes@email.pt', '914345678', 'Avenida Almirante Gago Coutinho, 50, Coimbra', true),
('Marco Oliveira', 'marco.oliveira@email.pt', '925456789', 'Praça da República, 30, Coimbra', true),
('Cristina Santos', 'cristina.santos@email.pt', '936567890', 'Rua António Granjo, 15, Coimbra', true),
('Paulo Almeida', 'paulo.almeida@email.pt', '947678901', 'Avenida João Jacinto de Magalhães, 75, Coimbra', true),
('Mónica Pereira', 'monica.pereira@email.pt', '958789012', 'Rua do Arco da Traição, 20, Coimbra', true),
('Nuno Costa', 'nuno.costa@email.pt', '969890123', 'Praça do Comércio, 40, Coimbra', true),
('Lúcia Rodrigues', 'lucia.rodrigues@email.pt', '971901234', 'Avenida Sá da Bandeira, 25, Coimbra', true),
('José Ferreira', 'jose.ferreira@email.pt', '982012345', 'Rua da Sofia, 55, Coimbra', true),
('Alice Silva', 'alice.silva@email.pt', '993123456', 'Praça 8 de Maio, 35, Coimbra', true),
('Vítor Pereira', 'vitor.pereira@email.pt', '914234567', 'Avenida Emídio Navarro, 90, Coimbra', true),
('Sónia Costa', 'sonia.costa@email.pt', '925345678', 'Rua do Brasil, 60, Coimbra', true),
('Manuel Rodrigues', 'manuel.rodrigues@email.pt', '936456789', 'Praça da Canção, 25, Coimbra', true),
('Rosa Fernandes', 'rosa.fernandes@email.pt', '947567890', 'Rua do Penedo da Saudade, 95, Coimbra', true),
('António Oliveira', 'antonio.oliveira@email.pt', '958678901', 'Avenida Fernão de Magalhães, 175, Coimbra', true),
('Conceição Santos', 'conceicao.santos@email.pt', '969789012', 'Rua das Azevinhas, 50, Coimbra', true),
('Fernando Almeida', 'fernando.almeida@email.pt', '971890123', 'Praça do Município, 15, Coimbra', true),
('Margarida Pereira', 'margarida.pereira@email.pt', '982901234', 'Avenida Bissaya Barreto, 125, Coimbra', true),
('Eduardo Costa', 'eduardo.costa@email.pt', '993012345', 'Rua do Comércio, 35, Coimbra', true),
('Cláudia Rodrigues', 'claudia.rodrigues@email.pt', '914123456', 'Largo do Poço, 20, Coimbra', true),
('Ruben Silva', 'ruben.silva@email.pt', '925234567', 'Rua Visconde da Luz, 55, Coimbra', true),
('Paula Fernandes', 'paula.fernandes@email.pt', '936345678', 'Avenida Almirante Gago Coutinho, 65, Coimbra', true),
('Jorge Oliveira', 'jorge.oliveira@email.pt', '947456789', 'Praça da República, 45, Coimbra', true),
('Fátima Santos', 'fatima.santos@email.pt', '958567890', 'Rua António Granjo, 30, Coimbra', true),
('Augusto Almeida', 'augusto.almeida@email.pt', '969678901', 'Avenida João Jacinto de Magalhães, 85, Coimbra', true),
('Gabriela Pereira', 'gabriela.pereira@email.pt', '971789012', 'Rua do Arco da Traição, 35, Coimbra', true),
('Roberto Costa', 'roberto.costa@email.pt', '982890123', 'Praça do Comércio, 55, Coimbra', true),
('Elisa Rodrigues', 'elisa.rodrigues@email.pt', '993901234', 'Avenida Sá da Bandeira, 40, Coimbra', true),
('Adriano Ferreira', 'adriano.ferreira@email.pt', '914012345', 'Rua da Sofia, 70, Coimbra', true),
('Natália Silva', 'natalia.silva@email.pt', '925123456', 'Praça 8 de Maio, 50, Coimbra', true),
('Leonardo Pereira', 'leonardo.pereira@email.pt', '936234567', 'Avenida Emídio Navarro, 110, Coimbra', true),
('Raquel Costa', 'raquel.costa@email.pt', '947345678', 'Rua do Brasil, 75, Coimbra', true),
('Artur Rodrigues', 'artur.rodrigues@email.pt', '958456789', 'Praça da Canção, 40, Coimbra', true),
('Lurdes Fernandes', 'lurdes.fernandes@email.pt', '969567890', 'Rua do Penedo da Saudade, 110, Coimbra', true),
('Sebastião Oliveira', 'sebastiao.oliveira@email.pt', '971678901', 'Avenida Fernão de Magalhães, 150, Coimbra', true),
('Albertina Santos', 'albertina.santos@email.pt', '982789012', 'Rua das Azevinhas, 65, Coimbra', true),
('Horácio Almeida', 'horacio.almeida@email.pt', '993890123', 'Praça do Município, 25, Coimbra', true),
('Glória Pereira', 'gloria.pereira@email.pt', '914901234', 'Avenida Bissaya Barreto, 100, Coimbra', true),
('Ivo Costa', 'ivo.costa@email.pt', '925012345', 'Rua do Comércio, 45, Coimbra', true),
('Olívia Rodrigues', 'olivia.rodrigues@email.pt', '936123456', 'Largo do Poço, 30, Coimbra', true);

INSERT INTO cars (make, model, year, price, description, is_for_sale, is_for_rent, daily_rent_price, owner_id) VALUES
('Toyota', 'Corolla', 2020, 25000.00, 'Sedan confiável', TRUE, TRUE, 50.00, 1),
('BMW', 'X3', 2021, 45000.00, 'SUV de luxo', FALSE, TRUE, 100.00, NULL),
('Mercedes-Benz', 'C-Class', 2022, 55000.00, 'Sedan executivo premium', TRUE, TRUE, 120.00, 3),
('Volkswagen', 'Golf', 2021, 28000.00, 'Hatchback versátil', TRUE, TRUE, 60.00, 3),
('Audi', 'A4', 2020, 42000.00, 'Sedan elegante', TRUE, FALSE, NULL, 4),
('Renault', 'Clio', 2022, 18000.00, 'Citadino económico', TRUE, TRUE, 35.00, 5),
('Peugeot', '308', 2021, 26000.00, 'Familiar confortável', TRUE, TRUE, 55.00, 6),
('Opel', 'Corsa', 2023, 16000.00, 'Citadino moderno', TRUE, FALSE, NULL, 7),
('Ford', 'Focus', 2020, 24000.00, 'Hatchback dinâmico', TRUE, TRUE, 50.00, 8),
('Nissan', 'Qashqai', 2021, 32000.00, 'SUV compacto', TRUE, TRUE, 70.00, 9),
('Seat', 'Leon', 2022, 27000.00, 'Hatchback desportivo', TRUE, FALSE, NULL, 10),
('Kia', 'Sportage', 2021, 35000.00, 'SUV familiar', FALSE, TRUE, 80.00, 3),
('Hyundai', 'Tucson', 2020, 33000.00, 'SUV espaçoso', TRUE, TRUE, 75.00, 11),
('Dacia', 'Duster', 2022, 22000.00, 'SUV acessível', TRUE, TRUE, 45.00, 12),
('Fiat', '500', 2023, 19000.00, 'Citadino icónico', TRUE, TRUE, 40.00, 13),
('Jeep', 'Renegade', 2021, 29000.00, 'SUV aventureiro', TRUE, TRUE, 65.00, 14),
('Mazda', 'CX-5', 2020, 31000.00, 'SUV premium', TRUE, FALSE, NULL, 15),
('Subaru', 'Forester', 2022, 34000.00, 'SUV robusto', TRUE, TRUE, 75.00, 16),
('Tesla', 'Model 3', 2023, 45000.00, 'Sedan elétrico', TRUE, FALSE, NULL, 17),
('Volvo', 'XC40', 2021, 38000.00, 'SUV seguro', TRUE, TRUE, 85.00, 18),
('Land Rover', 'Discovery', 2020, 52000.00, 'SUV de luxo', FALSE, TRUE, 150.00, 3),
('Porsche', 'Cayenne', 2022, 85000.00, 'SUV desportivo', TRUE, FALSE, NULL, 19),
('Jaguar', 'F-Pace', 2021, 48000.00, 'SUV elegante', TRUE, TRUE, 110.00, 20),
('Mini', 'Cooper', 2023, 23000.00, 'Citadino premium', TRUE, TRUE, 55.00, 21),
('Smart', 'Fortwo', 2022, 14000.00, 'Micro citadino', TRUE, TRUE, 30.00, 22),
('Citroën', 'C3', 2023, 17000.00, 'Citadino francês', TRUE, FALSE, NULL, 23),
('Peugeot', '2008', 2022, 24000.00, 'SUV citadino', TRUE, TRUE, 50.00, 24),
('Renault', 'Captur', 2021, 23000.00, 'SUV urbano', TRUE, TRUE, 48.00, 25),
('Opel', 'Mokka', 2023, 21000.00, 'SUV compacto', TRUE, FALSE, NULL, 26),
('Ford', 'Puma', 2022, 22000.00, 'SUV citadino', TRUE, TRUE, 45.00, 27),
('Nissan', 'Juke', 2021, 20000.00, 'SUV jovem', TRUE, TRUE, 42.00, 28),
('Kia', 'Stonic', 2023, 19000.00, 'SUV acessível', TRUE, FALSE, NULL, 29),
('Hyundai', 'Kona', 2022, 25000.00, 'SUV moderno', TRUE, TRUE, 52.00, 30),
('Toyota', 'Yaris', 2023, 16000.00, 'Citadino híbrido', TRUE, TRUE, 35.00, 31),
('Honda', 'Jazz', 2022, 18000.00, 'Citadino versátil', TRUE, FALSE, NULL, 32),
('Mazda', 'Mazda2', 2023, 17000.00, 'Citadino premium', TRUE, TRUE, 38.00, 33),
('Volkswagen', 'Polo', 2022, 19000.00, 'Citadino alemão', TRUE, TRUE, 40.00, 34),
('Seat', 'Ibiza', 2023, 18000.00, 'Citadino espanhol', TRUE, FALSE, NULL, 35),
('Skoda', 'Fabia', 2022, 17000.00, 'Citadino checo', TRUE, TRUE, 36.00, 36),
('Dacia', 'Sandero', 2023, 13000.00, 'Citadino económico', TRUE, FALSE, NULL, 37),
('Renault', 'Twingo', 2023, 11000.00, 'Micro citadino', TRUE, TRUE, 25.00, 39),
('Peugeot', '108', 2022, 13000.00, 'Citadino francês', TRUE, FALSE, NULL, 40),
('Citroën', 'C1', 2023, 12500.00, 'Citadino francês', TRUE, TRUE, 27.00, 41);


INSERT INTO cars (make, model, year, price, description, image, status, owner_id, is_for_sale, is_for_rent, daily_rent_price, created_at, image_filename) VALUES
('Honda', 'Civic', '2019', '24000.00', 'Carro desportivo', NULL, 'available', '2', '1', '0', NULL, '2026-02-22 21:38:53', 'car_128_1771808139.png'),
('BMW', 'X3', '2021', '49000.00', 'SUV de luxo', NULL, 'available', NULL, '0', '1', '100.00', '2026-02-22 21:38:53', 'car_129_1771808105.png'),
('Mercedes-Benz', 'C-Class', '2022', '90900.00', 'Sedan executivo premium', NULL, 'available', '3', '1', '1', '120.00', '2026-02-22 21:38:53', 'car_130_1771808364.png'),
('Audi', 'A4', '2020', '92000.00', 'Sedan elegante', NULL, 'available', '4', '1', '0', NULL, '2026-02-22 21:38:53', 'car_132_1771808070.png'),
('Peugeot', '308', '2021', '36000.00', 'Familiar confortável', NULL, 'available', '6', '1', '1', '55.00', '2026-02-22 21:38:53', 'car_134_1771808033.png'),
('Jaguar', 'F-Pace', '2021', '68000.00', 'SUV elegante', NULL, 'available', '20', '1', '1', '110.00', '2026-02-22 21:38:53', 'car_150_1771808295.png'),
('Fiat', 'Panda', '2022', '32000.00', 'Citadino italiano', NULL, 'available', '38', '1', '1', '28.00', '2026-02-22 21:38:53', 'car_168_1771808172.png');

INSERT INTO rentals (car_id, renter_id, start_date, end_date, total_price, status) VALUES
(1, 1, '2026-02-01', '2026-02-05', 250.00, 'completed'),
(2, 2, '2026-02-10', '2026-02-15', 300.00, 'active'),
(3, 3, '2026-01-20', '2026-01-25', 500.00, 'completed'),
(4, 4, '2026-02-20', '2026-02-25', 600.00, 'active'),
(5, 5, '2026-03-01', '2026-03-07', 420.00, 'active'),
(6, 6, '2026-01-15', '2026-01-20', 300.00, 'cancelled');

-- Insert sample users (passwords are hashed using password_hash() in PHP)
-- Default password for all users is 'password123' (hashed)
INSERT INTO users (username, password, email, role, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@autolusitano.pt', 'admin', 1), -- password: password123
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager@autolusitano.pt', 'manager', 1), -- password: password123
('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user@autolusitano.pt', 'user', 1); -- password: password123

-- Users table (for application authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Will store hashed passwords
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'user') DEFAULT 'user',
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) DEFAULT CHARSET utf8mb4;
