// API Operations and Patient Actions
HealthQueueApp.prototype.assignDoctor = async function(patientId) {
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
};

HealthQueueApp.prototype.completeConsultation = async function(patientId) {
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
};

HealthQueueApp.prototype.addPatient = async function() {
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
};

HealthQueueApp.prototype.renderRegistration = function() {
    // Registration form is already rendered in HTML
    // This method can be used for any dynamic registration logic
    console.log('Registration tab rendered');
};