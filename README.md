This Laravel extension allows users to make CRUD operation in a lightning speed.
Currently it supports only API crud operations however I am planning to build for web as well.
By using a single command you will be able to generate all CRUD operations for a specified folder.
First you need to build a database migrations and make Models for each your table you want with relations.
Then run the command for generating CRUD and it will generate following files:
- **Controller**
- **Create request**
- **Update request**
- **Resource**

## Requirements
    Laravel >= 10.x
    PHP >= 8.1

## Installation
Install
```
composer require maxkamov48/crud-generator-laravel
```
## Usage
```
php artisan generate:all --folder=v1/SampleFolder
```

Add a route in `api.php`
```
Route::resource('users', UserController::class);
```
## Author

Maxkamov Akmal & Denis Say // [Contact me by email](mailto:maxkamov48@gmail.com)

Hire me [LinkedIn](https://www.linkedin.com/in/akmal-makhkamov-814193134/)
