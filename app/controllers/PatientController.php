<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Appointment.php';

class PatientController {
    private $db;
    private $patient;
    private $doctor;
    private $appointment;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient = new Patient($this->db);
        $this->doctor = new Doctor($this->db);
        $this->appointment = new Appointment($this->db);
    }
    
    public function index() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $patients = $this->patient->getAllPatients();
                jsonResponse($patients);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function store() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = getPostData();
                
                // Validate required fields
                if (empty($data['firstName']) || empty($data['lastName']) || empty($data['symptoms'])) {
                    jsonResponse(['error' => 'First name, last name, and symptoms are required'], 400);
                    return;
                }
                
                $patient_id = $this->patient->addPatient($data);
                
                if ($patient_id) {
                    jsonResponse(['success' => true, 'patient_id' => $patient_id]);
                } else {
                    jsonResponse(['error' => 'Failed to add patient'], 500);
                }
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function assignDoctor() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = getPostData();
                
                if (empty($data['patientId']) || empty($data['doctorId'])) {
                    jsonResponse(['error' => 'Patient ID and Doctor ID are required'], 400);
                    return;
                }
                
                // Start transaction
                $this->db->beginTransaction();
                
                // Get patient details for service type
                $patient_data = $this->patient->getPatientById($data['patientId']);
                if (!$patient_data) {
                    $this->db->rollback();
                    jsonResponse(['error' => 'Patient not found'], 404);
                    return;
                }
                
                // Assign doctor to patient
                if (!$this->patient->assignDoctor($data['patientId'], $data['doctorId'])) {
                    $this->db->rollback();
                    jsonResponse(['error' => 'Failed to assign doctor to patient'], 500);
                    return;
                }
                
                // Increment doctor's patient count
                if (!$this->doctor->incrementPatientCount($data['doctorId'])) {
                    $this->db->rollback();
                    jsonResponse(['error' => 'Failed to update doctor patient count'], 500);
                    return;
                }
                
                // Create appointment record
                if (!$this->appointment->createAppointment($data['patientId'], $data['doctorId'], $patient_data['service_type'])) {
                    $this->db->rollback();
                    jsonResponse(['error' => 'Failed to create appointment'], 500);
                    return;
                }
                
                $this->db->commit();
                jsonResponse(['success' => true]);
                
            } catch(Exception $e) {
                $this->db->rollback();
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function completeConsultation() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = getPostData();
                
                if (empty($data['patientId'])) {
                    jsonResponse(['error' => 'Patient ID is required'], 400);
                    return;
                }
                
                // Start transaction
                $this->db->beginTransaction();
                
                // Get the patient's assigned doctor
                $patient_data = $this->patient->getPatientById($data['patientId']);
                
                if ($patient_data && $patient_data['assigned_doctor_id']) {
                    // Complete the consultation
                    if (!$this->patient->completeConsultation($data['patientId'])) {
                        $this->db->rollback();
                        jsonResponse(['error' => 'Failed to complete consultation'], 500);
                        return;
                    }
                    
                    // Decrease doctor's current patient count
                    if (!$this->doctor->decrementPatientCount($patient_data['assigned_doctor_id'])) {
                        $this->db->rollback();
                        jsonResponse(['error' => 'Failed to update doctor patient count'], 500);
                        return;
                    }
                    
                    // Update appointment status
                    if (!$this->appointment->updateAppointmentStatus($data['patientId'], 'Completed')) {
                        $this->db->rollback();
                        jsonResponse(['error' => 'Failed to update appointment status'], 500);
                        return;
                    }
                }
                
                $this->db->commit();
                jsonResponse(['success' => true]);
                
            } catch(Exception $e) {
                $this->db->rollback();
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
}
?>