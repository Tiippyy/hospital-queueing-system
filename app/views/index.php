<?php
// Main entry point for the clinic management application
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin: 2rem auto;
            padding: 2rem;
            max-width: 1200px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
            <div class="text-center mb-4">
                <h1 class="display-4"><i class="fas fa-hospital-user me-3"></i>Clinic Management System</h1>
                <p class="lead text-muted">Efficient Patient Queue and Doctor Management</p>
            </div>

            <ul class="nav nav-tabs justify-content-center mb-4" id="mainTabs" role="tablist">
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link active" id="queue-tab" data-bs-toggle="tab" data-bs-target="#queue" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>Patient Queue
                    </button>
                </li>
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link" id="doctors-tab" data-bs-toggle="tab" data-bs-target="#doctors" type="button" role="tab">
                        <i class="fas fa-user-md me-2"></i>Doctors
                    </button>
                </li>
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                        <i class="fas fa-history me-2"></i>Patient History
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="mainTabContent">
                <!-- Patient Queue Tab -->
                <div class="tab-pane fade show active" id="queue" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Patient</h5>
                                </div>
                                <div class="card-body">
                                    <form id="patientForm">
                                        <div class="mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="firstName" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="lastName" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Age</label>
                                            <input type="number" class="form-control" name="age" min="1" max="120" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Contact</label>
                                            <input type="tel" class="form-control" name="contact" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Symptoms</label>
                                            <textarea class="form-control" name="symptoms" rows="3" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Priority</label>
                                            <select class="form-control" name="priority" required>
                                                <option value="Low">Low</option>
                                                <option value="Medium" selected>Medium</option>
                                                <option value="High">High</option>
                                                <option value="Emergency">Emergency</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Service Type</label>
                                            <select class="form-control" name="serviceType" required>
                                                <option value="Consultation">Consultation</option>
                                                <option value="Follow-up">Follow-up</option>
                                                <option value="Emergency">Emergency</option>
                                                <option value="Checkup">Checkup</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-plus me-2"></i>Add Patient
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Patient Queue</h5>
                                    <button class="btn btn-light btn-sm" onclick="refreshQueue()">
                                        <i class="fas fa-refresh me-1"></i>Refresh
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="patientQueue">
                                        <!-- Patient queue will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctors Tab -->
                <div class="tab-pane fade" id="doctors" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Doctor Management</h5>
                        </div>
                        <div class="card-body">
                            <div id="doctorsList">
                                <!-- Doctors list will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Patient History</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="searchHistory" placeholder="Search patients...">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="Waiting">Waiting</option>
                                        <option value="In Consultation">In Consultation</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="dateFilter">
                                        <option value="">All Time</option>
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">This Month</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary" onclick="loadPatientHistory()">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                            <div id="patientHistory">
                                <!-- Patient history will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Tab -->
                <div class="tab-pane fade" id="analytics" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="h2 mb-1" id="totalPatients">0</div>
                                <div>Total Patients</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="h2 mb-1" id="totalDoctors">0</div>
                                <div>Total Doctors</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="h2 mb-1" id="completedToday">0</div>
                                <div>Completed Today</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center">
                                <div class="h2 mb-1" id="avgWaitTime">0</div>
                                <div>Avg Wait Time</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Monthly Reports</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Service Distribution</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="serviceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals and Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../js/app-core.js"></script>
    <script src="../../js/queue-management.js"></script>
    <script src="../../js/doctor-management.js"></script>
    <script src="../../js/patient-history.js"></script>
    <script src="../../js/reports-analytics.js"></script>
    <script src="../../js/event-handlers.js"></script>
    <script src="../../js/api-operations.js"></script>
</body>
</html>