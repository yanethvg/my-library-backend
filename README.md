# My Library Backend

  
## Description
    Create a small university library system where students can check out physical books with the following functionality.
    There would be 2 roles: Student and Librarian.

    The flow of the app should be this:
    The student can see the list of all the books that exist in the library. He can filter or search by title, author or genre. Once a book is selected to see its details, you can see if there are available copies of this book (in stock). If so, the student can request the check out for this book and the stock for that book will decrease.

    The student can see all the books he has requested for check out. Once the student has used the book, he will return it to the library. For this, the librarian can look for people that has check out books and then he can mark that record/book as returned, that way the stock of that
    book will increase.

    The Librarian is in charge of adding new users with the following information: First name, Last name, email and role.
    Also, the Librarian can add new books with the following information: Title, Author, Published Year and Genre.
  

## Installation

  

In order to run the project we will need the following software:

|  Software |  Version   |  Description                       |
|-----------|------------|----------------------------------- |
| Docker    | >=20.10    | Required to run docker containers  |
| PHP       |  ^8.0      | Required to install composer       |
| Composer  |  ^2.0      | Required to install dependencies and install sails |


  Note: once we have sails instailed this will mount all needed for PostgresSQL (dev database and testing database) and PHP
  

## Usage

### Development  

To install project backend API we should follow the steps once we have install prerequesites and we are on the root folder of the project.

  
```bash
composer install
cp .env.example .env
```
  
  Then you can fill  enviroment variables attributes, required ones for these project are `DB_DATABASE`,  `DB_USERNAME`,  `DB_PASSWORD`  on .env file, you can fill these enviroment variables with your custom ones, docker wil generate the containers using the variables you choose

Then we will run:
```bash
./vendor/bin/sail up
```
  Once the containers are running we can generate the application key 
```bash
./vendor/bin/sail artisan key:generate
```  
  Now we can run `./vendor/bin/sail  artisan migrate:refresh` to create database tables and then we can also run `/vendor/bin/sail artisan db:seed` to populate with seeders the tables already created

### Run tests
To run test we only need to run `./vendor/bin/sail artisan test` 
If you need to get a coverage report you can run the following  command:

 `sail php artisan test --coverage-html <path>` 

 where path is where you want to store the html coverage report

## Documentation

Click the next link to see postman API docs: [API BACKEND POSTMAN](https://documenter.getpostman.com/view/7984452/2s83tDosQj)
