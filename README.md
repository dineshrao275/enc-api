EncAPI 

FEATURES
========
* User authentication using Laravel Sanctum
* Create, read, update, and delete articles (restricted to the user who created them)
* Request and response encryption using AES-256-CBC and user-specific API key
* Middleware for auto encryption and decryption
* Policies to ensure only article owners can access/modify their data

INSTALLATION
============
1. Clone the repository:
   git clone https://github.com/dineshrao275/enc-api.git

2. Navigate into the project:
   cd enc-api

3. Install dependencies:
   composer install

4. Copy .env file:
   cp .env.example .env

5. Generate application key:
   php artisan key:generate

6. Update your .env file with database credentials.

7. Run migrations:
   php artisan migrate

8. Serve the application:
   php artisan serve



API ENDPOINTS
=============

Authenticated Article Routes:
* GET /api/article - List articles owned by the user
* POST /api/article - Create a new article
* GET /api/article/{id} - View a specific article
* PUT /api/article/{id} - Update a specific article
* DELETE /api/article/{id} - Delete a specific article

Encryption Routes:
* POST /api/encrypt - Encrypt data using the current user's API key
* POST /api/decrypt - Decrypt data using the current user's API key
