<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Exception;

class ApiClient
{
    /**
     * Base URL untuk API Gateway/Microservices
     */
    protected string $baseUrl;
    protected string $bearerToken;
    protected int $timeout;
    protected bool $debug;

    public function __construct()
    {
        $this->baseUrl = config('microservices.base_url');
        $this->timeout = config('microservices.timeout', 30);
        $this->debug = config('microservices.debug', false);
        $this->bearerToken = $this->getBearerToken();
    }

    /**
     * Ambil bearer token dari session
     */
    protected function getBearerToken(): string
    {
        $token = session('api_token') ?? (Auth::check() ? Auth::user()?->api_token : null) ?? null;
        return $token ? "Bearer {$token}" : '';
    }

    /**
     * GET request
     */
    public function get(string $endpoint, array $params = [], array $options = []): mixed
    {
        return $this->request('GET', $endpoint, $params, $options);
    }

    /**
     * POST request
     */
    public function post(string $endpoint, array $data = [], array $options = []): mixed
    {
        return $this->request('POST', $endpoint, $data, $options);
    }

    /**
     * PUT request
     */
    public function put(string $endpoint, array $data = [], array $options = []): mixed
    {
        return $this->request('PUT', $endpoint, $data, $options);
    }

    /**
     * DELETE request
     */
    public function delete(string $endpoint, array $options = []): mixed
    {
        return $this->request('DELETE', $endpoint, [], $options);
    }

    /**
     * Main request method dengan retry logic
     */
    protected function request(string $method, string $endpoint, array $data = [], array $options = []): mixed
    {
        // Remove leading slash if present
        $endpoint = ltrim($endpoint, '/');
        $url = "{$this->baseUrl}/{$endpoint}";

        // Check cache untuk GET requests
        if ($method === 'GET' && config('microservices.cache.enabled')) {
            $cacheKey = "api_cache_{$endpoint}_" . md5(json_encode($data));
            $cached = Cache::get($cacheKey);
            if ($cached) {
                if ($this->debug) {
                    Log::info("API Cache HIT: {$endpoint}");
                }
                return $cached;
            }
        }

        $retries = config('microservices.retry.enabled') ? config('microservices.retry.times', 3) : 1;
        $lastException = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $response = $this->sendRequest($method, $url, $data, $options);

                // Log successful request
                if ($this->debug) {
                    Log::info("API Request: {$method} {$endpoint}", [
                        'status' => $response->status(),
                        'attempt' => $attempt,
                    ]);
                }

                $result = $response->json();

                // Cache successful GET responses
                if ($method === 'GET' && config('microservices.cache.enabled')) {
                    $cacheKey = "api_cache_{$endpoint}_" . md5(json_encode($data));
                    Cache::put($cacheKey, $result, config('microservices.cache.ttl', 3600));
                }

                return $result;
            } catch (Exception $e) {
                $lastException = $e;

                // Log failed attempt
                Log::warning("API Request Failed (Attempt {$attempt}/{$retries}): {$method} {$endpoint}", [
                    'error' => $e->getMessage(),
                    'delay' => config('microservices.retry.delay', 100) . 'ms',
                ]);

                // Wait before retry
                if ($attempt < $retries) {
                    usleep(config('microservices.retry.delay', 100) * 1000);
                }
            }
        }

        // All retries failed
        if ($lastException) {
            Log::error("API Request Failed After {$retries} Attempts: {$method} {$endpoint}", [
                'error' => $lastException->getMessage(),
            ]);
            throw $lastException;
        }

        throw new Exception("Failed to complete API request to {$endpoint}");
    }

    /**
     * Send HTTP request
     */
    protected function sendRequest(string $method, string $url, array $data = [], array $options = []): mixed
    {
        $request = Http::timeout($this->timeout)
            ->withHeaders($this->getHeaders($options))
            ->acceptJson();

        // Add bearer token if available
        if (!empty($this->bearerToken)) {
            $request->withToken(str_replace('Bearer ', '', $this->bearerToken));
        }

        return match ($method) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url),
            default => throw new Exception("Unsupported HTTP method: {$method}"),
        };
    }

    /**
     * Get request headers
     */
    protected function getHeaders(array $options = []): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Add custom headers from options
        if (isset($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }

        return $headers;
    }

    /**
     * Set base URL (for direct service calls if needed)
     */
    public function setBaseUrl(string $url): self
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Set bearer token manually
     */
    public function setToken(string $token): self
    {
        $this->bearerToken = "Bearer {$token}";
        return $this;
    }
}
