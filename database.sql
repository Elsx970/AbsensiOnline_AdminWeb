
CREATE DATABASE IF NOT EXISTS absensi_db;
USE absensi_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    foto_profile VARCHAR(255) DEFAULT NULL,
    program_studi VARCHAR(100) DEFAULT NULL,
    nomor_telpon VARCHAR(20) DEFAULT NULL,
    email_mhs VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS lokasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lokasi VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    radius INT NOT NULL DEFAULT 50,
    pertemuan INT DEFAULT 1,
    tanggal DATE DEFAULT NULL,
    jam_mulai TIME DEFAULT NULL,
    jam_selesai TIME DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lokasi_id INT DEFAULT 1,
    tanggal DATE NOT NULL,
    jam_masuk TIME,
    latitude_masuk DECIMAL(10, 8),
    longitude_masuk DECIMAL(11, 8),
    foto_masuk VARCHAR(255),
    status ENUM('Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa') DEFAULT 'Hadir',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (nama, email, password, role) VALUES ('Administrator', 'admin@admin.com', '$2b$12$gvDERWe0K1kRD344kyDCF.v6hCgCYiEqniwFA287ZkUNbXQbXGeC.', 'admin');
INSERT INTO lokasi (nama_lokasi, latitude, longitude, radius) VALUES ('Kampus Unila', -5.364446, 105.243501, 100);
