# Rozkład Jazdy - Bus/Train Schedule Application

## Running the Application

### Database Setup


1. Start the PostgreSQL database using Docker:

```bash
cd rozklad_jazdy-stack
docker-compose up -d
```

2. Install dependencies:

```bash
cd ../rozklad_jazdy
composer install
```

3. Run migrations to create database tables:

```bash
php artisan migrate
```

4. Seed the database with sample data:

```bash
php artisan db:seed
```

### Database Credentials

#### Database Admin User:
- **Username:** rozklad_admin
- **Password:** IU0Bg0yMkaiBqXiz

#### Application Database User (used by the Laravel application):
- **Username:** rozklad_user
- **Password:** IEALdiauSyFWXzPN
- **Database:** rozklad_jazdy

### Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at [http://localhost:8000](http://localhost:8000).

### Application Login Credentials

#### Admin User:
- **Username:** admin
- **Email:** admin@example.com
- **Password:** admin123

### Regular User:
- **Username:** jkowalski
- **Email:** jan.kowalski@example.com
- **Password:** password123
