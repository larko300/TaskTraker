## Installation

git clone https://github.com/larko300/TaskTraker.git

composer install

cp .env.example .env

php artisan key:generate

add database information to allow Laravel to connect to the database

php artisan migrate

php artisan passport:install
