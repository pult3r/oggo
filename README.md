
## How to install 

## 1. Get repository from: https://github.com/pult3r/oggo :
<br/>
$ gh repo clone pult3r/oggo<br/>
or<br/>
$ git clone https://github.com/pult3r/oggo.git<br/>

## 2. Go to project directory :<br/>
$ cd oggo

## 3. Install composer :<br/>
$ composer install

## 4. Create .env file by :<br/>
$ cp .env.example .env

## 5. Open .env file and set you database acceess settings :<br/>
DB_DATABASE=Oggo<br/>
DB_USERNAME=your_mysql_login<br/>
DB_PASSWORD=your_mysql_password<br/>

# For ensure correct environment settings, please check .env variables : 

APP_URL=<br/>
FRONTEND_URL=<br/>
SESSION_DOMAIN=<br/>
APP_DOMAIN=<br/>
<br/><br/>
!! Important <br/>
For polish, change <br/>
APP_LOCALE=gb <br/>
to <br/>
APP_LOCALE=pl<br/>

## 6. open mysql console and create Movies database : <br/>
CREATE DATABASE Oggo DEFAULT CHARACTER SET = 'utf8mb4';

## 7. Add aplication key :<br/>
$ php artisan key:generate

## 8. Make database migration :<br/>
$ php artisan migrate

## 9. Create filament user
php artisan make:filament-user

## 10. If you want add 'test' data use :<br/>
$ php artisan db:seed --class=ProjectSeeder

## Important : <br/>
If you have Laravel problem 'Permission denied' - set corect permissions!




