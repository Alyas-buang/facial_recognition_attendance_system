-- Create database
CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

-- Table for students
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  student_number VARCHAR(50) NOT NULL UNIQUE,
  descriptors JSON NOT NULL
);

-- Table for attendance logs
CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
