/* ============================================================================
 * HEALTH QUEUE MANAGEMENT SYSTEM - MAIN APPLICATION
 * ============================================================================
 * 
 * This file contains the main HealthQueueApp class with organized sections:
 * 1. INITIALIZATION & CORE SETUP
 * 2. DATA MANAGEMENT & API CALLS
 * 3. EVENT LISTENERS & UI INTERACTION
 * 4. TAB NAVIGATION & DISPLAY MANAGEMENT
 * 5. QUEUE MANAGEMENT (Waiting & In-Progress)
 * 6. DOCTOR MANAGEMENT & ASSIGNMENT
 * 7. REPORTS & ANALYTICS
 * 8. PATIENT HISTORY
 * 9. UTILITY & HELPER FUNCTIONS
 * 
 * ============================================================================ */

class HealthQueueApp {
    
    /* ========================================================================
     * 1. INITIALIZATION & CORE SETUP
     * ======================================================================== */
    
    constructor() {
        // Core application state
        this.currentTab = 'queue';
        
        // Data containers
        this.doctors = [];
        this.patients = [];
        this.appointments = [];
        this.monthlyReports = [];
        this.recommendations = [];
        this.selectedDoctors = {};
        
        // Patient history specific data
        this.patientHistory = [];
        this.historyFilters = {
            search: '',
            status: '',
            date: ''
        };
        
        // Initialize the application
        this.init();
    }
    
    async init() {
        await this.loadData();
        this.setupEventListeners();
        this.showTab('queue');
    }
    
    /* ========================================================================
     * 2. DATA MANAGEMENT & API CALLS
     * ======================================================================== */
    
    async loadData() {
        this.showLoading(true);
        try {
            // Fetch all data in parallel for better performance
            const [doctorsRes, patientsRes, appointmentsRes, reportsRes, statsRes, recommendationsRes] = await Promise.all([
                fetch('api/doctors.php'),
                fetch('api/patients.php'),
                fetch('api/appointments.php'),
                fetch('api/monthly_reports.php'),
                fetch('api/statistics.php'),
                fetch('api/recommendations.php')
            ]);
            
            // Parse all responses
            this.doctors = await doctorsRes.json();
            this.patients = await patientsRes.json();
            this.appointments = await appointmentsRes.json();
            this.monthlyReports = await reportsRes.json();
            this.statistics = await statsRes.json();
            this.recommendations = await recommendationsRes.json();
            
            // Update all displays with fresh data
            this.updateDisplay();
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Failed to load data. Please refresh the page.');
        }
        this.showLoading(false);
    }
    
    /* ========================================================================
     * 3. EVENT LISTENERS & UI INTERACTION
     * ======================================================================== */
    
    setupEventListeners() {
        // Tab navigation event listeners
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tab = e.target.dataset.tab;
                this.showTab(tab);
            });
        });
        
        // Patient registration form event listeners
        document.getElementById('addPatientBtn').addEventListener('click', () => {
            this.addPatient();
        });
        
        // History tab specific event listeners - setup when tab is accessed
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-tab="history"]')) {
                // Delay to ensure DOM elements are ready
                setTimeout(() => this.setupHistoryEventListeners(), 100);
            }
        });
    }
    
    setupHistoryEventListeners() {
        // Patient history search and filter event listeners
        const searchInput = document.getElementById('historySearch');
        const statusSelect = document.getElementById('historyStatus');
        const dateSelect = document.getElementById('historyDate');
        
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.historyFilters.search = searchInput.value;
                this.loadPatientHistory();
            });
        }
        
        if (statusSelect) {
            statusSelect.addEventListener('change', () => {
                this.historyFilters.status = statusSelect.value;
                this.loadPatientHistory();
            });
        }
        
        if (dateSelect) {
            dateSelect.addEventListener('change', () => {
                this.historyFilters.date = dateSelect.value;
                this.loadPatientHistory();
            });
        }
    }
    
    /* ========================================================================
     * 4. TAB NAVIGATION & DISPLAY MANAGEMENT
     * ======================================================================== */
    
    showTab(tab) {
        // Update current tab state
        this.currentTab = tab;
        
        // Update navigation button states
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
        
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected tab content
        document.getElementById(`${tab}Tab`).classList.remove('hidden');
        
        // Render tab-specific content
        switch(tab) {
            case 'queue':
                this.renderQueue();
                break;
            case 'registration':
                this.renderRegistration();
                break;
            case 'doctors':
                this.renderDoctors();
                break;
            case 'history':
                this.renderHistory();
                break;
            case 'reports':
                this.renderReports();
                break;
        }
    }
    
    updateDisplay() {
        // Update all main displays with current data
        this.renderQueue();
        this.renderDoctors();
        this.renderReports();
        this.updateStatisticsDisplay();
    }
    
    updateStatisticsDisplay() {
        // Update statistics display across all tabs
        if (this.statistics) {
            const totalPatientsEl = document.getElementById('totalPatients');
            const completedPatientsEl = document.getElementById('completedPatients');
            const avgWaitTimeEl = document.getElementById('avgWaitTime');
            const doctorUtilizationEl = document.getElementById('doctorUtilization');
            
            if (totalPatientsEl) totalPatientsEl.textContent = this.statistics.totalPatients;
            if (completedPatientsEl) completedPatientsEl.textContent = this.statistics.completedPatients;
            if (avgWaitTimeEl) avgWaitTimeEl.textContent = this.statistics.averageWaitTime;
            if (doctorUtilizationEl) doctorUtilizationEl.textContent = this.statistics.doctorUtilization + '%';
        }
    }
    
    renderQueue() {
        const waitingPatients = this.patients.filter(p => p.status === 'Waiting');
        const inProgressPatients = this.patients.filter(p => p.status === 'In Progress');
        
        // Render waiting queue
        const waitingContainer = document.getElementById('waitingQueue');
        waitingContainer.innerHTML = '';
        
        if (waitingPatients.length === 0) {
            waitingContainer.innerHTML = '<div class="text-center p-4"><p>No patients currently waiting in queue</p></div>';
        } else {
            waitingPatients.forEach(patient => {
                waitingContainer.appendChild(this.createPatientCard(patient));
            });
        }
        
        // Update waiting count
        document.getElementById('waitingCount').textContent = `${waitingPatients.length} patients waiting`;
        
        // Render in-progress consultations
        const inProgressContainer = document.getElementById('inProgressQueue');
        inProgressContainer.innerHTML = '';
        
        if (inProgressPatients.length === 0) {
            inProgressContainer.innerHTML = '<div class="text-center p-4"><p>No ongoing consultations</p></div>';
        } else {
            inProgressPatients.forEach(patient => {
                inProgressContainer.appendChild(this.createInProgressCard(patient));
            });
        }
        
        // Update in-progress count
        document.getElementById('inProgressCount').textContent = `${inProgressPatients.length} ongoing consultations`;
    }
    
    createPatientCard(patient) {
        const card = document.createElement('div');
        card.className = `card patient-card ${patient.priority === 'Urgent' ? 'urgent' : ''}`;
        
        const recommendations = this.getDoctorRecommendations(patient.symptoms, patient.service_type);
        const availableDoctors = this.doctors.filter(d => d.availability && d.current_patients < d.max_patients_per_day);
        
        card.innerHTML = `
            <div class="card-content">
                <div class="flex justify-between">
                    <div class="patient-info">
                        <h3>${patient.name} (${patient.age}, ${patient.gender})</h3>
                        <p>${patient.symptoms}</p>
                        <p>Priority: <span class="${patient.priority === 'Urgent' ? 'priority-urgent' : ''}">${patient.priority}</span> • Service: ${patient.service_type}</p>
                        <p>Check-in: ${new Date(patient.check_in_time).toLocaleTimeString()}</p>
                    </div>
                    <div class="assign-controls">
                        <select class="form-select" id="doctor-select-${patient.id}" style="width: 200px; margin-bottom: 8px;">
                            <option value="">Assign doctor</option>
                            ${availableDoctors.map(doctor => 
                                `<option value="${doctor.id}">${doctor.name} (${doctor.specialty}) - ${doctor.max_patients_per_day - doctor.current_patients} slots left</option>`
                            ).join('')}
                        </select>
                        <br>
                        <button class="btn btn-sm" onclick="app.assignDoctor(${patient.id})">Assign</button>
                    </div>
                </div>
                ${recommendations.length > 0 ? `
                    <div class="recommendations">
                        <p style="font-weight: 600; font-size: 0.75rem; margin-bottom: 8px;">Recommended Doctors:</p>
                        ${recommendations.map(doctor => `<span class="recommendation-tag">${doctor.name}</span>`).join('')}
                    </div>
                ` : ''}
            </div>
        `;
        
        return card;
    }
    
    createInProgressCard(patient) {
        const doctor = this.doctors.find(d => d.id == patient.assigned_doctor_id);
        
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <div class="card-content">
                <div class="flex justify-between items-center">
                    <div>
                        <h3>${patient.name}</h3>
                        <p>With ${doctor ? doctor.name : 'Unknown'} (${doctor ? doctor.specialty : 'N/A'})</p>
                        <p>Service: ${patient.service_type}</p>
                    </div>
                    <button class="btn" onclick="app.completeConsultation(${patient.id})">Complete</button>
                </div>
            </div>
        `;
        
        return card;
    }
    
    renderDoctors() {
        const container = document.getElementById('doctorsGrid');
        container.innerHTML = '';
        
        this.doctors.forEach(doctor => {
            const card = document.createElement('div');
            card.className = `card doctor-card ${!doctor.availability ? 'opacity-50' : ''}`;
            
            const utilizationPercent = (doctor.current_patients / doctor.max_patients_per_day) * 100;
            
            card.innerHTML = `
                <div class="card-content">
                    <div class="availability-indicator ${doctor.availability ? 'available' : 'unavailable'}"></div>
                    <h3>${doctor.name}</h3>
                    <p style="color: #6b7280; margin-bottom: 16px;">${doctor.specialty}</p>
                    
                    <div>
                        <div class="flex justify-between" style="font-size: 0.875rem; margin-bottom: 8px;">
                            <span>Patients today:</span>
                            <span>${doctor.current_patients} / ${doctor.max_patients_per_day}</span>
                        </div>
                        
                        <div class="utilization-bar">
                            <div class="utilization-fill" style="width: ${utilizationPercent}%"></div>
                        </div>
                        
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 12px;">
                            <p style="font-weight: 600; margin-bottom: 4px;">Expertise:</p>
                            <div class="expertise-tags">
                                ${doctor.expertise.slice(0, 3).map(exp => `<span class="expertise-tag">${exp}</span>`).join('')}
                                ${doctor.expertise.length > 3 ? `<span class="expertise-tag">+${doctor.expertise.length - 3} more</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
    }
    
    renderReports() {
        // Update stats with real data from API
        if (this.statistics) {
            document.getElementById('totalPatients').textContent = this.statistics.totalPatients;
            document.getElementById('completedPatients').textContent = this.statistics.completedPatients;
            document.getElementById('avgWaitTime').textContent = this.statistics.averageWaitTime;
            document.getElementById('doctorUtilization').textContent = this.statistics.doctorUtilization + '%';
        }
        
        // Render charts with real data
        this.renderCharts();
        
        // Render dynamic recommendations
        this.renderRecommendations();
    }
    
    renderRecommendations() {
        const container = document.getElementById('recommendationsContainer');
        container.innerHTML = '';
        
        if (this.recommendations && this.recommendations.length > 0) {
            this.recommendations.forEach(recommendation => {
                const card = document.createElement('div');
                card.className = 'recommendation-card';
                card.innerHTML = `
                    <h4>${recommendation.title}</h4>
                    <p>${recommendation.description}</p>
                `;
                container.appendChild(card);
            });
        } else {
            container.innerHTML = '<div class="recommendation-card"><h4>No Recommendations</h4><p>System is operating normally.</p></div>';
        }
    }
    
    calculateAverageUtilization() {
        if (this.doctors.length === 0) return 0;
        const total = this.doctors.reduce((sum, doctor) => {
            return sum + ((doctor.current_patients / doctor.max_patients_per_day) * 100);
        }, 0);
        return Math.round(total / this.doctors.length);
    }
    
    renderCharts() {
        const chartContainer = document.getElementById('chartsContainer');
        
        // Monthly trends table
        let monthlyTrendsHtml = '<h4>No data available</h4>';
        if (this.monthlyReports && this.monthlyReports.length > 0) {
            monthlyTrendsHtml = `
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #e5e7eb;">Month</th>
                            <th style="padding: 8px; text-align: center; border: 1px solid #e5e7eb;">Consultations</th>
                            <th style="padding: 8px; text-align: center; border: 1px solid #e5e7eb;">Emergencies</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${this.monthlyReports.map(report => `
                            <tr>
                                <td style="padding: 8px; border: 1px solid #e5e7eb;">${report.month}</td>
                                <td style="padding: 8px; text-align: center; border: 1px solid #e5e7eb;">${report.consultations}</td>
                                <td style="padding: 8px; text-align: center; border: 1px solid #e5e7eb;">${report.emergencies}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
        
        // Service distribution
        let serviceDistributionHtml = '<p>No service data available</p>';
        if (this.statistics && this.statistics.serviceDistribution && this.statistics.serviceDistribution.length > 0) {
            serviceDistributionHtml = `
                <div class="grid grid-cols-1" style="gap: 8px;">
                    ${this.statistics.serviceDistribution.map(service => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: #f8fafc; border-radius: 4px;">
                            <span>${service.name}</span>
                            <span style="font-weight: 600;">${service.value}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }
        
        chartContainer.innerHTML = `
            <div class="grid grid-cols-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Monthly Trends</div>
                        <div class="card-description">Patient visits by month</div>
                    </div>
                    <div class="card-content">
                        ${monthlyTrendsHtml}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Service Distribution Today</div>
                        <div class="card-description">Types of services provided</div>
                    </div>
                    <div class="card-content">
                        ${serviceDistributionHtml}
                    </div>
                </div>
            </div>
        `;
    }
    
    getDoctorRecommendations(symptoms, serviceType) {
        const keywords = symptoms.toLowerCase().split(' ');
        
        return this.doctors
            .filter(doctor => doctor.availability)
            .filter(doctor => doctor.current_patients < doctor.max_patients_per_day)
            .map(doctor => {
                const score = doctor.expertise.filter(exp => 
                    keywords.some(kw => exp.toLowerCase().includes(kw))
                ).length;
                return { ...doctor, score };
            })
            .sort((a, b) => b.score - a.score)
            .slice(0, 3);
    }
    
    async assignDoctor(patientId) {
        const select = document.getElementById(`doctor-select-${patientId}`);
        const doctorId = select.value;
        
        if (!doctorId) {
            alert('Please select a doctor');
            return;
        }
        
        try {
            const response = await fetch('api/assign_doctor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ patientId, doctorId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Reload all data including fresh statistics
                await this.loadData();
                // Force update statistics display immediately
                this.updateStatisticsDisplay();
            } else {
                alert('Failed to assign doctor: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error assigning doctor:', error);
            alert('Failed to assign doctor');
        }
    }
    
    async completeConsultation(patientId) {
        try {
            const response = await fetch('api/complete_consultation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ patientId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Reload all data including fresh statistics
                await this.loadData();
                // Force update statistics display immediately
                this.updateStatisticsDisplay();
            } else {
                alert('Failed to complete consultation: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error completing consultation:', error);
            alert('Failed to complete consultation');
        }
    }
    
    async addPatient() {
        const formData = {
            firstName: document.getElementById('patientFirstName').value,
            lastName: document.getElementById('patientLastName').value,
            age: document.getElementById('patientAge').value,
            gender: document.getElementById('patientGender').value,
            contact: document.getElementById('patientContact').value,
            symptoms: document.getElementById('patientSymptoms').value,
            priority: document.getElementById('patientPriority').value,
            serviceType: document.getElementById('patientServiceType').value
        };
        
        if (!formData.firstName || !formData.lastName || !formData.symptoms) {
            alert('First name, last name, and symptoms are required');
            return;
        }
        
        try {
            const response = await fetch('api/add_patient.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear form
                document.getElementById('patientRegistrationForm').reset();
                // Refresh data
                await this.loadData();
                // Switch to queue tab
                this.showTab('queue');
                alert('Patient added successfully!');
            } else {
                alert('Failed to add patient: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error adding patient:', error);
            alert('Failed to add patient');
        }
    }
    
    showLoading(show) {
        const loader = document.getElementById('loading');
        if (show) {
            loader.classList.remove('hidden');
        } else {
            loader.classList.add('hidden');
        }
    }
    
    showError(message) {
        alert(message); // In a real app, you'd use a better notification system
    }
    
    // Patient History Methods
    async renderHistory() {
        this.setupHistoryEventListeners();
        await this.loadPatientHistory();
    }
    
    async loadPatientHistory() {
        const historyLoading = document.getElementById('historyLoading');
        if (historyLoading) {
            historyLoading.classList.remove('hidden');
        }
        
        try {
            // Build query parameters
            const params = new URLSearchParams();
            if (this.historyFilters.search) params.append('search', this.historyFilters.search);
            if (this.historyFilters.status) params.append('status', this.historyFilters.status);
            if (this.historyFilters.date) params.append('date', this.historyFilters.date);
            
            const response = await fetch(`api/patient_history.php?${params.toString()}`);
            const data = await response.json();
            
            if (data.success) {
                this.patientHistory = data.patients;
                this.renderHistoryTable(data.patients);
                this.updateHistoryStatistics(data.statistics);
            } else {
                this.showError('Failed to load patient history: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading patient history:', error);
            this.showError('Failed to load patient history');
        } finally {
            if (historyLoading) {
                historyLoading.classList.add('hidden');
            }
        }
    }
    
    renderHistoryTable(patients) {
        const tbody = document.getElementById('historyTableBody');
        if (!tbody) return;
        
        if (!patients || patients.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="padding: 20px; text-align: center; color: #6b7280;">
                        No patients found matching your criteria
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = patients.map(patient => {
            const statusClass = patient.status ? patient.status.toLowerCase().replace(' ', '-') : 'unknown';
            const statusColor = {
                'waiting': '#f59e0b',
                'in-progress': '#3b82f6', 
                'completed': '#10b981'
            }[statusClass] || '#6b7280';
            
            return `
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px;">${patient.id}</td>
                    <td style="padding: 12px; font-weight: 500;">${patient.patient_name || 'N/A'}</td>
                    <td style="padding: 12px;">${patient.age || 'N/A'}</td>
                    <td style="padding: 12px;">${patient.contact || 'N/A'}</td>
                    <td style="padding: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="${patient.symptoms || 'N/A'}">${patient.symptoms || 'N/A'}</td>
                    <td style="padding: 12px;">
                        <span style="background: ${statusColor}20; color: ${statusColor}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            ${patient.status || 'Unknown'}
                        </span>
                    </td>
                    <td style="padding: 12px;">${patient.formatted_check_in || 'N/A'}</td>
                    <td style="padding: 12px;">
                        <span style="background: ${patient.priority === 'Urgent' ? '#dc262620' : '#f3f4f620'}; color: ${patient.priority === 'Urgent' ? '#dc2626' : '#6b7280'}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            ${patient.priority || 'Regular'}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    updateHistoryStatistics(stats) {
        if (!stats) return;
        
        const totalElement = document.getElementById('totalHistoryPatients');
        const completedElement = document.getElementById('completedHistoryPatients');
        const todayElement = document.getElementById('todayHistoryPatients');
        
        if (totalElement) totalElement.textContent = stats.total || 0;
        if (completedElement) completedElement.textContent = stats.completed || 0;
        if (todayElement) todayElement.textContent = stats.today || 0;
    }
}

// Initialize the app when the page loads
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new HealthQueueApp();
});