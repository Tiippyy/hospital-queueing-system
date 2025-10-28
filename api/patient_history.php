<?php
require_once '../config/database.php';

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $dateFilter = $_GET['date'] ?? '';
    
    // Build the query
    $query = "SELECT 
                p.id,
                CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                p.age,
                p.gender,
                p.contact,
                p.symptoms,
                p.status,
                p.priority,
                p.service_type,
                p.check_in_time,
                p.completion_time,
                CONCAT(d.first_name, ' ', d.last_name) as doctor_name
              FROM patients p
              LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
              WHERE 1=1";
    
    $params = [];
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND (CONCAT(p.first_name, ' ', p.last_name) LIKE :search 
                   OR p.contact LIKE :search 
                   OR p.symptoms LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }
    
    // Add status filter
    if (!empty($status)) {
        $query .= " AND p.status = :status";
        $params[':status'] = $status;
    }
    
    // Add date filter
    if (!empty($dateFilter)) {
        switch ($dateFilter) {
            case 'today':
                $query .= " AND DATE(p.check_in_time) = CURDATE()";
                break;
            case 'week':
                $query .= " AND YEARWEEK(p.check_in_time, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $query .= " AND YEAR(p.check_in_time) = YEAR(CURDATE()) AND MONTH(p.check_in_time) = MONTH(CURDATE())";
                break;
        }
    }
    
    $query .= " ORDER BY p.check_in_time DESC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    
    $patients = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format check-in time
        if ($row['check_in_time']) {
            $row['formatted_check_in'] = date('M j, Y g:i A', strtotime($row['check_in_time']));
        }
        
        // Calculate wait time if completed
        if ($row['completion_time'] && $row['check_in_time']) {
            $checkIn = new DateTime($row['check_in_time']);
            $completion = new DateTime($row['completion_time']);
            $diff = $checkIn->diff($completion);
            $row['wait_time'] = ($diff->h * 60) + $diff->i . ' minutes';
        } else {
            $row['wait_time'] = 'N/A';
        }
        
        $patients[] = $row;
    }
    
    // Get statistics
    $statsQuery = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN DATE(check_in_time) = CURDATE() THEN 1 ELSE 0 END) as today
                   FROM patients";
    
    // Apply same filters to statistics
    if (!empty($search) || !empty($status) || !empty($dateFilter)) {
        $statsQuery .= " WHERE 1=1";
        
        if (!empty($search)) {
            $statsQuery .= " AND (CONCAT(first_name, ' ', last_name) LIKE :search 
                           OR contact LIKE :search 
                           OR symptoms LIKE :search)";
        }
        
        if (!empty($status)) {
            $statsQuery .= " AND status = :status";
        }
        
        if (!empty($dateFilter)) {
            switch ($dateFilter) {
                case 'today':
                    $statsQuery .= " AND DATE(check_in_time) = CURDATE()";
                    break;
                case 'week':
                    $statsQuery .= " AND YEARWEEK(check_in_time, 1) = YEARWEEK(CURDATE(), 1)";
                    break;
                case 'month':
                    $statsQuery .= " AND YEAR(check_in_time) = YEAR(CURDATE()) AND MONTH(check_in_time) = MONTH(CURDATE())";
                    break;
            }
        }
    }
    
    $statsStmt = $db->prepare($statsQuery);
    
    // Bind same parameters for statistics
    foreach ($params as $key => $value) {
        $statsStmt->bindValue($key, $value);
    }
    
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'patients' => $patients,
        'statistics' => [
            'total' => (int)$stats['total'],
            'completed' => (int)$stats['completed'],
            'today' => (int)$stats['today']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
