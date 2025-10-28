<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";
    
    public $id;
    public $patient_id;
    public $doctor_id;
    public $service_type;
    public $status;
    public $appointment_date;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllAppointments() {
        $query = "SELECT a.*, 
                    CONCAT(p.first_name, ' ', p.last_name) as patient_name, 
                    CONCAT(d.first_name, ' ', d.last_name) as doctor_name 
                 FROM " . $this->table_name . " a
                 JOIN patients p ON a.patient_id = p.id
                 JOIN doctors d ON a.doctor_id = d.id
                 ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['serviceType'] = $row['service_type'];
            $row['patientId'] = $row['patient_id'];
            $row['doctorId'] = $row['doctor_id'];
            $row['date'] = $row['appointment_date'];
            $appointments[] = $row;
        }
        
        return $appointments;
    }
    
    public function createAppointment($patient_id, $doctor_id, $service_type) {
        $query = "INSERT INTO " . $this->table_name . " (patient_id, doctor_id, service_type, status) 
                 VALUES (:patient_id, :doctor_id, :service_type, 'In Progress')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':service_type', $service_type);
        
        return $stmt->execute();
    }
    
    public function updateAppointmentStatus($patient_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE patient_id = :patient_id AND status = 'In Progress'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':patient_id', $patient_id);
        
        return $stmt->execute();
    }
    
    public function getAppointmentsByDoctor($doctor_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE doctor_id = :doctor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAppointmentsByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>