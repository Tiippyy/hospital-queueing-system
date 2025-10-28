// Queue Management Functions
HealthQueueApp.prototype.renderQueue = function() {
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
};

HealthQueueApp.prototype.createPatientCard = function(patient) {
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
};

HealthQueueApp.prototype.createInProgressCard = function(patient) {
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
};

HealthQueueApp.prototype.getDoctorRecommendations = function(symptoms, serviceType) {
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
};