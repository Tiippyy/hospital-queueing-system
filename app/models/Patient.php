<?php
class Patient {
    private $conn;
    private $table_name = "patients";
    
    public $id;
    public $first_name;
    public $last_name;
    public $age;
    public $gender;
    public $contact;
    public $symptoms;
    public $priority;
    public $service_type;
    public $status;
    public $check_in_time;
    public $completion_time;
    public $assigned_doctor_id;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllPatients() {
        $query = "SELECT p.*, CONCAT(d.first_name, ' ', d.last_name) as doctor_name 
                 FROM " . $this->table_name . " p 
                 LEFT JOIN doctors d ON p.assigned_doctor_id = d.id 
                 ORDER BY 
                    CASE WHEN p.priority = 'Urgent' THEN 1 ELSE 2 END,
                    p.check_in_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['name'] = $row['first_name'] . ' ' . $row['last_name']; // For compatibility
            $row['checkInTime'] = $row['check_in_time'];
            $row['assignedDoctor'] = $row['assigned_doctor_id'];
            $row['serviceType'] = $row['service_type'];
            $patients[] = $row;
        }
        
        return $patients;
    }
    
    public function getPatientById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function addPatient($data) {
        $query = "INSERT INTO " . $this->table_name . " (first_name, last_name, age, gender, contact, symptoms, priority, service_type) 
                 VALUES (:first_name, :last_name, :age, :gender, :contact, :symptoms, :priority, :service_type)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':first_name', $data['firstName']);
        $stmt->bindParam(':last_name', $data['lastName']);
        $stmt->bindParam(':age', $data['age']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':contact', $data['contact']);
        $stmt->bindParam(':symptoms', $data['symptoms']);
        $stmt->bindParam(':priority', $data['priority']);
        $stmt->bindParam(':service_type', $data['serviceType']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    public function assignDoctor($patient_id, $doctor_id) {
        $query = "UPDATE " . $this->table_name . " SET assigned_doctor_id = :doctor_id, status = 'In Progress' WHERE id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':patient_id', $patient_id);
        
        return $stmt->execute();
    }
    
    public function completeConsultation($patient_id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'Completed', completion_time = CURRENT_TIMESTAMP WHERE id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        
        return $stmt->execute();
    }
    
    public function getPatientsByStatus($status) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        $patients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
            $patients[] = $row;
        }
        
        return $patients;
    }
    
    public function getTodayStatistics() {
        $today = date('Y-m-d');
        
        // Total patients today
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE DATE(check_in_time) = :today";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $totalPatients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Completed patients today
        $query = "SELECT COUNT(*) as completed FROM " . $this->table_name . " WHERE DATE(check_in_time) = :today AND status = 'Completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $completedPatients = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        
        // Average wait time
        $query = "SELECT AVG(TIMESTAMPDIFF(MINUTE, check_in_time, completion_time)) as avg_wait_time 
                  FROM " . $this->table_name . " 
                  WHERE DATE(check_in_time) = :today AND status = 'Completed' AND completion_time IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        $avgWaitTime = $stmt->fetch(PDO::FETCH_ASSOC)['avg_wait_time'];
        
        return [
            'totalPatients' => (int)$totalPatients,
            'completedPatients' => (int)$completedPatients,
            'averageWaitTime' => $avgWaitTime ? round($avgWaitTime) : 0
        ];
    }
    
    public function getServiceDistribution() {
        $today = date('Y-m-d');
        $query = "SELECT service_type, COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE DATE(check_in_time) = :today GROUP BY service_type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        
        $distribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $distribution[] = [
                'name' => $row['service_type'],
                'value' => (int)$row['count']
            ];
        }
        
        return $distribution;
    }
    
    public function getMonthlyReports() {
        $query = "
            SELECT 
                DATE_FORMAT(check_in_time, '%M') as month,
                COUNT(*) as consultations,
                SUM(CASE WHEN service_type = 'Emergency' THEN 1 ELSE 0 END) as emergencies
            FROM " . $this->table_name . " 
            WHERE check_in_time >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY YEAR(check_in_time), MONTH(check_in_time)
            ORDER BY YEAR(check_in_time), MONTH(check_in_time)
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $reports = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reports[] = [
                'month' => $row['month'],
                'consultations' => (int)$row['consultations'],
                'emergencies' => (int)$row['emergencies']
            ];
        }
        
        // If no data exists, provide some sample structure
        if (empty($reports)) {
            $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
            foreach ($months as $month) {
                $reports[] = [
                    'month' => $month,
                    'consultations' => 0,
                    'emergencies' => 0
                ];
            }
        }
        
        return $reports;
    }
}
?>