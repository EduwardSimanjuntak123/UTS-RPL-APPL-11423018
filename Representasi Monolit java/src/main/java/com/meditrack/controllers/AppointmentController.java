package com.meditrack.controllers;

import com.meditrack.services.*;
import com.meditrack.models.*;

/**
 * APPOINTMENT CONTROLLER - HTTP Endpoints
 */
public class AppointmentController {
    private AppointmentService appointmentService;
    private UserService userService;
    
    public AppointmentController() {
        // TIGHT COUPLING: Direct service dependencies
        this.appointmentService = AppointmentService.getInstance();
        this.userService = UserService.getInstance();
    }
    
    // GET /api/appointments/{id}
    public Appointment getAppointmentById(int id) {
        System.out.println("GET /api/appointments/" + id);
        return appointmentService.getAppointmentById(id);
    }
    
    // POST /api/appointments
    public boolean createAppointment(Appointment appointment) {
        System.out.println("POST /api/appointments");
        return appointmentService.createAppointment(appointment);
    }
    
    // PUT /api/appointments/{id}/cancel
    public boolean cancelAppointment(int id) {
        System.out.println("PUT /api/appointments/" + id + "/cancel");
        return appointmentService.cancelAppointment(id);
    }
    
    // GET /api/users/{userId}/appointments
    public java.util.List<Appointment> getUserAppointments(int userId) {
        System.out.println("GET /api/users/" + userId + "/appointments");
        return appointmentService.getUserAppointments(userId);
    }
}
