// Core Application Class and Initialization
class HealthQueueApp {
    constructor() {
        this.currentTab = 'queue';
        this.doctors = [];
        this.patients = [];
        this.appointments = [];
        this.monthlyReports = [];
        this.recommendations = [];
        this.selectedDoctors = {};
        this.patientHistory = [];
        this.historyFilters = {
            search: '',
            status: '',
            date: ''
        };
        
        // Show loading immediately when app starts
        this.showLoading(true);
        this.init();
    }
    
    async init() {
        try {
            await this.loadData();
            this.setupEventListeners();
            this.showTab('queue');
            console.log('App initialized successfully');
        } catch (error) {
            console.error('Error during app initialization:', error);
            this.showError('Failed to initialize application. Please refresh the page.');
        } finally {
            // Always ensure loading is hidden
            this.showLoading(false);
        }
    }
    
    async loadData() {
        try {
            console.log('Loading application data...');
            const [doctorsRes, patientsRes, appointmentsRes, reportsRes, statsRes, recommendationsRes] = await Promise.all([
                fetch('api/doctors.php'),
                fetch('api/patients.php'),
                fetch('api/appointments.php'),
                fetch('api/monthly_reports.php'),
                fetch('api/statistics.php'),
                fetch('api/recommendations.php')
            ]);
            
            this.doctors = await doctorsRes.json();
            this.patients = await patientsRes.json();
            this.appointments = await appointmentsRes.json();
            this.monthlyReports = await reportsRes.json();
            this.statistics = await statsRes.json();
            this.recommendations = await recommendationsRes.json();
            
            this.updateDisplay();
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Failed to load data. Please refresh the page.');
        } finally {
            this.showLoading(false);
        }
    }
    
    showTab(tab) {
        this.currentTab = tab;
        
        // Update nav buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
        
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show current tab
        document.getElementById(`${tab}Tab`).classList.remove('hidden');
        
        // Update content based on tab
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
        this.renderQueue();
        this.renderDoctors();
        this.renderReports();
        // Update statistics on all tabs
        this.updateStatisticsDisplay();
    }
    
    updateStatisticsDisplay() {
        // Update statistics even when not on reports tab
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
    
    showLoading(show) {
        const loader = document.getElementById('loading');
        if (!loader) {
            console.error('Loading element not found!');
            return;
        }
        
        console.log('Setting loading state to:', show);
        if (show) {
            loader.classList.remove('hidden');
            loader.style.display = 'flex';
        } else {
            loader.classList.add('hidden');
            loader.style.display = 'none';
        }
    }
    
    showError(message) {
        alert(message); // In a real app, you'd use a better notification system
    }
}

// Initialize the app when the page loads
let app;
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the app - let the loading spinner show during initialization
    app = new HealthQueueApp();
});