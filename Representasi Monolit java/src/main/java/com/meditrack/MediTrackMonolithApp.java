package com.meditrack;

import com.meditrack.services.*;
import com.meditrack.controllers.*;
import com.meditrack.models.*;
import java.time.LocalDateTime;

/**
 * MONOLITHIC APPLICATION
 * 
 * Demonstrasi Arsitektur Monolitik:
 * ✓ Semua modul dalam satu aplikasi
 * ✓ Database tunggal yang dibagikan
 * ✓ Services saling memanggil secara langsung
 * ✓ Ketergantungan erat antar service
 * ✓ Scaling vertikal saja
 */
public class MediTrackMonolithApp {
    
    // Singleton services - shared across entire application
    private UserService userService;
    private AppointmentService appointmentService;
    private MedicalService medicalService;
    private PharmacyService pharmacyService;
    private PaymentService paymentService;
    private AnalyticsService analyticsService;
    
    // Controllers
    private UserController userController;
    private AppointmentController appointmentController;
    private MedicalController medicalController;
    private PharmacyController pharmacyController;
    private AnalyticsController analyticsController;
    
    public MediTrackMonolithApp() {
        System.out.println("\n" + "=".repeat(80));
        System.out.println("MEDITRACK - MONOLITHIC ARCHITECTURE SIMULATION");
        System.out.println("=".repeat(80));
        System.out.println("\n📚 Inisialisasi Monolithic Application...\n");
        
        // Initialize all services
        initializeServices();
        
        // Initialize all controllers
        initializeControllers();
        
        System.out.println("\n✓ Monolithic Application Ready");
        System.out.println("✓ Single Database Connection");
        System.out.println("✓ All Services in Memory");
        System.out.println("✓ Tight Coupling Active");
        System.out.println("\n" + "=".repeat(80) + "\n");
    }
    
    private void initializeServices() {
        System.out.println("🔧 Initializing Services (MONOLITHIC):");
        
        // Get singleton instances - semua service berbagi satu database
        userService = UserService.getInstance();
        System.out.println("  ✓ UserService initialized");
        
        appointmentService = AppointmentService.getInstance();
        System.out.println("  ✓ AppointmentService initialized (depends on UserService & PaymentService)");
        
        medicalService = MedicalService.getInstance();
        System.out.println("  ✓ MedicalService initialized (depends on UserService)");
        
        pharmacyService = PharmacyService.getInstance();
        System.out.println("  ✓ PharmacyService initialized (depends on UserService & PaymentService)");
        
        paymentService = PaymentService.getInstance();
        System.out.println("  ✓ PaymentService initialized (depends on UserService)");
        
        analyticsService = AnalyticsService.getInstance();
        System.out.println("  ✓ AnalyticsService initialized (reads all tables)");
    }
    
    private void initializeControllers() {
        System.out.println("\n🎮 Initializing HTTP Controllers:");
        
        userController = new UserController();
        System.out.println("  ✓ UserController initialized");
        
        appointmentController = new AppointmentController();
        System.out.println("  ✓ AppointmentController initialized");
        
        medicalController = new MedicalController();
        System.out.println("  ✓ MedicalController initialized");
        
        pharmacyController = new PharmacyController();
        System.out.println("  ✓ PharmacyController initialized");
        
        analyticsController = new AnalyticsController();
        System.out.println("  ✓ AnalyticsController initialized");
    }
    
    /**
     * DEMONSTRASI 1: Simple User Creation
     */
    public void demonstrateUserCreation() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("DEMO 1: Create User (Simple Operation)");
        System.out.println("═".repeat(80));
        
        User doctor = new User("Dr. Budi", "budi@meditrack.com", "08123456789", "doctor");
        doctor.setPassword("hashed_password");
        doctor.setSpecialty("Cardiology");
        
        boolean created = userController.createUser(doctor);
        System.out.println("✓ User created: " + created);
    }
    
    /**
     * DEMONSTRASI 2: Tight Coupling - Create Appointment
     * Appointment service harus memanggil UserService dan PaymentService
     */
    public void demonstrateAppointmentCreation() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("DEMO 2: Create Appointment (TIGHT COUPLING - Multiple Service Calls)");
        System.out.println("═".repeat(80));
        
        Appointment appointment = new Appointment();
        appointment.setPatientId(1);
        appointment.setDoctorId(2);
        appointment.setAppointmentDate(LocalDateTime.now().plusDays(1));
        appointment.setType("consultation");
        
        System.out.println("\n⚠️  MONOLITHIC PATTERN: Creating appointment requires:");
        System.out.println("   1. Check if patient exists (UserService)");
        System.out.println("   2. Check if doctor exists (UserService)");
        System.out.println("   3. Check if patient has pending payments (PaymentService)");
        System.out.println("   4. Create appointment (AppointmentService)\n");
        
        boolean created = appointmentController.createAppointment(appointment);
        System.out.println("\n✓ Appointment created: " + created);
    }
    
    /**
     * DEMONSTRASI 3: Cascading Operations
     * Ketika user deactivate, banyak service yang terdampak
     */
    public void demonstrateCascadingOperations() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("DEMO 3: Cascading Operations (MONOLITHIC PROBLEM)");
        System.out.println("═".repeat(80));
        
        System.out.println("\n⚠️  MONOLITHIC: When user is deactivated:");
        System.out.println("   1. UserService.deactivateUser()");
        System.out.println("      ├─ CALLS AppointmentService.cancelUserAppointments()");
        System.out.println("      ├─ CALLS PaymentService.cancelUserPayments()");
        System.out.println("      └─ AFFECTS Analytics data\n");
        
        userController.deactivateUser(1);
        
        System.out.println("\n⚠️  MONOLITHIC PROBLEMS:");
        System.out.println("   • Complex transaction across multiple services");
        System.out.println("   • Risk of partial failure");
        System.out.println("   • Hard to rollback");
        System.out.println("   • Tight coupling makes maintenance difficult");
    }
    
    /**
     * DEMONSTRASI 4: Analytics reading all tables
     */
    public void demonstrateAnalytics() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("DEMO 4: Analytics (Complex Cross-Service Queries)");
        System.out.println("═".repeat(80));
        
        System.out.println("\n⚠️  MONOLITHIC: Analytics reads from all tables:");
        System.out.println("   SELECT * FROM users");
        System.out.println("   SELECT * FROM appointments");
        System.out.println("   SELECT * FROM medical_records");
        System.out.println("   SELECT * FROM payments");
        System.out.println("   SELECT * FROM drug_inventory\n");
        
        var dashboard = analyticsController.getDashboard();
        System.out.println("✓ Dashboard generated");
        System.out.println("  Data: " + dashboard);
    }
    
    /**
     * DEMONSTRASI 5: Shared Database Problem
     */
    public void demonstrateSharedDatabaseProblem() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("DEMO 5: Shared Database Problem");
        System.out.println("═".repeat(80));
        
        System.out.println("\n🔴 MONOLITHIC PROBLEMS with Shared Database:\n");
        
        System.out.println("1. SINGLE POINT OF FAILURE");
        System.out.println("   • Database down = entire application down");
        System.out.println("   • Cannot isolate failures by module\n");
        
        System.out.println("2. SCHEMA COUPLING");
        System.out.println("   • Changing table structure affects ALL services");
        System.out.println("   • Must coordinate schema changes across team\n");
        
        System.out.println("3. RESOURCE CONTENTION");
        System.out.println("   • Analytics queries can lock appointment tables");
        System.out.println("   • Payment service competes with user service for connections\n");
        
        System.out.println("4. SCALING DIFFICULTY");
        System.out.println("   • Cannot scale pharmacy independently from users");
        System.out.println("   • Must scale entire database together\n");
        
        System.out.println("5. DATA CONSISTENCY");
        System.out.println("   • ACID transactions across all services");
        System.out.println("   • Complex locking and deadlock scenarios");
    }
    
    /**
     * DEMONSTRASI 6: Comparison with Microservices
     */
    public void demonstrateComparison() {
        System.out.println("\n" + "═".repeat(80));
        System.out.println("COMPARISON: Monolithic vs Microservices");
        System.out.println("═".repeat(80));
        
        System.out.println("\n📊 MONOLITHIC ARCHITECTURE:");
        System.out.println("   ✗ One codebase");
        System.out.println("   ✗ One database");
        System.out.println("   ✗ Direct method calls");
        System.out.println("   ✗ Tight coupling");
        System.out.println("   ✗ Vertical scaling only");
        System.out.println("   ✗ Hard to maintain as grows");
        System.out.println("   ✗ Single point of failure");
        System.out.println("   ✓ Simple for small projects");
        System.out.println("   ✓ Easy initial development");
        
        System.out.println("\n📊 MICROSERVICES ARCHITECTURE:");
        System.out.println("   ✓ Multiple codebases");
        System.out.println("   ✓ Separate databases per service");
        System.out.println("   ✓ HTTP/REST/gRPC communication");
        System.out.println("   ✓ Loose coupling");
        System.out.println("   ✓ Independent scaling");
        System.out.println("   ✓ Easy to maintain and update");
        System.out.println("   ✓ Isolated failure domains");
        System.out.println("   ✗ More complex distributed system");
        System.out.println("   ✗ Requires orchestration/deployment tools");
    }
    
    public static void main(String[] args) {
        // Create monolithic application instance
        MediTrackMonolithApp app = new MediTrackMonolithApp();
        
        // Run demonstrations
        app.demonstrateUserCreation();
        app.demonstrateAppointmentCreation();
        app.demonstrateCascadingOperations();
        app.demonstrateAnalytics();
        app.demonstrateSharedDatabaseProblem();
        app.demonstrateComparison();
        
        System.out.println("\n" + "═".repeat(80));
        System.out.println("SIMULATION COMPLETE");
        System.out.println("═".repeat(80));
        System.out.println("\n✓ Monolithic architecture demonstrated");
        System.out.println("✓ See microservices folder for comparison");
        System.out.println("\n");
    }
}
