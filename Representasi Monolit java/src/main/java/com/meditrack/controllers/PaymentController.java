package com.meditrack.controllers;

import com.meditrack.services.*;

/**
 * ANALYTICS CONTROLLER - HTTP Endpoints
 */
public class AnalyticsController {
    private AnalyticsService analyticsService;
    
    public AnalyticsController() {
        this.analyticsService = AnalyticsService.getInstance();
    }
    
    // GET /api/analytics/dashboard
    public java.util.Map<String, Object> getDashboard() {
        System.out.println("GET /api/analytics/dashboard");
        return analyticsService.getDashboardMetrics();
    }
    
    // GET /api/analytics/users/{userId}
    public java.util.Map<String, Object> getUserActivity(int userId) {
        System.out.println("GET /api/analytics/users/" + userId);
        return analyticsService.getUserActivityReport(userId);
    }
    
    // GET /api/analytics/health
    public java.util.Map<String, Object> getSystemHealth() {
        System.out.println("GET /api/analytics/health");
        return analyticsService.getSystemHealth();
    }
}
