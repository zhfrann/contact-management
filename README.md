# Contact Management with Laravel RESTful API

I use this repository to build a simple contact management API consisting of users, contact lists, and address lists. This project is made as a case study exercise for me using laravel in building a simple restful api

## Run Locally

Clone the project

```bash
  git clone https://github.com/zhfrann/laravel-contact-management.git
```

Go to the project directory

```bash
  cd laravel-contact-management
```

Install dependencies

```bash
  composer install
```

Copy and set the .env

```bash
  cp .env.example .env
```

Generate Application Key

```bash
  php artisan key:generate
```

Run the migration

```bash
  php artisan migrate
```

Start the server

```bash
  php artisan serve
```

## API Endpoint List

### Auth

You can try these endpoints using test.http

| Method | Endpoint           | Description       |
| ------ | ------------------ | ----------------- |
| POST   | /api/users         | Register new user |
| POST   | /api/users/login   | Login user        |
| GET    | /api/users/current | Get current user  |
| PATCH  | /api/users/current | Update user       |
| DELETE | /api/users/logout  | Logout user       |

### 👤 Contact Management

| Method | Endpoint               | Description              |
| ------ | ---------------------- | ------------------------ |
| POST   | /api/contacts          | Create contact           |
| GET    | /api/contacts          | Search contacts          |
| GET    | /api/contacts/{id}     | Get contact detail by ID |
| PUT    | /api/contacts/{id}     | Update contact detail    |
| DELETE | /api/contacts/{id}     | Delete a contact         |
| GET    | /api/contacts?name=... | Search contact by name   |
| GET    | /api/contacts?size=2   | Paginated contact list   |

### Address Management

| Method | Endpoint                                  | Description                       |
| ------ | ----------------------------------------- | --------------------------------- |
| POST   | /api/contacts/{id}/addresses              | Create address                    |
| GET    | /api/contacts/{id}/addresses              | get addresses list from a contact |
| GET    | /api/contacts/{id}/addresses/{address_id} | Get address detail                |
| PUT    | /api/contacts/{id}/addresses/{address_id} | Update address detail             |
| DELETE | /api/contacts/{id}/addresses/{address_id} | Delete an address                 |

> All endpoints (except register & login) require the header:
> `Authorization: {token}` that created after you login

## Authors

-   [@zhfrann](https://www.github.com/zhfrann)
