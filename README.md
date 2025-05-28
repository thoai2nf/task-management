Init setup
1. Git clone https://github.com/thoai2nf/task-management
2. Set up backend
   2.1 cd backend
   2.2 run command: docker compose up --build -d
   2.3 cp .env.example .env
   2.4 docker exec -it app bash
   2.5 php artisan key:generate
   2.6 php artisan jwt:secret
   2.7 php artisan optimize:clear
   2.8 php artisan config:clear

3. Set up frontend
   3.1 cd frontend
   3.2 npm install
   3.3 npm run dev
   access: http://localhost:5173/