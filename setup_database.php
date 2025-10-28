<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Create patients table
    $patients_sql = "CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        age INT NOT NULL,
        gender ENUM('Male', 'Female', 'Other') NOT NULL,
        contact VARCHAR(20) NOT NULL,
        symptoms TEXT NOT NULL,
        priority ENUM('Low', 'Medium', 'High', 'Emergency') DEFAULT 'Medium',
        service_type ENUM('Consultation', 'Follow-up', 'Emergency', 'Checkup') DEFAULT 'Consultation',
        status ENUM('Waiting', 'In Consultation', 'Completed') DEFAULT 'Waiting',
        assigned_doctor_id INT NULL,
        check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        consultation_start_time TIMESTAMP NULL,
        completion_time TIMESTAMP NULL,
        queue_position INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    // Create doctors table
    $doctors_sql = "CREATE TABLE IF NOT EXISTS doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        specialization VARCHAR(100) NOT NULL,
        contact VARCHAR(20) NOT NULL,
        email VARCHAR(100) NULL,
        status ENUM('Available', 'Busy', 'Off Duty') DEFAULT 'Available',
        current_patients INT DEFAULT 0,
        max_patients INT DEFAULT 10,
        consultation_fee DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    // Create appointments table
    $appointments_sql = "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NULL,
        service_type VARCHAR(50) NOT NULL,
        status ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
        notes TEXT NULL,
        consultation_fee DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )";    
    
    // Execute table creation
    $db->exec($patients_sql);
    echo "Patients table created successfully.\n";
    
    $db->exec($doctors_sql);
    echo "Doctors table created successfully.\n";
    
    $db->exec($appointments_sql);
    echo "Appointments table created successfully.\n";
    
    // Insert sample doctors if table is empty
    $check_doctors = $db->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    
    if ($check_doctors == 0) {
        $sample_doctors = [
            ['Dr. John', 'Smith', 'General Medicine', '+1234567890', 'john.smith@clinic.com'],
            ['Dr. Sarah', 'Johnson', 'Pediatrics', '+1234567891', 'sarah.johnson@clinic.com'],
            ['Dr. Michael', 'Brown', 'Cardiology', '+1234567892', 'michael.brown@clinic.com'],
            ['Dr. Emily', 'Davis', 'Dermatology', '+1234567893', 'emily.davis@clinic.com'],
            ['Dr. David', 'Wilson', 'Orthopedics', '+1234567894', 'david.wilson@clinic.com']
        ];
        
        $insert_doctor = $db->prepare("INSERT INTO doctors (first_name, last_name, specialization, contact, email) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($sample_doctors as $doctor) {
            $insert_doctor->execute($doctor);
        }
        
        echo "Sample doctors inserted successfully.\n";
    }
    
    echo "\nDatabase setup completed successfully!";
    echo "\nYou can now access the application at: http://localhost/MyWebsite/";
    
} catch(Exception $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>