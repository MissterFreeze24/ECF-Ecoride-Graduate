-- init_db.sql for EcoRide
CREATE DATABASE IF NOT EXISTS ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecoride;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pseudo VARCHAR(50) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  credits DECIMAL(10,2) DEFAULT 0,
  role ENUM('user','employee','admin','suspended') DEFAULT 'user',
  photo VARCHAR(255) DEFAULT NULL,
  note_moyenne DECIMAL(3,2) DEFAULT 0,
  created_at DATETIME,
  INDEX(pseudo)
);
CREATE TABLE api_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  created_at DATETIME,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX(token)
);
CREATE TABLE vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  plaque VARCHAR(20),
  date_premiere_imm DATE,
  marque VARCHAR(100),
  modele VARCHAR(100),
  couleur VARCHAR(50),
  energie ENUM('essence','diesel','electrique','hybride') DEFAULT 'essence',
  places INT DEFAULT 4,
  preferences JSON DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE rides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chauffeur_id INT NOT NULL,
  vehicle_id INT DEFAULT NULL,
  depart_city VARCHAR(150),
  arrivee_city VARCHAR(150),
  date_depart DATE,
  time_depart TIME,
  time_arrivee TIME,
  duree_min INT DEFAULT NULL,
  places_total INT DEFAULT 1,
  places_restantes INT DEFAULT 1,
  prix DECIMAL(10,2) DEFAULT 0,
  ecologique TINYINT(1) DEFAULT 0,
  status ENUM('scheduled','started','finished','cancelled') DEFAULT 'scheduled',
  created_at DATETIME,
  FOREIGN KEY (chauffeur_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
  INDEX(depart_city),
  INDEX(arrivee_city),
  INDEX(date_depart)
);
CREATE TABLE participations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ride_id INT NOT NULL,
  user_id INT NOT NULL,
  seats INT DEFAULT 1,
  cancelled TINYINT(1) DEFAULT 0,
  created_at DATETIME,
  FOREIGN KEY (ride_id) REFERENCES rides(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ride_id INT NOT NULL,
  user_id INT NOT NULL,
  note TINYINT NOT NULL,
  comment TEXT,
  validated TINYINT(1) DEFAULT 0,
  created_at DATETIME,
  FOREIGN KEY (ride_id) REFERENCES rides(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE admin_actions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT,
  action_type VARCHAR(100),
  details TEXT,
  created_at DATETIME,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
);
INSERT INTO users (pseudo, email, password_hash, credits, role, created_at) VALUES ('admin', 'admin@ecoride.local', '$2y$10$wVbZ0h1yRk0Iu0kq2h3uDOzY4yQHhQ0vN2lX2Jb9Qz0h3yYbq1G8.', 0, 'admin', NOW());
INSERT INTO users (pseudo, email, password_hash, credits, role, created_at) VALUES ('alice', 'alice@example.com', '$2y$10$7iQZcf8dK7a3gHh4tJp6Oe9YzQmVb3Xk9z1q2w3e4r5t6y7u8i9o0', 20, 'user', NOW());
INSERT INTO rides (chauffeur_id, vehicle_id, depart_city, arrivee_city, date_depart, time_depart, time_arrivee, places_total, places_restantes, prix, ecologique, created_at) VALUES (2, NULL, 'Paris', 'Lille', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', '16:30:00', 3, 3, 15.00, 1, NOW());
