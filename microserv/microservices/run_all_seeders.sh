#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}🌱 MediTrack Database Seeding - Starting All Services${NC}\n"

# Define service paths
SERVICES=(
    "user-service"
    "appointment-service"
    "pharmacy-service"
    "medical-service"
    "payment-service"
    "analytics-service"
)

# Counter
SUCCESS=0
FAILED=0

# Function to run seeder for each service
run_seeder() {
    local service=$1
    local service_path="microservices/$service"
    
    echo -e "${YELLOW}[*] Starting seeding for $service...${NC}"
    
    if [ ! -d "$service_path" ]; then
        echo -e "${RED}[✗] Error: $service_path not found${NC}\n"
        ((FAILED++))
        return 1
    fi
    
    cd "$service_path" || return 1
    
    # Build seeder binary
    if [ ! -d "cmd/seeder" ]; then
        echo -e "${RED}[✗] Error: cmd/seeder directory not found for $service${NC}\n"
        cd - > /dev/null
        ((FAILED++))
        return 1
    fi
    
    echo -e "${YELLOW}  [>] Building seeder...${NC}"
    go build -o seeder_bin cmd/seeder/main.go
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}[✗] Build failed for $service${NC}\n"
        cd - > /dev/null
        ((FAILED++))
        return 1
    fi
    
    # Run seeder
    echo -e "${YELLOW}  [>] Running seeder...${NC}"
    ./seeder_bin
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}[✓] $service seeding completed successfully${NC}\n"
        ((SUCCESS++))
        rm -f seeder_bin
        cd - > /dev/null
        return 0
    else
        echo -e "${RED}[✗] $service seeding failed${NC}\n"
        ((FAILED++))
        rm -f seeder_bin
        cd - > /dev/null
        return 1
    fi
}

# Run all seeders
for service in "${SERVICES[@]}"; do
    run_seeder "$service"
done

# Summary
echo -e "${YELLOW}===========================================${NC}"
echo -e "${GREEN}✓ Successful: $SUCCESS/${#SERVICES[@]}${NC}"
echo -e "${RED}✗ Failed: $FAILED/${#SERVICES[@]}${NC}"
echo -e "${YELLOW}===========================================${NC}"

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ All services seeded successfully!${NC}"
    exit 0
else
    echo -e "${RED}❌ Some services failed to seed${NC}"
    exit 1
fi
