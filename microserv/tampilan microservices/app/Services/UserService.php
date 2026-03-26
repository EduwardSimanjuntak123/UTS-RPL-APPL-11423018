<?php

namespace App\Services;

use App\Services\Api\ApiClient;
use Exception;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Get all users
     */
    public function getAllUsers(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/users', $filters);
            return $response['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById(int $id): ?array
    {
        try {
            $response = $this->apiClient->get("/api/v1/users/{$id}");
            return $response['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Failed to fetch user {$id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new user
     */
    public function createUser(array $data): array
    {
        try {
            $response = $this->apiClient->post('/api/v1/users', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            throw new Exception('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function updateUser(int $id, array $data): array
    {
        try {
            $response = $this->apiClient->put("/api/v1/users/{$id}", $data);
            return $response;
        } catch (Exception $e) {
            Log::error("Failed to update user {$id}: " . $e->getMessage());
            throw new Exception('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): array
    {
        try {
            $response = $this->apiClient->delete("/api/v1/users/{$id}");
            return $response;
        } catch (Exception $e) {
            Log::error("Failed to delete user {$id}: " . $e->getMessage());
            throw new Exception('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Login user
     */
    public function login(string $email, string $password): array
    {
        try {
            $response = $this->apiClient->post('/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

            if (isset($response['token'])) {
                session(['api_token' => $response['token']]);
                session(['user' => $response['user'] ?? []]);
            }

            return $response;
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Register new user
     */
    public function register(array $data): array
    {
        try {
            $response = $this->apiClient->post('/auth/register', $data);
            return $response;
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all roles
     */
    public function getAllRoles(): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/roles');
            return $response['roles'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch roles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(array $filters = []): array
    {
        try {
            $response = $this->apiClient->get('/api/v1/audit-logs', $filters);
            return $response['logs'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to fetch audit logs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user audit logs
     */
    public function getUserAuditLogs(int $userId): array
    {
        try {
            $response = $this->apiClient->get("/api/v1/audit-logs/user/{$userId}");
            return $response['logs'] ?? [];
        } catch (Exception $e) {
            Log::error("Failed to fetch audit logs for user {$userId}: " . $e->getMessage());
            return [];
        }
    }
}
