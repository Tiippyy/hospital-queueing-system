// Reports and Analytics Functions
HealthQueueApp.prototype.renderReports = function() {
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
};

HealthQueueApp.prototype.renderRecommendations = function() {
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
};

HealthQueueApp.prototype.renderCharts = function() {
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
};