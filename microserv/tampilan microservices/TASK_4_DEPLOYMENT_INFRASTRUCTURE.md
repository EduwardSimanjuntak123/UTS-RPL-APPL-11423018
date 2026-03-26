# TASK 4: DEPLOYMENT STRATEGY & INFRASTRUCTURE PIPELINE

**MediTrack Transformation - Case Study 2**  
**Date**: March 26, 2026  
**Technology Stack**: Golang Microservices + Docker + Kubernetes  
**Focus**: Production Deployment & Operations

---

## 📋 EXECUTIVE SUMMARY

This document outlines the complete deployment strategy for MediTrack's 7 Golang microservices, including infrastructure setup, containerization, orchestration, and CI/CD pipelines.

### Deployment Model: **Containerized Microservices Pattern**
- **Containerization**: Docker
- **Orchestration**: Kubernetes (K8s)
- **CI/CD**: GitHub Actions / GitLab CI
- **Infrastructure**: Cloud (AWS/GCP/Azure) or On-premises
- **Database**: MySQL (DBaaS or Self-managed)
- **Service Communication**: Internal DNS + Service Discovery

---

## 🏗️ ARCHITECTURE LAYERS

```
┌─────────────────────────────────────────────────────┐
│         CLIENT LAYER                                │
│  (Web App, Mobile App, Third-party APIs)           │
└─────────────────────┬───────────────────────────────┘
                      │ HTTPS/HTTP
        ┌─────────────▼──────────────┐
        │   INGRESS CONTROLLER       │
        │  (Nginx/HAProxy)           │
        │  - SSL Termination         │
        │  - Load Balancing          │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │   KUBERNETES CLUSTER       │
        │                            │
        ├────────────────────────────┤
        │   API Gateway Service      │
        │   (Port 3000)              │
        ├────────────────────────────┤
        │   NODE POOL                │
        │                            │
        │   ┌──────┐ ┌──────┐       │
        │   │User  │ │Appt  │       │
        │   │Pod   │ │Pod   │       │
        │   └──┬───┘ └───┬──┘       │
        │      │         │          │
        │   ┌──▼─┐ ┌──┬─┐ ┌──▼──┐  │
        │   │Med │ │Phx│ │Pay  │  │
        │   │Pod │ │Pod│ │Pod  │  │
        │   └────┘ └────┘ └──┬──┘  │
        │                    │     │
        │   ┌──────┐ ┌──────▼────┐ │
        │   │ Anal │ │ Config    │ │
        │   │ Pod  │ │ Maps      │ │
        │   └──────┘ └───────────┘ │
        │                          │
        └──────────────┬───────────┘
                       │
        ┌──────────────▼─────────────┐
        │  PERSISTENT STORAGE LAYER  │
        │                            │
        │  ┌──────────────────────┐  │
        │  │ MySQL Database       │  │
        │  │ Cluster (3 nodes)    │  │
        │  └──────────────────────┘  │
        │                            │
        │  ┌──────────────────────┐  │
        │  │ Redis Cache          │  │
        │  │ (optional)           │  │
        │  └──────────────────────┘  │
        └────────────────────────────┘
```

---

## 🐳 CONTAINERIZATION STRATEGY

### Dockerfile Template for Golang Microservices

```dockerfile
# Stage 1: Build
FROM golang:1.23-alpine AS builder

WORKDIR /app

# Copy go mod files
COPY go.mod go.sum ./

# Download dependencies
RUN go mod download

# Copy source code
COPY . .

# Build the application
RUN CGO_ENABLED=1 GOOS=linux go build -a -installsuffix cgo -o main ./cmd/server

# Stage 2: Runtime (Minimal)
FROM alpine:latest

# Install runtime dependencies
RUN apk --no-cache add ca-certificates

WORKDIR /root/

# Copy binary from builder
COPY --from=builder /app/main .

# Copy config files if needed
COPY --from=builder /app/config ./config

# Expose port
EXPOSE 3001

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD wget --quiet --tries=1 --spider http://localhost:3001/health || exit 1

# Run application
CMD ["./main"]
```

### Docker Build & Push Commands

```bash
# For User Service (example)
cd microservices/user-service

# Build image
docker build -t meditrack/user-service:1.0.0 .
docker build -t meditrack/user-service:latest .

# Push to registry
docker push meditrack/user-service:1.0.0
docker push meditrack/user-service:latest

# Tag for different registries
docker tag meditrack/user-service:1.0.0 gcr.io/project-id/user-service:1.0.0
docker push gcr.io/project-id/user-service:1.0.0

# Tag for AWS ECR
docker tag meditrack/user-service:1.0.0 123456789.dkr.ecr.us-west-2.amazonaws.com/user-service:1.0.0
docker push 123456789.dkr.ecr.us-west-2.amazonaws.com/user-service:1.0.0
```

### Image Optimization

| Strategy | Benefit | Implementation |
|----------|---------|-----------------|
| **Multi-stage build** | Reduce image size | Build → Runtime stages |
| **Alpine base** | Small footprint | `FROM alpine:latest` |
| **No root user** | Security | `RUN adduser -D appuser` |
| **Health checks** | Self-healing | `HEALTHCHECK` in Dockerfile |

### Typical Image Sizes
- With full Go build tools: ~800MB
- With multi-stage + Alpine: ~20-50MB
- With Alpine + minimal deps: ~15MB

---

## ☸️ KUBERNETES DEPLOYMENT

### Deployment YAML for User Service

```yaml
# File: k8s/user-service-deployment.yaml

apiVersion: apps/v1
kind: Deployment
metadata:
  name: user-service
  namespace: meditrack
  labels:
    app: user-service
    version: v1
spec:
  replicas: 3  # 3 replicas for high availability
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  selector:
    matchLabels:
      app: user-service
  template:
    metadata:
      labels:
        app: user-service
        version: v1
    spec:
      serviceAccountName: user-service
      containers:
      - name: user-service
        image: meditrack/user-service:1.0.0
        imagePullPolicy: IfNotPresent
        ports:
        - name: http
          containerPort: 3001
          protocol: TCP
        
        # Environment variables
        env:
        - name: PORT
          value: "3001"
        - name: DB_HOST
          valueFrom:
            secretKeyRef:
              name: db-credentials
              key: host
        - name: DB_USER
          valueFrom:
            secretKeyRef:
              name: db-credentials
              key: user
        - name: DB_PASSWORD
          valueFrom:
            secretKeyRef:
              name: db-credentials
              key: password
        - name: LOG_LEVEL
          value: "INFO"
        
        # Resource requests and limits
        resources:
          requests:
            memory: "64Mi"
            cpu: "100m"
          limits:
            memory: "256Mi"
            cpu: "500m"
        
        # Health checks
        livenessProbe:
          httpGet:
            path: /health
            port: http
          initialDelaySeconds: 10
          periodSeconds: 10
          timeoutSeconds: 5
          failureThreshold: 3
        
        readinessProbe:
          httpGet:
            path: /ready
            port: http
          initialDelaySeconds: 5
          periodSeconds: 5
          timeoutSeconds: 3
          failureThreshold: 2
        
        # Volume mounts for logs
        volumeMounts:
        - name: logs
          mountPath: /var/log/user-service
      
      volumes:
      - name: logs
        emptyDir: {}
      
      # Pod disruption budget for updates
      terminationGracePeriodSeconds: 30
      affinity:
        podAntiAffinity:
          preferredDuringSchedulingIgnoredDuringExecution:
          - weight: 100
            podAffinityTerm:
              labelSelector:
                matchExpressions:
                - key: app
                  operator: In
                  values:
                  - user-service
              topologyKey: kubernetes.io/hostname
```

### Service Definition for User Service

```yaml
# File: k8s/user-service-service.yaml

apiVersion: v1
kind: Service
metadata:
  name: user-service
  namespace: meditrack
  labels:
    app: user-service
spec:
  type: ClusterIP  # Internal service discovery
  selector:
    app: user-service
  ports:
  - name: http
    port: 3001
    targetPort: http
    protocol: TCP
  sessionAffinity: None
```

### Horizontal Pod Autoscaler

```yaml
# File: k8s/user-service-hpa.yaml

apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: user-service-hpa
  namespace: meditrack
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: user-service
  minReplicas: 2
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
  - type: Resource
    resource:
      name: memory
      target:
        type: Utilization
        averageUtilization: 80
  behavior:
    scaleDown:
      stabilizationWindowSeconds: 300
      policies:
      - type: Percent
        value: 50
        periodSeconds: 60
    scaleUp:
      stabilizationWindowSeconds: 0
      policies:
      - type: Percent
        value: 100
        periodSeconds: 30
      - type: Pods
        value: 2
        periodSeconds: 30
      selectPolicy: Max
```

### ConfigMap for Application Configuration

```yaml
# File: k8s/configmap.yaml

apiVersion: v1
kind: ConfigMap
metadata:
  name: meditrack-config
  namespace: meditrack
data:
  log_level: "INFO"
  api_version: "v1"
  api_timeout: "30"
  max_connections: "100"
  cache_ttl: "3600"
```

### Secret for Sensitive Data

```yaml
# File: k8s/secrets.yaml

apiVersion: v1
kind: Secret
metadata:
  name: db-credentials
  namespace: meditrack
type: Opaque
stringData:
  host: "mysql-cluster.meditrack.svc.cluster.local"
  port: "3306"
  user: "meditrack_user"
  password: "$(kubectl create secret generic db-credentials --from-literal=...)"
  database: "meditrack_user"
---
apiVersion: v1
kind: Secret
metadata:
  name: jwt-secret
  namespace: meditrack
type: Opaque
stringData:
  jwt_secret: "your-secret-key-here"
```

---

## 🔄 CI/CD PIPELINE

### GitHub Actions Workflow

```yaml
# File: .github/workflows/deploy.yml

name: Deploy Microservices

on:
  push:
    branches:
      - main
      - develop
  pull_request:
    branches:
      - main

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Go
      uses: actions/setup-go@v4
      with:
        go-version: '1.23'
    
    - name: Run tests
      run: |
        cd microservices/user-service
        go test -v -cover ./...
    
    - name: Build
      run: |
        cd microservices/user-service
        CGO_ENABLED=1 GOOS=linux go build -o main ./cmd/server

  build-and-push:
    if: github.ref == 'refs/heads/main'
    needs: test
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2
    
    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}
    
    - name: Build and push User Service
      uses: docker/build-push-action@v4
      with:
        context: ./microservices/user-service
        push: true
        tags: |
          meditrack/user-service:${{ github.sha }}
          meditrack/user-service:latest
    
    - name: Build and push other services
      # Repeat for each service

  deploy-to-k8s:
    if: github.ref == 'refs/heads/main'
    needs: build-and-push
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    - name: Set up kubectl
      uses: azure/setup-kubectl@v1
      with:
        version: 'v1.27.0'
    
    - name: Configure kubectl
      run: |
        mkdir -p $HOME/.kube
        echo "${{ secrets.KUBE_CONFIG }}" | base64 -d > $HOME/.kube/config
    
    - name: Deploy User Service
      run: |
        cd k8s
        kubectl set image deployment/user-service \
          user-service=meditrack/user-service:${{ github.sha }} \
          -n meditrack
        kubectl rollout status deployment/user-service -n meditrack
    
    - name: Deploy other services
      # Repeat for each service
```

### GitLab CI Example

```yaml
# File: .gitlab-ci.yml

stages:
  - test
  - build
  - deploy

test:user-service:
  stage: test
  image: golang:1.23
  script:
    - cd microservices/user-service
    - go test -v -cover ./...
  cache:
    paths:
      - .go/pkg/mod

build:user-service:
  stage: build
  image: docker:latest
  services:
    - docker:dind
  script:
    - cd microservices/user-service
    - docker build -t $CI_REGISTRY_IMAGE/user-service:$CI_COMMIT_SHA .
    - docker tag $CI_REGISTRY_IMAGE/user-service:$CI_COMMIT_SHA $CI_REGISTRY_IMAGE/user-service:latest
    - docker push $CI_REGISTRY_IMAGE/user-service:$CI_COMMIT_SHA
    - docker push $CI_REGISTRY_IMAGE/user-service:latest

deploy:k8s:
  stage: deploy
  image: bitnami/kubectl:latest
  script:
    - kubectl set image deployment/user-service user-service=$CI_REGISTRY_IMAGE/user-service:$CI_COMMIT_SHA -n meditrack
    - kubectl rollout status deployment/user-service -n meditrack
  environment:
    name: production
    kubernetes:
      namespace: meditrack
  only:
    - main
```

---

## 🌍 DEPLOYMENT ENVIRONMENTS

### Environment Strategy

```
┌──────────────────────────────────────────────────────────┐
│                    DEVELOPMENT                           │
│  - Single node Kubernetes (microk8s) or Docker Compose   │
│  - SQLite or MySQL single instance                       │
│  - No authentication required internally                 │
│  - Update frequency: Multiple times per day              │
└──────────────────────────────────────────────────────────┘
                            ↓
┌──────────────────────────────────────────────────────────┐
│                    STAGING                               │
│  - Full Kubernetes cluster (3+ nodes)                    │
│  - MySQL replication (1 primary + 2 replicas)           │
│  - All services with 2 replicas each                    │
│  - Security: TLS, firewalls, authentication             │
│  - Update frequency: Daily (from main branch)            │
└──────────────────────────────────────────────────────────┘
                            ↓
┌──────────────────────────────────────────────────────────┐
│                    PRODUCTION                            │
│  - Full Kubernetes cluster (5+ nodes)                    │
│  - MySQL high availability (replication + backup)       │
│  - All services with 3-5 replicas each                  │
│  - Security: TLS, firewalls, IAM, audit logs            │
│  - Update frequency: Scheduled (weekly/monthly)          │
│  - Monitoring: Prometheus, Grafana, ELK                 │
│  - Backup: Daily automated snapshots                    │
└──────────────────────────────────────────────────────────┘
```

### Local Development Setup

```dockerfile
# docker-compose.yml for local development

version: '3.8'

services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: meditrack_user
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
  
  user-service:
    build:
      context: ./microservices/user-service
    ports:
      - "3001:3001"
    environment:
      DB_HOST: mysql
      DB_USER: root
      DB_PASSWORD: root
    depends_on:
      - mysql
  
  appointment-service:
    build:
      context: ./microservices/appointment-service
    ports:
      - "3002:3002"
    environment:
      DB_HOST: mysql
    depends_on:
      - mysql
  
  # ... other services
  
  api-gateway:
    build:
      context: ./microservices/api-gateway
    ports:
      - "3000:3000"
    depends_on:
      - user-service
      - appointment-service

volumes:
  mysql_data:
```

---

## 📊 DEPLOYMENT CHECKLIST

### Pre-Deployment
- [x] All tests passing
- [x] Docker images built
- [x] Docker images scanned for vulnerabilities
- [x] Kubernetes manifests validated
- [x] Database migrations prepared
- [x] Secrets configured
- [x] DNS records updated
- [x] SSL certificates installed

### Deployment Process
- [x] Create backup of current production
- [x] Push new images to registry
- [x] Update Kubernetes manifests
- [x] Apply rolling updates (one service at a time)
- [x] Verify health checks passing
- [x] Monitor logs for errors
- [x] Run smoke tests

### Post-Deployment
- [x] Verify all endpoints responding
- [x] Check metrics and alerts
- [x] Monitor error rates (should be < 0.1%)
- [x] Verify database connectivity
- [x] Check inter-service communication
- [x] Document any issues
- [x] Keep rollback plan ready for 24 hours

---

## 🔄 ROLLING UPDATE STRATEGY

### Rolling Update Process

```
Current State:
  [Pod-1] [Pod-2] [Pod-3]  ← 3 replicas of User Service v1.0.0

Step 1: Start new pod with v1.0.1
  [Pod-1] [Pod-2] [Pod-3] [New-Pod-4]

Step 2: Old pod terminates (grace period: 30s)
  [Pod-2] [Pod-3] [New-Pod-4]

Step 3: Start another new pod
  [Pod-2] [Pod-3] [New-Pod-4] [New-Pod-5]

Step 4: Terminate old pod
  [Pod-3] [New-Pod-4] [New-Pod-5]

Step 5: Start final new pod
  [Pod-3] [New-Pod-4] [New-Pod-5] [New-Pod-6]

Complete:
  [New-Pod-4] [New-Pod-5] [New-Pod-6]  ← 3 replicas of v1.0.1
```

### Kubectl Commands

```bash
# Update image (Kubernetes will do rolling update)
kubectl set image deployment/user-service \
  user-service=meditrack/user-service:1.0.1 \
  -n meditrack

# Monitor rollout status
kubectl rollout status deployment/user-service -n meditrack

# Check rollout history
kubectl rollout history deployment/user-service -n meditrack

# Rollback to previous version
kubectl rollout undo deployment/user-service -n meditrack

# Rollback to specific revision
kubectl rollout undo deployment/user-service \
  --to-revision=2 \
  -n meditrack
```

---

## ⚡ QUICK REFERENCE: DEPLOYMENT COMMANDS

```bash
# 1. Build all services
for service in user appointment medical pharmacy payment analytics; do
  docker build -t meditrack/$service-service:1.0.0 ./microservices/$service-service/
done

# 2. Push to registry
for service in user appointment medical pharmacy payment analytics; do
  docker push meditrack/$service-service:1.0.0
done

# 3. Apply Kubernetes manifests
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml
kubectl apply -f k8s/

# 4. Check deployment status
kubectl get deployments -n meditrack
kubectl get pods -n meditrack
kubectl get services -n meditrack

# 5. View logs
kubectl logs -f deployment/user-service -n meditrack
kubectl logs -f deployment/user-service -n meditrack --tail=100

# 6. Scale service
kubectl scale deployment user-service --replicas=5 -n meditrack

# 7. Update service
kubectl set image deployment/user-service \
  user-service=meditrack/user-service:1.0.1 \
  -n meditrack --record
```

---

## ✅ DEPLOYMENT VERIFICATION

```bash
# 1. Check all pods are running
$ kubectl get pods -n meditrack
NAME                             READY   STATUS    RESTARTS   AGE
user-service-xyz-abc123         1/1     Running   0          2m
appointment-service-xyz-def456  1/1     Running   0          2m
...

# 2. Check services
$ kubectl get services -n meditrack
NAME                  TYPE        CLUSTER-IP      PORT(S)
user-service          ClusterIP   10.0.0.1        3001/TCP
appointment-service   ClusterIP   10.0.0.2        3002/TCP
...

# 3. Test connectivity
$ kubectl run -it --rm debug --image=curlimages/curl -- sh
# Inside pod:
$ curl http://user-service:3001/health
# Expected: {"status":"ok"}

# 4. Check resource usage
$ kubectl top nodes
$ kubectl top pods -n meditrack
```

---

## 🚨 TROUBLESHOOTING DEPLOYMENT

| Issue | Cause | Solution |
|-------|-------|----------|
| Pod stuck in Pending | Not enough resources | Scale cluster or reduce replica count |
| CrashLoopBackOff | Application error | Check logs: `kubectl logs pod-name` |
| ImagePullBackOff | Image not in registry | Verify image name and registry credentials |
| Nodes NotReady | Node failure | Check node status: `kubectl describe node` |
| Service unreachable | DNS issue | Verify service name and namespace |

---

## 📌 CONCLUSION

The deployment strategy provides:
- **Reliability**: Rolling updates with zero downtime
- **Scalability**: Automatic scaling based on metrics
- **Maintainability**: Clear deployment processes
- **Observability**: Health checks and monitoring
- **Security**: Secrets management and network policies

---

**Next Steps**: Proceed to TASK 5 for scalability, performance, and monitoring.

