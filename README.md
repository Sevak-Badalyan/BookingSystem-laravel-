# Room Booking API

REST API для бронирования офисных переговорных комнат с разделением прав доступа.

## Требования

- PHP 8.2+
- Composer
- PostgreSQL

## Установка

```bash
# Клонировать репозиторий
git clone <repository-url>
cd Laravel\(TestTask\)

# Установить зависимости
composer install

# Скопировать .env
cp .env.example .env

# Настроить базу данных в .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=booking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Сгенерировать ключ
php artisan key:generate

# Запустить миграции
php artisan migrate

# Запустить сервер
php artisan serve
```

## API Endpoints

### Аутентификация

| Метод | URL | Описание | Auth |
|-------|-----|----------|------|
| POST | `/api/register` | Регистрация | - |
| POST | `/api/login` | Вход | - |
| POST | `/api/logout` | Выход | Token |
| GET | `/api/user` | Текущий пользователь | Token |

### Комнаты (Rooms)

| Метод | URL | Описание | Auth |
|-------|-----|----------|------|
| GET | `/api/rooms` | Список комнат | Token |
| GET | `/api/rooms/{id}` | Одна комната | Token |
| POST | `/api/rooms` | Создать комнату | Admin |
| PUT | `/api/rooms/{id}` | Обновить комнату | Admin |
| DELETE | `/api/rooms/{id}` | Удалить комнату | Admin |

### Бронирование (Bookings)

| Метод | URL | Описание | Auth |
|-------|-----|----------|------|
| POST | `/api/bookings` | Создать бронь | Token |
| DELETE | `/api/bookings/{id}` | Отменить бронь | Owner/Admin |

## Примеры запросов (Postman)

### 1. Регистрация

```
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"  
}
```

### 2. Логин

```
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password123"
}
```

Response:
```json
{
    "message": "Login successful",
    "token": "1|abc123..."
}
```

### 3. Создать комнату (Admin)

```
POST http://localhost:8000/api/rooms
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Conference Room A",
    "capacity": 10
}
```

### 4. Создать бронь

```
POST http://localhost:8000/api/bookings
Authorization: Bearer {token}
Content-Type: application/json

{
    "room_id": 1,
    "start_at": "2026-02-24 10:00:00",
    "end_at": "2026-02-24 11:00:00"
}
```

### 5. Отменить бронь

```
DELETE http://localhost:8000/api/bookings/1
Authorization: Bearer {token}
```

## Валидация бронирования

- Минимальная длительность: 30 минут
- Максимальная длительность: 8 часов
- Только будущее время
- Проверка пересечений в одной комнате (с DB Transaction)

## Роли

- `admin` — полный доступ к CRUD комнат, может удалять любые брони
- `employee` — может просматривать комнаты, создавать брони, удалять только свои брони

## Структура проекта

```
app/
├── Enums/
│   └── UserRole.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── BookingController.php
│   │   └── RoomController.php
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php
│   └── Requests/
│       ├── Auth/
│       ├── Booking/
│       └── Room/
├── Models/
│   ├── Booking.php
│   ├── Room.php
│   └── User.php
├── Policies/
│   └── BookingPolicy.php
└── Services/
    ├── AuthService.php
    ├── BookingService.php
    └── RoomService.php
```

## Технологии

- Laravel 12
- PHP 8.5
- PostgreSQL
- Laravel Sanctum (API tokens)
