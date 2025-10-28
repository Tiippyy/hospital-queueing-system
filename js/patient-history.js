// Patient History Functions
HealthQueueApp.prototype.renderHistory = function() {
    this.setupHistoryEventListeners();
    this.loadPatientHistory();
};

HealthQueueApp.prototype.loadPatientHistory = async function() {
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
};

HealthQueueApp.prototype.renderHistoryTable = function(patients) {
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
};

HealthQueueApp.prototype.updateHistoryStatistics = function(stats) {
    if (!stats) return;
    
    const totalElement = document.getElementById('totalHistoryPatients');
    const completedElement = document.getElementById('completedHistoryPatients');
    const todayElement = document.getElementById('todayHistoryPatients');
    
    if (totalElement) totalElement.textContent = stats.total || 0;
    if (completedElement) completedElement.textContent = stats.completed || 0;
    if (todayElement) todayElement.textContent = stats.today || 0;
};