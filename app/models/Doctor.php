<?php
class Doctor {
    private $conn;
    private $table_name = "doctors";
    
    public $id;
    public $first_name;
    public $last_name;
    public $specialty;
    public $availability;
    public $current_patients;
    public $max_patients_per_day;
    public $expertise;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllDoctors() {
        $query = "SELECT id, first_name, last_name, specialty, availability, current_patients, max_patients_per_day, expertise FROM " . $this->table_name . " ORDER BY last_name, first_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['name'] = $row['first_name'] . ' ' . $row['last_name']; // For compatibility
            $row['expertise'] = explode(', ', $row['expertise']);
            $row['availability'] = (bool)$row['availability'];
            $doctors[] = $row;
        }
        
        return $doctors;
    }
    
    public function getDoctorById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function incrementPatientCount($doctor_id) {
        $query = "UPDATE " . $this->table_name . " SET current_patients = current_patients + 1 WHERE id = :doctor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        
        return $stmt->execute();
    }
    
    public function decrementPatientCount($doctor_id) {
        $query = "UPDATE " . $this->table_name . " SET current_patients = current_patients - 1 WHERE id = :doctor_id AND current_patients > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        
        return $stmt->execute();
    }
    
    public function getAvailableDoctors() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE availability = 1 AND current_patients < max_patients_per_day";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $doctors = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
            $row['expertise'] = explode(', ', $row['expertise']);
            $doctors[] = $row;
        }
        
        return $doctors;
    }
    
    public function getAverageUtilization() {
        $query = "SELECT AVG((current_patients / max_patients_per_day) * 100) as avg_utilization FROM " . $this->table_name . " WHERE availability = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['avg_utilization'] ? round($result['avg_utilization']) : 0;
    }
}
?>