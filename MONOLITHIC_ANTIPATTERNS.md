# Monolithic Anti-Patterns - Detailed Analysis

**Code Examples from Representasi Monolit java/**

---

## 🔴 Anti-Pattern #1: God Database

### Location: `database/DatabaseConnection.java`

```java
public class DatabaseConnection {
    private static Connection connection;
    
    // Singleton - one instance for entire application
    public static synchronized Connection getInstance() {
        if (connection == null) {
            // Connection pool shared by ALL services
            connection = DriverManager.getConnection(
                "jdbc:mysql://localhost:3306/meditrack_monolith",
                "root", "password"
            );
        }
        return connection;
    }
}
```

### Problem: SINGLE POINT OF FAILURE

```
Database connection lost
        ↓
Connection.close()
        ↓
All Services Stop Working
        ↓
UserService: ✗ Cannot read users
AppointmentService: ✗ Cannot read/write appointments
PaymentService: ✗ Cannot process payments
PharmacyService: ✗ Cannot manage inventory
        ↓
ENTIRE APPLICATION DOWN
```

### What Happens

| Scenario | Monolithic | Microservices |
|----------|-----------|--------------|
| MySQL down | App down (0% uptime) | User service down, others continue |
| Network partition | App down | Services fallback to circuit breaker |
| Connection pool exhausted | App down | Service degraded, others normal |
| Schema migration | Entire app must restart | One service restarts |

### Why It's Bad

1. **Cascading Failures**
   ```
   Payment DB issue 
   → Shared connection pool blocks
   → User queries timeout
   → Appointment queries timeout
   → Analytics queries timeout
   → API Gateway timeout
   → Client error
   ```

2. **No Isolation**
   ```
   // In microservices:
   If payment-service DB down → user-service still works
   
   // In monolithic:
   If ANY table down → ALL services affected
   ```

3. **Backup Complexity**
   ```
   Cannot backup users independently
   Cannot backup payments separately
   Must backup entire database together
   ```

---

## 🔴 Anti-Pattern #2: Service Locator (Direct Dependencies)

### Location: `services/AppointmentService.java`

```java
public class AppointmentService {
    private UserService userService;           // Direct reference
    private PaymentService paymentService;     // Direct reference
    
    public AppointmentService() {
        // Hard-coded dependency
        this.userService = UserService.getInstance();
        this.paymentService = PaymentService.getInstance();
    }
    
    public boolean createAppointment(Appointment appointment) {
        // DIRECT METHOD CALL - tight coupling
        if (userService.getUserById(appointment.getPatientId()) == null) {
            return false;
        }
        
        // DIRECT METHOD CALL - tight coupling
        if (!paymentService.isUserPaymentUpToDate(appointment.getPatientId())) {
            return false;
        }
        
        // ... continue
    }
}
```

### Problem: IMPOSSIBLE TO DECOUPLE

```
AppointmentService depends on:
├─ UserService
│  └─ User model
│     └─ database/DatabaseConnection
│
├─ PaymentService
│  └─ Payment model
│     └─ database/DatabaseConnection
│
└─ Appointment model
   └─ database/DatabaseConnection
```

### Testing Hell

```java
// To test AppointmentService.createAppointment:
@Test
public void testCreateAppointment() {
    // But we must setup:
    // 1. UserService + its database
    // 2. PaymentService + its database
    // 3. DatabaseConnection + actual MySQL
    
    // Even to test one method, need entire system
    AppointmentService service = new AppointmentService();
    // service.userService is already initialized with REAL database
    
    // No way to mock UserService
    // No way to stub PaymentService
    // CANNOT test in isolation
}
```

### Why It's Bad

1. **Can't Test in Isolation**
   ```
   Unit test AppointmentService
   → Requires UserService
   → Requires PaymentService
   → Requires MySQL
   → Not a unit test anymore (integration test)
   → Takes 5 seconds instead of 50ms
   ```

2. **Can't Mock Dependencies**
   ```java
   // WANT TO DO:
   @Test
   public void testAppointmentWithInactiveUser() {
       UserService mockUserService = mock(UserService.class);
       when(mockUserService.getUserById(1)).thenReturn(null);
       service.createAppointment(apt);
   }
   
   // CANNOT DO:
   // userService is getInstance() - singleton
   // Cannot inject mock
   // Cannot change behavior
   ```

3. **Deployment Coupling**
   ```
   To deploy AppointmentService:
   - Must have UserService running
   - Must have PaymentService running
   - Cannot deploy AppointmentService alone
   ```

---

## 🔴 Anti-Pattern #3: Cascading Updates

### Location: `services/UserService.java`

```java
public void deactivateUserAndCancelAppointments(int userId) {
    System.out.println("🚨 Deactivating user and cascading to other services");
    
    // Step 1: Update user
    User user = getUserById(userId);
    user.setStatus("inactive");
    updateUser(user);  // Persist to database
    
    // Step 2: DIRECT CALL to AppointmentService
    appointmentService.cancelUserAppointments(userId);  // CASCADING
    
    // Step 3: DIRECT CALL to PaymentService
    paymentService.cancelUserPayments(userId);           // CASCADING
}
```

### Problem: COMPLEX MULTI-SERVICE TRANSACTION

```
userController.deactivateUser(1)
    ↓
userService.deactivateUserAndCancelAppointments(1)
    ├─ UPDATE users SET status='inactive' WHERE id=1
    │   ✓ Succeeds
    │
    ├─ appointmentService.cancelUserAppointments(1)
    │   └─ UPDATE appointments SET status='cancelled' WHERE user_id=1
    │       ✓ Succeeds
    │
    └─ paymentService.cancelUserPayments(1)
        └─ UPDATE payments SET status='cancelled' WHERE user_id=1 AND status != 'completed'
            ✗ DATABASE ERROR
            
RESULT: PARTIAL FAILURE
├─ User status: inactive ✓
├─ Appointments: cancelled ✓
├─ Payments: NOT cancelled ✗
└─ INCONSISTENT STATE
```

### Why It's Bad

1. **No Atomic Transactions**
   ```sql
   -- WANT:
   BEGIN TRANSACTION
       UPDATE users SET status='inactive' WHERE id=1;
       UPDATE appointments SET status='cancelled' WHERE user_id=1;
       UPDATE payments SET status='cancelled' WHERE user_id=1;
   COMMIT;
   
   -- REALITY (Monolithic):
   UPDATE users ...;
   -- Now we're in another service
   UPDATE appointments ...;
   -- What if this fails?
   UPDATE payments ...;
   -- Too late to rollback user
   ```

2. **Hard to Rollback**
   ```java
   try {
       appointmentService.cancelAppointments(userId);
       paymentService.cancelPayments(userId);
   } catch (Exception e) {
       // How to rollback user deactivation?
       // userService already updated database
       // No transaction to rollback
   }
   ```

3. **Risk of Partial Failure**
   ```
   User sees himself deactivated
   But his appointments still active
   And his payments still pending
   
   Support calls: "Where did my appointments go?"
   ```

---

## 🔴 Anti-Pattern #4: Shared Models Coupling

### Location: `models/User.java`

```java
public class User {
    private int id;
    private String name;
    private String email;
    private String phone;
    private String role;
    private String status;
    private String password;
    private LocalDateTime createdAt;
    private LocalDateTime updatedAt;
    
    // Used by UserService
    // Used by AppointmentService
    // Used by MedicalService
    // Used by PharmacyService
    // Used by PaymentService
}
```

### Problem: GOD OBJECT

```
User.class changes
├─ UserService affected
├─ AppointmentService affected
├─ MedicalService affected
├─ PharmacyService affected
├─ PaymentService affected
└─ AnalyticsService affected

All 6 services must recompile
All 6 services must redeploy
```

### Scenario: Add Field

```java
// New requirement: Add user.verified_email field
public class User {
    // ... existing fields
    private boolean verifiedEmail;  // NEW
}

// Now must update:
// 1. database/DatabaseConnection - add column
// 2. UserService - handle migration
// 3. AppointmentService - recompile
// 4. MedicalService - recompile
// 5. PharmacyService - recompile
// 6. PaymentService - recompile
// 7. AnalyticsService - recompile
// 8. ALL tests - recompile

// Must deploy ALL services together
// Risk of regression in unrelated services
// Takes 30 minutes to deploy one field
```

### Why It's Bad

1. **Can't Evolve Independently**
   ```
   UserService wants User.verified_email
   AppointmentService doesn't care
   But must recompile AppointmentService anyway
   ```

2. **Model Bloat**
   ```java
   public class User {
       // UserService fields
       private int id;
       private String name;
       private String email;
       
       // AppointmentService fields
       private String role;
       
       // PharmacyService fields
       private LocalDateTime lastPharmacyVisit;
       
       // PaymentService fields
       private double totalDebts;
       
       // AnalyticsService fields
       private LocalDateTime lastLogin;
       
       // Class becomes God Object with 50+ fields
   }
   ```

3. **N+1 Compilation Problem**
   ```
   One change → all services recompile
   10 services changed → all services need redeployment
   Risk increases exponentially
   ```

---

## 🔴 Anti-Pattern #5: Shared Database Schema

### Location: `services/AnalyticsService.java`

```java
public Map<String, Object> getDashboardMetrics() {
    try {
        // ANALYTICS reads from ALL tables
        String userSql = "SELECT COUNT(*) as count FROM users";
        String appointmentSql = "SELECT COUNT(*) FROM appointments WHERE status != 'cancelled'";
        String paymentSql = "SELECT SUM(amount) FROM payments WHERE status = 'completed'";
        String drugSql = "SELECT COUNT(*) FROM drug_inventory WHERE stock < 10";
        
        // Analytics knows about:
        // - users table (UserService)
        // - appointments table (AppointmentService)
        // - payments table (PaymentService)
        // - drug_inventory table (PharmacyService)
    }
}
```

### Problem: SCHEMA COUPLING

```
PharmacyService decides to rename:
    drug_inventory → inventory

Result:
├─ PharmacyService updates table name
├─ PharmacyService queries break
├─ AnalyticsService queries break ✗
├─ ANALYTICS CRASHES
└─ Dashboard unavailable
```

### Schema Change Cascade

```
If UserService changes user.email → user_email:
├─ UserService updates
├─ AnalyticsService must know about it
├─ MedicalService might use it
├─ PaymentService might use it
└─ All must coordinate migration
```

### Why It's Bad

1. **Analytics is Tight Coupled**
   ```
   AnalyticsService doesn't "own" users/appointments/payments
   But has hard dependency on their schema
   Schema changes in one place break analytics
   ```

2. **Can't Migrate Data Independently**
   ```
   Want to migrate PaymentService to PostgreSQL
   But AnalyticsService expects MySQL table
   Cannot do independently
   ```

3. **Complex Query Optimization**
   ```sql
   -- Analytics needs data from multiple tables
   SELECT 
       COUNT(*) as users,
       SUM(appointments) as appts,
       SUM(payments) as revenue,
       COUNT(low_stock) as inventory_issues
   FROM users, appointments, payments, drug_inventory
   WHERE ...
   
   -- Complex joins
   -- Hard to optimize
   -- Single slow query affects dashboard
   ```

---

## 🔴 Anti-Pattern #6: Single Database Connection Pool

### Location: Multiple services use same `DatabaseConnection`

```java
// UserService
public class UserService {
    private DatabaseConnection db = DatabaseConnection.getInstance();
}

// PaymentService
public class PaymentService {
    private DatabaseConnection db = DatabaseConnection.getInstance();
}

// PharmacyService
public class PharmacyService {
    private DatabaseConnection db = DatabaseConnection.getInstance();
}

// ALL use SAME connection pool
```

### Problem: RESOURCE CONTENTION

```
Connection Pool: 10 connections

Scenario:
├─ UserService: 5 connections (slow query)
├─ PaymentService: 3 connections
├─ PharmacyService: 2 connections (trying to connect)
│   └─ WAIT (pool exhausted)
├─ AnalyticsService: (trying to connect)
│   └─ WAIT (pool exhausted)
└─ API Gateway: (trying to connect)
    └─ WAIT (pool exhausted)

Result:
All requests timeout
Cascading failures
```

### Why It's Bad

1. **One Slow Service Blocks Others**
   ```
   Analytics running complex dashboard query (30 seconds)
   ├─ Uses 3 connections
   └─ Blocks all other services from connecting
   ```

2. **Connection Pool Tuning**
   ```
   Pool size = ?
   
   Too small: Timeout errors
   Too large: Memory consumption
   
   Cannot tune per service
   Must tune for entire application
   One service's bad behavior affects all
   ```

3. **No Resource Isolation**
   ```
   // In microservices:
   user-service pool: 10 connections
   pharmacy-service pool: 5 connections
   payment-service pool: 10 connections
   
   // In monolithic:
   Total pool: 10 connections (shared by all)
   ```

---

## 🔴 Anti-Pattern #7: Horizontal Scaling Impossible

### Location: Entire `MediTrackMonolithApp`

```java
public static void main(String[] args) {
    // One application instance
    MediTrackMonolithApp app = new MediTrackMonolithApp();
}
```

### Problem: VERTICAL SCALING ONLY

```
Request Volume: 1000 req/sec

Monolithic Approach:
├─ Option 1: Add CPU to server
├─ Option 2: Add RAM to server
└─ Option 3: Add more server CPUs

But all requests hit same application
All request threads use same database
Cannot separate traffic by service
```

### Scaling Scenario

```
Dashboard queries (10% traffic) → slow
├─ Analytics service uses 30% CPU
├─ Blocks appointment service queries
└─ All users slow down

Solution with monolithic:
├─ Add CPU to entire server
├─ Must pay for resources you don't need
├─ Appointment service doesn't need scaling

Solution with microservices:
├─ Scale analytics-service 3x
├─ Appointment service unchanged
└─ Pay only for what you need
```

### Why It's Bad

1. **Cost Inefficiency**
   ```
   Pharmacy orders spike (100x normal)
   ├─ Need to scale pharmacy 100x
   ├─ But must scale entire app
   ├─ Including user service (doesn't need it)
   └─ Cost multiplies
   ```

2. **Deployment Risk**
   ```
   To scale: must deploy entire application
   ├─ Risk of regression
   ├─ All code tested together
   └─ Cannot add instance without full test
   ```

3. **Uneven Resource Usage**
   ```
   CPU: 10%  (mostly idle)
   Memory: 80% (service A needs memory)
   Network: 50% (service B uses bandwidth)
   Storage: 30% (service C uses disk)
   
   All bottlenecks hit one server
   Cannot optimize each resource independently
   ```

---

## 🎯 Summary of Anti-Patterns

| Anti-Pattern | Problem | Impact | Severity |
|-------------|---------|--------|----------|
| God Database | Single shared DB | Cascading failures | 🔴 CRITICAL |
| Service Locator | Direct dependencies | Impossible testing | 🔴 CRITICAL |
| Cascading Updates | Multi-service transactions | Data inconsistency | 🔴 CRITICAL |
| Shared Models | Model bloat | Complex evolution | 🟠 HIGH |
| Schema Coupling | Shared table structure | Breaking changes | 🟠 HIGH |
| Connection Pool | Resource contention | Performance degradation | 🟠 HIGH |
| Vertical Scaling | Cannot scale per service | Cost inefficiency | 🟠 HIGH |

---

## ✅ Solutions (Microservices)

### Problem → Solution

| Monolithic Problem | Microservices Solution |
|------------------|----------------------|
| God Database | Separate DB per service |
| Service Locator | HTTP/gRPC async calls |
| Cascading Updates | Eventual consistency + events |
| Shared Models | Domain models per service |
| Schema Coupling | Independent schema evolution |
| Connection Pool | Isolated connection pools |
| Vertical Scaling | Horizontal scaling per service |

---

## 📚 Learning Outcomes

After studying these anti-patterns, you understand:

1. ✓ Why shared databases are problematic
2. ✓ Why direct method calls create coupling
3. ✓ Why monolithic scaling is expensive
4. ✓ Why microservices require independent databases
5. ✓ Why HTTP communication enables loose coupling
6. ✓ Why domain-driven design matters

---

**Next Steps:**
- Study `microserv/microservices/` for solutions
- See how Go services address each anti-pattern
- Understand trade-offs of each approach

