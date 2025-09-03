-- =============================
-- Réinitialisation complète EcoRide
-- =============================
DROP DATABASE IF EXISTS EcoRide;
CREATE DATABASE EcoRide;
USE EcoRide;

-- =============================
-- Table Utilisateur
-- =============================
CREATE TABLE Utilisateur (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  motdepasse VARCHAR(255) NOT NULL,
  role ENUM('passager','conducteur','admin') DEFAULT 'passager'
);

-- =============================
-- Table Vehicule
-- =============================
CREATE TABLE Vehicule (
  id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
  marque VARCHAR(50) NOT NULL,
  modele VARCHAR(50),
  energie VARCHAR(30),
  id_conducteur INT,
  FOREIGN KEY (id_conducteur) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE
);

-- =============================
-- Table Trajet
-- =============================
CREATE TABLE Trajet (
  id_trajet INT AUTO_INCREMENT PRIMARY KEY,
  depart VARCHAR(100) NOT NULL,
  arrivee VARCHAR(100) NOT NULL,
  date_trajet DATE NOT NULL,
  heureDep TIME NOT NULL,
  heureArr TIME NOT NULL,
  prix DECIMAL(6,2) NOT NULL,
  places INT NOT NULL,
  id_conducteur INT,
  FOREIGN KEY (id_conducteur) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE
);

-- =============================
-- Table Reservation
-- =============================
CREATE TABLE Reservation (
  id_resa INT AUTO_INCREMENT PRIMARY KEY,
  id_passager INT NOT NULL,
  id_trajet INT NOT NULL,
  etat ENUM('confirmee','annulee') DEFAULT 'confirmee',
  FOREIGN KEY (id_passager) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE,
  FOREIGN KEY (id_trajet) REFERENCES Trajet(id_trajet)
    ON DELETE CASCADE
);

-- =============================
-- Table Avis
-- =============================
CREATE TABLE Avis (
  id_avis INT AUTO_INCREMENT PRIMARY KEY,
  note INT CHECK (note >=1 AND note <=5),
  commentaire TEXT,
  id_passager INT NOT NULL,
  id_trajet INT NOT NULL,
  FOREIGN KEY (id_passager) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE,
  FOREIGN KEY (id_trajet) REFERENCES Trajet(id_trajet)
    ON DELETE CASCADE
);

-- =============================
-- Table Message
-- =============================
CREATE TABLE Message (
  id_message INT AUTO_INCREMENT PRIMARY KEY,
  contenu TEXT NOT NULL,
  dateEnvoi DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_expediteur INT NOT NULL,
  id_destinataire INT NOT NULL,
  FOREIGN KEY (id_expediteur) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE,
  FOREIGN KEY (id_destinataire) REFERENCES Utilisateur(id_user)
    ON DELETE CASCADE
);

-- =============================
-- Données d'exemple
-- =============================
INSERT INTO Utilisateur (nom, email, motdepasse, role) VALUES
('Jean Dupont', 'jean.dupont@mail.com', 'pass123', 'passager'),
('Claire Martin', 'claire.martin@mail.com', 'pass123', 'conducteur'),
('Paul Bernard', 'paul.bernard@mail.com', 'pass123', 'conducteur'),
('Admin EcoRide', 'admin@ecoride.com', 'admin123', 'admin');

INSERT INTO Vehicule (marque, modele, energie, id_conducteur) VALUES
('Peugeot', '208', 'Essence', 2),
('Tesla', 'Model 3', 'Electrique', 3);

INSERT INTO Trajet (depart, arrivee, date_trajet, heureDep, heureArr, prix, places, id_conducteur) VALUES
('Paris', 'Versailles', '2025-09-10', '08:30:00', '09:15:00', 8.50, 3, 2),
('Paris', 'Fontainebleau', '2025-09-11', '07:00:00', '08:30:00', 10.00, 2, 3),
('Saint-Denis', 'Paris', '2025-09-12', '18:00:00', '18:45:00', 6.00, 4, 2);

INSERT INTO Reservation (id_passager, id_trajet, etat) VALUES
(1, 1, 'confirmee'),
(1, 2, 'confirmee');

INSERT INTO Avis (note, commentaire, id_passager, id_trajet) VALUES
(5, 'Claire est super sympa et ponctuelle !', 1, 1),
(4, 'Trajet agréable, conducteur prudent.', 1, 2);

INSERT INTO Message (contenu, id_expediteur, id_destinataire) VALUES
('Bonjour Claire, est-ce que vous passez par la gare ?', 1, 2),
('Oui Jean, je peux vous prendre devant la gare.', 2, 1),
('Bonjour Paul, est-ce que vous acceptez les animaux ?', 1, 3),
('Oui, les petits chiens en cage sont acceptés.', 3, 1);

