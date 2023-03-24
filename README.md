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
## Installation Guide

****
- Clone the Current Repository
- run ``cp .env.example .env``
- Ensure creating new database in your local mysql
- set your database keys in the .env file
  - DB_DATABASE=DATABASE_NAME 
  - DB_USERNAME=DATABASE_USER_NAME 
  - DB_PASSWORD=DATABASE_PASSWORD
- run ``composer i``
- run ``php artisan key:generate``
- run ``php artisan migrate``
- and to start the server run
  - ``php artisan serve``
> #### __*note*__
> when run the serve command will register the server to the localhost and the port **8000**.\
> to change it you can pass the **`--port`** option to the command\
> Example:\
> ``php artisan serve --port=7070``

