package httpclient

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"time"
)

// Client wraps HTTP client with retry and circuit breaker logic
type Client struct {
	baseURL    string
	httpClient *http.Client
	maxRetries int
	retryDelay time.Duration
	timeout    time.Duration
	breaker    *CircuitBreaker
}

// NewClient creates new HTTP client with defaults
func NewClient(baseURL string) *Client {
	return &Client{
		baseURL: baseURL,
		httpClient: &http.Client{
			Timeout: 5 * time.Second,
		},
		maxRetries: 3,
		retryDelay: 1 * time.Second,
		timeout:    5 * time.Second,
		breaker:    NewCircuitBreaker(5, 30*time.Second),
	}
}

// Request represents an HTTP request
type Request struct {
	Method  string
	Path    string
	Headers map[string]string
	Body    interface{}
}

// Response represents HTTP response
type Response struct {
	StatusCode int
	Body       []byte
	Headers    http.Header
}

// Do performs HTTP request with retry and circuit breaker
func (c *Client) Do(req *Request) (*Response, error) {
	// Check circuit breaker
	if !c.breaker.CanExecute() {
		return nil, fmt.Errorf("circuit breaker is open")
	}

	var lastErr error
	for attempt := 0; attempt < c.maxRetries; attempt++ {
		resp, err := c.doRequest(req)

		// Record response in circuit breaker
		if err != nil {
			c.breaker.RecordFailure()
			lastErr = err
		} else {
			c.breaker.RecordSuccess()
			return resp, nil
		}

		// Exponential backoff
		if attempt < c.maxRetries-1 {
			backoff := time.Duration(1<<uint(attempt)) * c.retryDelay
			time.Sleep(backoff)
		}
	}

	return nil, fmt.Errorf("max retries exceeded: %w", lastErr)
}

// doRequest performs single HTTP request
func (c *Client) doRequest(req *Request) (*Response, error) {
	url := fmt.Sprintf("%s%s", c.baseURL, req.Path)

	// Create HTTP request
	httpReq, err := http.NewRequest(req.Method, url, nil)
	if err != nil {
		return nil, err
	}

	// Set headers
	if req.Headers != nil {
		for key, value := range req.Headers {
			httpReq.Header.Set(key, value)
		}
	}
	httpReq.Header.Set("Content-Type", "application/json")
	httpReq.Header.Set("User-Agent", "MediTrack-Service-Client/1.0")

	// Set body if provided
	if req.Body != nil {
		bodyBytes, err := json.Marshal(req.Body)
		if err != nil {
			return nil, err
		}
		httpReq.Body = io.NopCloser(bytes.NewReader(bodyBytes))
	}

	// Execute request
	httpResp, err := c.httpClient.Do(httpReq)
	if err != nil {
		return nil, err
	}
	defer httpResp.Body.Close()

	// Read response body
	body, err := io.ReadAll(httpResp.Body)
	if err != nil {
		return nil, err
	}

	return &Response{
		StatusCode: httpResp.StatusCode,
		Body:       body,
		Headers:    httpResp.Header,
	}, nil
}

// GetJSON performs GET request and unmarshal JSON response
func (c *Client) GetJSON(path string, result interface{}) error {
	resp, err := c.Do(&Request{
		Method: "GET",
		Path:   path,
	})
	if err != nil {
		return err
	}

	if resp.StatusCode >= 400 {
		return fmt.Errorf("HTTP %d: %s", resp.StatusCode, string(resp.Body))
	}

	return json.Unmarshal(resp.Body, result)
}

// PostJSON performs POST request and unmarshal JSON response
func (c *Client) PostJSON(path string, payload interface{}, result interface{}) error {
	resp, err := c.Do(&Request{
		Method: "POST",
		Path:   path,
		Body:   payload,
	})
	if err != nil {
		return err
	}

	if resp.StatusCode >= 400 {
		return fmt.Errorf("HTTP %d: %s", resp.StatusCode, string(resp.Body))
	}

	if result != nil {
		return json.Unmarshal(resp.Body, result)
	}

	return nil
}

// PutJSON performs PUT request
func (c *Client) PutJSON(path string, payload interface{}, result interface{}) error {
	resp, err := c.Do(&Request{
		Method: "PUT",
		Path:   path,
		Body:   payload,
	})
	if err != nil {
		return err
	}

	if resp.StatusCode >= 400 {
		return fmt.Errorf("HTTP %d: %s", resp.StatusCode, string(resp.Body))
	}

	if result != nil {
		return json.Unmarshal(resp.Body, result)
	}

	return nil
}

// DeleteJSON performs DELETE request
func (c *Client) DeleteJSON(path string, result interface{}) error {
	resp, err := c.Do(&Request{
		Method: "DELETE",
		Path:   path,
	})
	if err != nil {
		return err
	}

	if resp.StatusCode >= 400 {
		return fmt.Errorf("HTTP %d: %s", resp.StatusCode, string(resp.Body))
	}

	if result != nil {
		return json.Unmarshal(resp.Body, result)
	}

	return nil
}
