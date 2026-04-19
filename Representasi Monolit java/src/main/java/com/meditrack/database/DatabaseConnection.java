package com.meditrack.database;

import java.sql.*;

/**
 * SHARED DATABASE CONNECTION - Monolithic Pattern
 * Semua modul menggunakan koneksi database yang sama (single point of failure)
 * Tidak ada isolasi database per service
 */
public class DatabaseConnection {
    private static DatabaseConnection instance;
    private static Connection connection;
    
    private DatabaseConnection() {
        // Singleton - koneksi tunggal untuk seluruh aplikasi
    }
    
    public static synchronized DatabaseConnection getInstance() {
        if (instance == null) {
            instance = new DatabaseConnection();
            initializeConnection();
        }
        return instance;
    }
    
    private static void initializeConnection() {
        try {
            // Koneksi ke database tunggal
            String url = "jdbc:mysql://localhost:3306/meditrack_monolith";
            String user = "root";
            String password = "";
            
            connection = DriverManager.getConnection(url, user, password);
            System.out.println("✓ Database terhubung (Monolithic - Shared Connection)");
        } catch (SQLException e) {
            System.err.println("✗ Gagal koneksi database: " + e.getMessage());
        }
    }
    
    public Connection getConnection() {
        if (connection == null) {
            initializeConnection();
        }
        return connection;
    }
    
    public void executeUpdate(String sql, Object... params) throws SQLException {
        PreparedStatement stmt = connection.prepareStatement(sql);
        for (int i = 0; i < params.length; i++) {
            stmt.setObject(i + 1, params[i]);
        }
        stmt.executeUpdate();
        stmt.close();
    }
    
    public ResultSet executeQuery(String sql, Object... params) throws SQLException {
        PreparedStatement stmt = connection.prepareStatement(sql);
        for (int i = 0; i < params.length; i++) {
            stmt.setObject(i + 1, params[i]);
        }
        return stmt.executeQuery();
    }
}
