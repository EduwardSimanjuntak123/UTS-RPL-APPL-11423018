package com.meditrack.services;

import com.meditrack.database.DatabaseConnection;
import com.meditrack.models.User;
import java.sql.*;
import java.time.LocalDateTime;
import java.util.*;

/**
 * USER SERVICE - Monolithic Pattern
 * Digunakan oleh semua modul lain (tight coupling)
 * Semua operasi user melalui service ini
 */
public class UserService {
    private static UserService instance;
    private DatabaseConnection db;
    
    private UserService() {
        this.db = DatabaseConnection.getInstance();
    }
    
    public static synchronized UserService getInstance() {
        if (instance == null) {
            instance = new UserService();
        }
        return instance;
    }
    
    /**
     * DIRECT METHOD CALL - Monolithic
     * Appointment, Medical, Pharmacy services memanggil method ini secara langsung
     */
    public User getUserById(int id) {
        String sql = "SELECT * FROM users WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                User user = new User();
                user.setId(rs.getInt("id"));
                user.setName(rs.getString("name"));
                user.setEmail(rs.getString("email"));
                user.setPhone(rs.getString("phone"));
                user.setRole(rs.getString("role"));
                user.setStatus(rs.getString("status"));
                return user;
            }
        } catch (SQLException e) {
            System.err.println("Error fetching user: " + e.getMessage());
        }
        return null;
    }
    
    public List<User> getAllUsers() {
        List<User> users = new ArrayList<>();
        String sql = "SELECT * FROM users";
        try {
            Statement stmt = db.getConnection().createStatement();
            ResultSet rs = stmt.executeQuery(sql);
            
            while (rs.next()) {
                User user = new User();
                user.setId(rs.getInt("id"));
                user.setName(rs.getString("name"));
                user.setEmail(rs.getString("email"));
                user.setRole(rs.getString("role"));
                users.add(user);
            }
        } catch (SQLException e) {
            System.err.println("Error fetching users: " + e.getMessage());
        }
        return users;
    }
    
    public boolean createUser(User user) {
        String sql = "INSERT INTO users (name, email, password, phone, role, status, created_at) " +
                     "VALUES (?, ?, ?, ?, ?, ?, ?)";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setString(1, user.getName());
            stmt.setString(2, user.getEmail());
            stmt.setString(3, user.getPassword());
            stmt.setString(4, user.getPhone());
            stmt.setString(5, user.getRole());
            stmt.setString(6, user.getStatus());
            stmt.setObject(7, LocalDateTime.now());
            
            stmt.executeUpdate();
            stmt.close();
            return true;
        } catch (SQLException e) {
            System.err.println("Error creating user: " + e.getMessage());
            return false;
        }
    }
    
    public boolean updateUser(User user) {
        String sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setString(1, user.getName());
            stmt.setString(2, user.getEmail());
            stmt.setString(3, user.getPhone());
            stmt.setString(4, user.getStatus());
            stmt.setInt(5, user.getId());
            
            stmt.executeUpdate();
            stmt.close();
            return true;
        } catch (SQLException e) {
            System.err.println("Error updating user: " + e.getMessage());
            return false;
        }
    }
    
    public boolean deleteUser(int id) {
        String sql = "DELETE FROM users WHERE id = ?";
        try {
            PreparedStatement stmt = db.getConnection().prepareStatement(sql);
            stmt.setInt(1, id);
            stmt.executeUpdate();
            stmt.close();
            return true;
        } catch (SQLException e) {
            System.err.println("Error deleting user: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * INTERDEPENDENT OPERATIONS - Monolithic Pattern
     * Service ini berinteraksi dengan service lain secara langsung
     */
    public void deactivateUserAndCancelAppointments(int userId) {
        System.out.println("⚠ Monolithic: Deactivating user and cancelling appointments (tight coupling)");
        
        // Deactivate user
        User user = getUserById(userId);
        if (user != null) {
            user.setStatus("inactive");
            updateUser(user);
            
            // Langsung call AppointmentService
            AppointmentService appointmentService = AppointmentService.getInstance();
            appointmentService.cancelUserAppointments(userId);
            
            // Langsung call PaymentService
            PaymentService paymentService = PaymentService.getInstance();
            paymentService.cancelUserPayments(userId);
        }
    }
}
