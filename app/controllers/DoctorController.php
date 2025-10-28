<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Doctor.php';

class DoctorController {
    private $db;
    private $doctor;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->doctor = new Doctor($this->db);
    }
    
    public function index() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $doctors = $this->doctor->getAllDoctors();
                jsonResponse($doctors);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function show($id) {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $doctor = $this->doctor->getDoctorById($id);
                if ($doctor) {
                    jsonResponse($doctor);
                } else {
                    jsonResponse(['error' => 'Doctor not found'], 404);
                }
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function getAvailable() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $doctors = $this->doctor->getAvailableDoctors();
                jsonResponse($doctors);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
}
?>