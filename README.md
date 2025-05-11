database structure 
show databases;
use newprjt;
-- Create the database
CREATE DATABASE MilitaryInstituteProjects;
USE MilitaryInstituteProjects;

CREATE TABLE Unit (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL CHECK (role IN ('admin', 'encadrant', 'eleve')),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Encadrant table (supervisors)
CREATE TABLE Encadrant (
    matricule VARCHAR(4) PRIMARY KEY CHECK (matricule REGEXP '^[0-9]{4}$'),
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    grade ENUM('Colonel', 'Lieutenant Colonel', 'Commandant', 'Capitaine', 'Lieutenant', 
              'Sous-Lieutenant', 'Adjudant', 'Sergent', 'Caporal', 'Soldat') NOT NULL,
    fonction VARCHAR(100),
    unit_id INT,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES Unit(unit_id)
);

-- Create Class table
CREATE TABLE Class (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Create Student table
CREATE TABLE Student (
    matricule VARCHAR(4) PRIMARY KEY CHECK (matricule REGEXP '^[0-9]{4}$'),
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    class_id INT,
    unit_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES Class(class_id),
    FOREIGN KEY (unit_id) REFERENCES Unit(unit_id)
);

-- Create Project table
CREATE TABLE Project (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    specialite ENUM('genie civil', 'telecomunication', 'electomecanique', 'genie informatique') NOT NULL,
    nombre_eleves ENUM('monome', 'binome') NOT NULL,
    encadrant_matricule VARCHAR(4),
    organisme_or_address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    description TEXT NOT NULL,
    objectif TEXT NOT NULL,
    resultats_attendus TEXT NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (encadrant_matricule) REFERENCES Encadrant(matricule)
);

-- Create Reservation table
CREATE TABLE Reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    student1_matricule VARCHAR(4),
    student1_class_id INT,
    student2_matricule VARCHAR(4),
    student2_class_id INT,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT NULL,
    rejection_reason TEXT,
    approved_by VARCHAR(4),
    approval_date datetime,
    FOREIGN KEY (project_id) REFERENCES Project(project_id),
    FOREIGN KEY (student1_matricule) REFERENCES Student(matricule),
    FOREIGN KEY (student1_class_id) REFERENCES Class(class_id),
    FOREIGN KEY (student2_matricule) REFERENCES Student(matricule),
    FOREIGN KEY (student2_class_id) REFERENCES Class(class_id),
    FOREIGN KEY (approved_by) REFERENCES Encadrant(matricule)
);

-- Create Admin table
CREATE TABLE Admin (
    matricule VARCHAR(4) PRIMARY KEY CHECK (matricule REGEXP '^[0-9]{4}$'),
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    grade ENUM('Colonel', 'Lieutenant Colonel', 'Commandant', 'Capitaine', 'Lieutenant', 
              'Sous-Lieutenant', 'Adjudant', 'Sergent', 'Caporal', 'Soldat') NOT NULL,
    unit_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES Unit(unit_id)
);

-- Create ProjectHistory table for tracking changes
CREATE TABLE ProjectHistory (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    changed_by VARCHAR(4), -- Could be encadrant or admin matricule
    change_type VARCHAR(20) NOT NULL, -- 'create', 'update', 'delete'
    change_details JSON,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id)
);
show tables;
-- Insertion dans la table Unit
INSERT INTO Unit (unit_name, role, password_hash) VALUES
('Unité Commandement', 'admin', '$2a$10$xJwL5v5Jz5U6Zz5U6Zz5Ue'),
('Unité Génie Informatique', 'encadrant', '$2a$10$yKvM6wN7a8B9c0D1E2F3G'),
('Unité Télécommunications', 'encadrant', '$2a$10$zLwN7xO8p9Q0R1S2T3U4V'),
('Promotion 2023', 'eleve', '$2a$10$aBcD1E2F3G4H5I6J7K8L9');

-- Insertion dans la table Class
INSERT INTO Class (class_name, description) VALUES
('GI-23', 'Promotion 2023 Génie Informatique'),
('TLC-23', 'Promotion 2023 Télécommunications'),
('GC-23', 'Promotion 2023 Génie Civil'),
('EM-23', 'Promotion 2023 Electromécanique');

-- Insertion dans la table Encadrant
INSERT INTO Encadrant (matricule, nom, prenom, grade, fonction, unit_id, email, phone) VALUES
('1001', 'Dupont', 'Pierre', 'Colonel', 'Chef de département', 2, 'p.dupont@mil.ac', '0612345678'),
('1002', 'Martin', 'Sophie', 'Commandant', 'Responsable projets', 3, 's.martin@mil.ac', '0623456789'),
('1003', 'Bernard', 'Luc', 'Capitaine', 'Encadrant technique', 2, 'l.bernard@mil.ac', '0634567890');

-- Insertion dans la table Admin
INSERT INTO Admin (matricule, nom, prenom, grade, unit_id) VALUES
('0001', 'Leblanc', 'Jean', 'Général', 1),
('0002', 'Dubois', 'Marie', 'Colonel', 1);

-- Insertion dans la table Student
INSERT INTO Student (matricule, nom, prenom, class_id, unit_id) VALUES
('2001', 'Benali', 'Karim', 1, 4),
('2002', 'Petit', 'Emma', 1, 4),
('2003', 'Rousseau', 'Thomas', 2, 4),
('2004', 'Nguyen', 'Linh', 3, 4),
('2005', 'Garcia', 'Carlos', 4, 4),
('2006', 'Diallo', 'Aminata', 1, 4);

-- Insertion dans la table Project
INSERT INTO Project (titre, specialite, nombre_eleves, encadrant_matricule, organisme_or_address, 
                    phone, email, description, objectif, resultats_attendus) VALUES
('Système de gestion des projets', 'genie informatique', 'binome', '1001', 
 'Institut Militaire', '0612345678', 'projet1@mil.ac', 
 'Développement d''une application web pour la gestion des projets de fin d''études',
 'Automatiser le processus de gestion des projets', 
 'Application fonctionnelle avec interface administrateur et étudiant'),

('Réseau sécurisé pour communications militaires', 'telecomunication', 'monome', '1002',
 'Unité Télécoms', '0623456789', 'projet2@mil.ac',
 'Mise en place d''un protocole de communication sécurisé',
 'Améliorer la sécurité des communications sur le terrain',
 'Protocole validé et documenté'),

('Robot de surveillance autonome', 'electomecanique', 'binome', '1003',
 'Laboratoire Robotique', '0634567890', 'projet3@mil.ac',
 'Conception d''un robot autonome pour la surveillance de zones militaires',
 'Automatiser les patrouilles de surveillance',
 'Prototype fonctionnel avec détection d''intrus');

-- Insertion dans la table Reservation
INSERT INTO Reservation (project_id, student1_matricule, student1_class_id, 
                        student2_matricule, student2_class_id, is_approved, approved_by) VALUES
(1, '2001', 1, '2002', 1, TRUE, '0001'),
(3, '2005', 4, '2006', 1, FALSE, '0002');

-- Insertion dans la table ProjectHistory
INSERT INTO ProjectHistory (project_id, changed_by, change_type, change_details) VALUES
(1, '1001', 'create', '{"titre": "Système de gestion des projets", "status": "created"}'),
(1, '0001', 'update', '{"champ": "is_available", "ancienne_valeur": true, "nouvelle_valeur": false}'),
(2, '1002', 'create', '{"titre": "Réseau sécurisé pour communications militaires", "status": "created"}');

ALTER TABLE Reservation
DROP FOREIGN KEY reservation_ibfk_6;

ALTER TABLE Reservation
ADD CONSTRAINT fk_approved_by_admin
FOREIGN KEY (approved_by) REFERENCES Admin(matricule);
show databases;
use newprjt;
select * from encadrant ;
select * from unit;
select * from student;
select * from unit;
DELETE FROM student WHERE matricule=2001;
INSERT INTO Unit (unit_name, role, password_hash) VALUES
('hopital', 'eleve', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO Student (matricule, nom, prenom, class_id, unit_id) VALUES
('2233', 'Diallo', 'Aminata', 1, 5);

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Delete data from tables with foreign key dependencies first
TRUNCATE TABLE ProjectHistory;
TRUNCATE TABLE Reservation;
TRUNCATE TABLE Project;
TRUNCATE TABLE Student;
TRUNCATE TABLE Admin;
TRUNCATE TABLE Encadrant;
TRUNCATE TABLE Class;
TRUNCATE TABLE Unit;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
select * from unit;
-- Unit (admin unit)
INSERT INTO Unit (unit_name, role, password_hash) VALUES
('Unité Commandement', 'admin', '$2a$10$xJwL5v5Jz5U6Zz5U6Zz5Ue'); -- password: "admin123"

-- Class 
INSERT INTO Class (class_name, description) VALUES
('GI-23', 'Promotion 2023 Génie Informatique');

-- Encadrant 
INSERT INTO Encadrant (matricule, nom, prenom, grade, fonction, unit_id, email, phone) VALUES
('1001', 'Dupont', 'Pierre', 'Colonel', 'Chef de département', 1, 'p.dupont@mil.ac', '0612345678');

-- Admin
INSERT INTO Admin (matricule, nom, prenom, grade, unit_id) VALUES
('0001', 'Leblanc', 'Jean', 'Général', 1);

-- Student
INSERT INTO Student (matricule, nom, prenom, class_id, unit_id) VALUES
('2001', 'Benali', 'Karim', 1, 1);

-- Project
INSERT INTO Project (titre, specialite, nombre_eleves, encadrant_matricule, organisme_or_address, 
                    phone, email, description, objectif, resultats_attendus) VALUES
('Système de gestion', 'genie informatique', 'monome', '1001', 
 'Institut Militaire', '0612345678', 'projet1@mil.ac', 
 'Application web de gestion', 'Automatiser les processus', 
 'Solution fonctionnelle');

-- Reservation
INSERT INTO Reservation (project_id, student1_matricule, student1_class_id, is_approved, approved_by) VALUES
(1, '2001', 1, TRUE, '0001');

-- ProjectHistory
INSERT INTO ProjectHistory (project_id, changed_by, change_type, change_details) VALUES
(1, '1001', 'create', '{"titre": "Système de gestion", "status": "created"}');
select * from admin;
select * from unit;
