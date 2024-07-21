# User service

### How to run docker compose
1. Update your .env file to match your Docker setup, especially for the database connection:
```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```
2. Build and Run the Docker Containers
```
docker-compose up --build
```

3. Run Laravel Migrations
```
docker-compose exec app php artisan migrate
```

4. Access the Application
Open your browser and navigate to http://localhost to see your Laravel application running in Docker.

### SQL Dump backup
```
/storage/backup/_backupdb_20240721_131755.sql
```

### Run testing
```
docker-compose exec app php artisan test
```
