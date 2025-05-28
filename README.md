# Task Management Project Setup

This guide provides step-by-step instructions to set up the Task Management application, including both backend and frontend components.

## Prerequisites
- Git
- Docker and Docker Compose
- Node.js and npm
- PHP and Composer (for backend)

## Installation Steps

### 1. Clone the Repository
Clone the project repository to your local machine:
```bash
git clone https://github.com/thoai2nf/task-management
```

### 2. Set Up the Backend
Navigate to the backend directory and set up the environment:

1. Change to the backend directory:
   ```bash
   cd backend
   ```

2. Start the Docker containers:
   ```bash
   docker compose up --build -d
   ```

3. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

4. Access the application container:
   ```bash
   docker exec -it app bash
   ```

5. Generate the application key:
   ```bash
   php artisan key:generate
   ```

6. Generate the JWT secret:
   ```bash
   php artisan jwt:secret
   ```

7. Clear the optimization cache:
   ```bash
   php artisan optimize:clear
   ```

8. Clear the configuration cache:
   ```bash
   php artisan config:clear
   ```

### 3. Set Up the Frontend
Navigate to the frontend directory and set up the environment:

1. Change to the frontend directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Start the development server:
   ```bash
   npm run dev
   ```

4. Access the frontend application at:
   ```
   http://localhost:5173/
   ```

## Notes
- Ensure all prerequisites are installed before starting the setup process.
- If you encounter any issues, verify that Docker is running and all ports are available.