package com.meditrack.controllers;

import com.meditrack.services.*;
import com.meditrack.models.*;

/**
 * USER CONTROLLER - HTTP Endpoints
 * Demonstrasi bagaimana endpoints menerima requests
 */
public class UserController {
    private UserService userService;
    
    public UserController() {
        // TIGHT COUPLING: Direct instantiation of service
        this.userService = UserService.getInstance();
    }
    
    // GET /api/users/{id}
    public User getUserById(int id) {
        System.out.println("GET /api/users/" + id);
        return userService.getUserById(id);
    }
    
    // GET /api/users
    public java.util.List<User> getAllUsers() {
        System.out.println("GET /api/users");
        return userService.getAllUsers();
    }
    
    // POST /api/users
    public boolean createUser(User user) {
        System.out.println("POST /api/users - Creating user: " + user.getName());
        return userService.createUser(user);
    }
    
    // PUT /api/users/{id}
    public boolean updateUser(User user) {
        System.out.println("PUT /api/users/" + user.getId());
        return userService.updateUser(user);
    }
    
    // DELETE /api/users/{id}
    public boolean deleteUser(int id) {
        System.out.println("DELETE /api/users/" + id);
        return userService.deleteUser(id);
    }
    
    // POST /api/users/{id}/deactivate (Monolithic behavior)
    public void deactivateUser(int userId) {
        System.out.println("POST /api/users/" + userId + "/deactivate");
        userService.deactivateUserAndCancelAppointments(userId);
    }
}
