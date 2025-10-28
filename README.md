# Health Queue Management System - MVC Architecture

A PHP-based healthcare queue management system with proper MVC (Model-View-Controller) architecture for XAMPP localhost.

## Features

- **Patient Registration**: Add new patients with separate first/last names
- **Queue Management**: View waiting patients and assign doctors
- **Doctor Management**: View doctor availability and workload  
- **Real-time Updates**: Track consultation progress with completion times
- **Dynamic Reports & Analytics**: Live statistics calculated from actual data
- **Wait Time Tracking**: Automatic calculation of average wait times
- **Service Distribution**: Real-time analysis of service types
- **Responsive Design**: Works on desktop and mobile devices
- **MVC Architecture**: Clean separation of concerns with Models, Views, and Controllers

## MVC Structure

The application follows a proper Model-View-Controller pattern:

### Models (app/models/)
- `Doctor.php` - Handles doctor data and operations
- `Patient.php` - Manages patient records and queue operations
- `Appointment.php` - Handles appointment scheduling and tracking

### Views (app/views/)
- `index.php` - Main application interface (HTML structure)

### Controllers (app/controllers/)
- `DoctorController.php` - Handles doctor-related requests
- `PatientController.php` - Manages patient operations (add, assign, complete)
- `AppointmentController.php` - Handles appointment operations
- `StatisticsController.php` - Generates reports and analytics

### Configuration (config/)
- `database.php` - Database connection and utility functions

### Public Directory (public/)
- `index.php` - Main entry point
- `api/` - API endpoints that use controllers
- `assets/` - CSS, JS, and static files
- `setup.php` - Database setup script
- `generate_demo_data.php` - Sample data generator

## Installation

### Prerequisites
- XAMPP with Apache and MySQL running
- PHP 7.4 or higher
- Modern web browser

### Setup Steps

1. **Start XAMPP**
   - Start Apache and MySQL services in XAMPP Control Panel

2. **Create Database**
   - Go to: `http://localhost/ITCC301/draft_mvc/public/setup.php`
   - This will create the database with the updated structure and sample doctors

3. **Add Demo Data (Optional)**
   - Go to: `http://localhost/ITCC301/draft_mvc/public/generate_demo_data.php`
   - This adds sample patients for testing the system

4. **Access the Application**
   - Go to: `http://localhost/ITCC301/draft_mvc/public/`
   - The application should load with the queue management interface

## Project Structure

```
draft_mvc/
├── app/                           # Application logic
│   ├── controllers/               # Controllers (handle requests)
│   │   ├── DoctorController.php
│   │   ├── PatientController.php
│   │   ├── AppointmentController.php
│   │   └── StatisticsController.php
│   ├── models/                    # Models (data layer)
│   │   ├── Doctor.php
│   │   ├── Patient.php
│   │   └── Appointment.php
│   └── views/                     # Views (presentation layer)
│       └── index.php
├── config/                        # Configuration files
│   └── database.php
├── public/                        # Public web directory
│   ├── index.php                  # Main entry point
│   ├── setup.php                  # Database setup
│   ├── generate_demo_data.php     # Demo data generator
│   ├── api/                       # API endpoints
│   │   ├── doctors.php
│   │   ├── patients.php
│   │   ├── add_patient.php
│   │   ├── assign_doctor.php
│   │   ├── complete_consultation.php
│   │   ├── appointments.php
│   │   ├── monthly_reports.php
│   │   └── statistics.php
│   └── assets/                    # Static assets
│       ├── css/
│       │   └── style.css
│       └── js/
│           └── app.js
└── README.md                      # This file
```

## MVC Benefits

### Separation of Concerns
- **Models** handle all data operations and business logic
- **Views** handle presentation and user interface
- **Controllers** handle user input and coordinate between models and views

### Code Organization
- Clean, organized code structure
- Easy to maintain and extend
- Better code reusability
- Easier unit testing

### Scalability
- Easy to add new features
- Simple to modify existing functionality
- Clear code organization for team development

## Usage

### 1. Patient Registration
- Click "Patient Registration" tab
- Fill in patient details
- Select priority (Regular/Urgent)
- Choose service type
- Click "Add to Queue"

### 2. Queue Management
- View waiting patients in priority order
- Assign available doctors to patients
- Complete consultations when finished

### 3. Doctor Overview
- View all doctors and their availability
- Check current patient load
- See specialties and expertise

### 4. Reports & Analytics
- View daily statistics
- Monitor doctor utilization
- Access monthly trends

## Database Tables

- `doctors` - Medical staff information
- `patients` - Patient records and queue status
- `appointments` - Doctor-patient assignments
- `monthly_reports` - Historical data for analytics

## API Endpoints

All API endpoints return JSON responses and use the MVC controllers:

- `GET /public/api/doctors.php` - Get all doctors
- `GET /public/api/patients.php` - Get all patients
- `POST /public/api/add_patient.php` - Add new patient
- `POST /public/api/assign_doctor.php` - Assign doctor to patient
- `POST /public/api/complete_consultation.php` - Mark consultation as complete
- `GET /public/api/appointments.php` - Get all appointments
- `GET /public/api/monthly_reports.php` - Get monthly statistics
- `GET /public/api/statistics.php` - Get current statistics

## Database Tables

- `doctors` - Medical staff information
- `patients` - Patient records and queue status
- `appointments` - Doctor-patient assignments

## Development Guidelines

### Adding New Features

1. **Create Model** (if needed) in `app/models/`
2. **Create Controller** in `app/controllers/`
3. **Add API endpoint** in `public/api/`
4. **Update View** (if needed) in `app/views/`

### Code Standards

- Follow PSR-4 autoloading standards
- Use meaningful class and method names
- Add proper error handling
- Include input validation
- Use prepared statements for database queries

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Run `public/setup.php` to create the database

### API Errors
- Check Apache error logs
- Ensure all PHP files have proper permissions
- Verify XAMPP is running on default ports

### Browser Issues
- Clear browser cache
- Check browser console for JavaScript errors
- Ensure JavaScript is enabled

## Customization

### Adding New Service Types
1. Update the service type options in `app/views/index.php`
2. Modify validation in `PatientController.php` if needed

### Changing Database Settings
1. Edit `config/database.php`
2. Update database connection parameters

### Styling Changes
1. Modify `public/assets/css/style.css`
2. Update color schemes, fonts, and layouts

### Adding New Models
1. Create new model class in `app/models/`
2. Follow existing model patterns
3. Include database connection and methods

### Adding New Controllers
1. Create new controller in `app/controllers/`
2. Include required models
3. Add proper error handling and validation

## Support

For issues or questions:
1. Check XAMPP installation and configuration
2. Verify database setup completed successfully
3. Check browser console for errors
4. Review PHP error logs in XAMPP
5. Ensure proper MVC structure is maintained

## License

This project is for educational purposes. Feel free to modify and use as needed.

## Changelog

### MVC Restructure
- Separated logic into proper MVC pattern
- Moved business logic to Models
- Created Controllers for request handling
- Organized files into logical directory structure
- Improved code maintainability and scalability
- Added proper error handling and validation
- Maintained all original functionality and interfaces
