FROM node:22-alpine

WORKDIR /app

# The package.json and other files will be mounted via docker-compose
# but we can optionally copy them here if we want a standalone image
COPY package*.json ./

# Install dependencies if package.json exists
RUN if [ -f "package.json" ]; then npm install; fi

# We rely on the docker-compose command to run npm run dev
CMD ["npm", "run", "dev"]
