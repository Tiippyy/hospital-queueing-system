<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';

class StatisticsController {
    private $db;
    private $patient;
    private $doctor;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->patient = new Patient($this->db);
        $this->doctor = new Doctor($this->db);
    }
    
    public function index() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                // Get patient statistics
                $patientStats = $this->patient->getTodayStatistics();
                
                // Get doctor utilization
                $doctorUtilization = $this->doctor->getAverageUtilization();
                
                // Get service distribution
                $serviceDistribution = $this->patient->getServiceDistribution();
                
                // Get status counts
                $statusCounts = [];
                $waitingPatients = $this->patient->getPatientsByStatus('Waiting');
                $inProgressPatients = $this->patient->getPatientsByStatus('In Progress');
                $completedPatients = $this->patient->getPatientsByStatus('Completed');
                
                $statusCounts['Waiting'] = count($waitingPatients);
                $statusCounts['In Progress'] = count($inProgressPatients);
                $statusCounts['Completed'] = count($completedPatients);
                
                jsonResponse([
                    'totalPatients' => $patientStats['totalPatients'],
                    'completedPatients' => $patientStats['completedPatients'],
                    'averageWaitTime' => $patientStats['averageWaitTime'],
                    'doctorUtilization' => $doctorUtilization,
                    'statusCounts' => $statusCounts,
                    'serviceDistribution' => $serviceDistribution
                ]);
                
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function monthlyReports() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $reports = $this->patient->getMonthlyReports();
                jsonResponse($reports);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
    
    public function getRecommendations() {
        enableCORS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $recommendations = [];
                
                // Get current data for analysis
                $patientStats = $this->patient->getTodayStatistics();
                $doctors = $this->doctor->getAllDoctors();
                $serviceDistribution = $this->patient->getServiceDistribution();
                
                // Recommendation 1: Doctor Utilization Analysis
                $overloadedDoctors = array_filter($doctors, function($doctor) {
                    return ($doctor['current_patients'] / $doctor['max_patients_per_day']) > 0.8;
                });
                
                if (count($overloadedDoctors) > 0) {
                    $recommendations[] = [
                        'title' => 'High Doctor Utilization Alert',
                        'description' => count($overloadedDoctors) . ' doctor(s) are at >80% capacity. Consider redistributing patients or adding staff.'
                    ];
                }
                
                // Recommendation 2: Service Type Analysis
                if (!empty($serviceDistribution)) {
                    $emergencyCount = 0;
                    $totalCount = 0;
                    foreach ($serviceDistribution as $service) {
                        $totalCount += $service['value'];
                        if ($service['name'] === 'Emergency') {
                            $emergencyCount = $service['value'];
                        }
                    }
                    
                    if ($totalCount > 0 && ($emergencyCount / $totalCount) > 0.3) {
                        $recommendations[] = [
                            'title' => 'High Emergency Volume',
                            'description' => 'Emergency cases account for ' . round(($emergencyCount / $totalCount) * 100) . '% of today\'s visits. Consider increasing emergency staff.'
                        ];
                    }
                }
                
                // Recommendation 3: Wait Time Analysis
                if ($patientStats['averageWaitTime'] > 30) {
                    $recommendations[] = [
                        'title' => 'Extended Wait Times',
                        'description' => 'Average wait time is ' . $patientStats['averageWaitTime'] . ' minutes. Consider optimizing patient flow or adding resources.'
                    ];
                }
                
                // Recommendation 4: Completion Rate Analysis
                if ($patientStats['totalPatients'] > 0) {
                    $completionRate = ($patientStats['completedPatients'] / $patientStats['totalPatients']) * 100;
                    if ($completionRate < 70) {
                        $recommendations[] = [
                            'title' => 'Low Completion Rate',
                            'description' => 'Only ' . round($completionRate) . '% of today\'s patients have completed consultations. Review workflow efficiency.'
                        ];
                    }
                }
                
                // Recommendation 5: Seasonal/Time-based (dynamic based on current month)
                $currentMonth = date('n'); // 1-12
                if (in_array($currentMonth, [11, 12, 1, 2])) { // Winter months
                    $recommendations[] = [
                        'title' => 'Seasonal Preparedness',
                        'description' => 'Winter season: Consider increasing pediatric and respiratory care capacity for flu season.'
                    ];
                }
                
                // Default recommendation if no issues found
                if (empty($recommendations)) {
                    $recommendations[] = [
                        'title' => 'System Operating Efficiently',
                        'description' => 'All metrics are within normal ranges. Continue monitoring patient flow and doctor utilization.'
                    ];
                }
                
                jsonResponse($recommendations);
            } catch(Exception $e) {
                jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            jsonResponse(['error' => 'Method not allowed'], 405);
        }
    }
}
?>