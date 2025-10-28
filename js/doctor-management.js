// Doctor Management Functions
HealthQueueApp.prototype.renderDoctors = function() {
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
};

HealthQueueApp.prototype.calculateAverageUtilization = function() {
    if (this.doctors.length === 0) return 0;
    const total = this.doctors.reduce((sum, doctor) => {
        return sum + ((doctor.current_patients / doctor.max_patients_per_day) * 100);
    }, 0);
    return Math.round(total / this.doctors.length);
};