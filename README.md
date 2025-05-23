# <p align="center"> Interviewing Backend </p>

## introduction
****
The Main Server side to interact with the [Interviewing Client-side App]()\
And Support Fully Admin panel to keep tracking the business needs easily.

## Requirements
 ****

- PHP 8.1
- apache OR Xampp OR Nginx
- mysql 8

## Contribution Guide
Here is a documentation to [contribution guide](https://github.com/sint-tech/interviewing-backend/blob/development/docs/Contribution_guide.md) 
## Installation Guide

****
- Clone the Current Repository
- run ``cp .env.example .env``
- Ensure creating new database in your local mysql
- set your database keys in the .env file

```php
DB_DATABASE=DATABASE_NAME 
DB_USERNAME=DATABASE_USER_NAME 
DB_PASSWORD=DATABASE_PASSWORD
```

- then run the next commands
```php
composer install
```
```php
php artisan key:generate
```
```php
php artisan migrate
```
### and to start the server run

```php  
php artisan serve
```

---
> [!NOTE]
> when run the serve command will register the server to the localhost and the port **8000**.\
> to change it you can pass the **`--port`** option to the command\
> Example:
> ```php
> php artisan serve --port=7070
---
