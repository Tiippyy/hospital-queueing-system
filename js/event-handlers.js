// Event Listeners and User Interactions
HealthQueueApp.prototype.setupEventListeners = function() {
    // Tab navigation
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const tab = e.target.dataset.tab;
            this.showTab(tab);
        });
    });
    
    // Patient registration form
    document.getElementById('addPatientBtn').addEventListener('click', () => {
        this.addPatient();
    });
    
    // History filters - will be set up when history tab is shown
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-tab="history"]')) {
            // Delay to ensure DOM is ready
            setTimeout(() => this.setupHistoryEventListeners(), 100);
        }
    });
};

HealthQueueApp.prototype.setupHistoryEventListeners = function() {
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
};