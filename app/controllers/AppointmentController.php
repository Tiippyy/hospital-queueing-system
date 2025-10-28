<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Appointment.php';

class AppointmentController {
    private $db;
    private $appointment;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->appointment = new Appointment($this->db);
    }
    
    public function index() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $appointments = $this->appointment->getAllAppointments();
                jsonResponse($appointments);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function getByDoctor($doctor_id) {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $appointments = $this->appointment->getAppointmentsByDoctor($doctor_id);
                jsonResponse($appointments);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function getByPatient($patient_id) {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $appointments = $this->appointment->getAppointmentsByPatient($patient_id);
                jsonResponse($appointments);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
}
?>