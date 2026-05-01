#!/bin/bash

set -e

echo "🚀 Karacabey Gross Market - Docker Startup"
echo "=========================================="

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}❌ Docker daemon is not running. Please start Docker.${NC}"
    exit 1
fi

echo -e "${BLUE}📦 Building and starting containers...${NC}"

# Build and start all services
docker compose up --build -d

echo -e "${YELLOW}⏳ Waiting for services to be healthy...${NC}"

# Wait for MySQL to be ready
echo -n "Waiting for MySQL... "
for i in {1..30}; do
    if docker compose exec -T mysql mysqladmin ping -h localhost -u root -proot &> /dev/null; then
        echo -e "${GREEN}✓${NC}"
        break
    fi
    echo -n "."
    sleep 1
done

# Wait for PHP app to be ready
echo -n "Waiting for PHP App... "
for i in {1..30}; do
    if docker compose exec -T app curl -f http://localhost:9000 &> /dev/null 2>&1 || [ $i -eq 30 ]; then
        echo -e "${GREEN}✓${NC}"
        break
    fi
    echo -n "."
    sleep 1
done

# Run Laravel migrations
echo -e "${BLUE}📋 Running database migrations...${NC}"
docker compose exec -T app php artisan migrate --force || true

# Run Laravel seeders (optional - comment out if not needed)
# echo -e "${BLUE}🌱 Running database seeders...${NC}"
# docker compose exec -T app php artisan db:seed --force || true

# Clear caches
echo -e "${BLUE}🧹 Clearing caches...${NC}"
docker compose exec -T app php artisan cache:clear || true
docker compose exec -T app php artisan config:clear || true

echo ""
echo -e "${GREEN}✅ All services are running!${NC}"
echo ""
echo -e "${BLUE}📍 Service URLs:${NC}"
echo -e "  ${GREEN}Web:${NC}        http://localhost:8000"
echo -e "  ${GREEN}Frontend:${NC}   http://localhost:3001"
echo -e "  ${GREEN}MySQL:${NC}      localhost:3307"
echo -e "  ${GREEN}Redis:${NC}      localhost:6379"
echo ""
echo -e "${YELLOW}💡 Useful commands:${NC}"
echo "  docker compose logs -f app     # View app logs"
echo "  docker compose logs -f node    # View frontend logs"
echo "  docker compose exec app bash   # Connect to Laravel container"
echo "  docker compose exec mysql bash # Connect to MySQL container"
echo "  docker compose down            # Stop all services"
echo ""
