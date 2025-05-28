Task Management Project Setup
This guide provides step-by-step instructions to set up the Task Management application, including both backend and frontend components.
Prerequisites

Git
Docker and Docker Compose
Node.js and npm
PHP and Composer (for backend)

Installation Steps
1. Clone the Repository
   Clone the project repository to your local machine:
   git clone https://github.com/thoai2nf/task-management

2. Set Up the Backend
   Navigate to the backend directory and set up the environment:

Change to the backend directory:
cd backend


Start the Docker containers:
docker compose up --build -d


Copy the example environment file:
cp .env.example .env


Access the application container:
docker exec -it app bash


Generate the application key:
php artisan key:generate


Generate the JWT secret:
php artisan jwt:secret


Clear the optimization cache:
php artisan optimize:clear


Clear the configuration cache:
php artisan config:clear



3. Set Up the Frontend
   Navigate to the frontend directory and set up the environment:

Change to the frontend directory:
cd frontend


Install dependencies:
npm install


Start the development server:
npm run dev


Access the frontend application at:
http://localhost:5173/



Notes

Ensure all prerequisites are installed before starting the setup process.
If you encounter any issues, verify that Docker is running and all ports are available.

