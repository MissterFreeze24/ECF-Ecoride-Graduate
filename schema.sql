
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','employee','admin') NOT NULL DEFAULT 'user',
  suspended TINYINT(1) NOT NULL DEFAULT 0,
  credits INT NOT NULL DEFAULT 20,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS trips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  driver_id INT NOT NULL,
  depart VARCHAR(100) NOT NULL,
  arrivee VARCHAR(100) NOT NULL,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  price DECIMAL(8,2) NOT NULL,
  places INT NOT NULL,
  vehicle_energy ENUM('Electrique','Essence','Diesel','Hybride') NOT NULL DEFAULT 'Essence',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (driver_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  trip_id INT NOT NULL,
  user_id INT NOT NULL,
  status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (trip_id) REFERENCES trips(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
-- admin: admin@ecoride.local / adminadmin
INSERT INTO users (email, password_hash, role, credits)
VALUES ('admin@ecoride.local', '$2y$10$6exyQ7a.9uIyTgWQe3yDkONr5h7J8kY6e1p0vF6wR1OaV7F2k7o5K', 'admin', 0)
ON DUPLICATE KEY UPDATE email=email;
