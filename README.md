<p align="center"><img height="188" width="198" src="https://botman.io/img/botman.png"></p>
<h1 align="center">Jobsity Chatbot</h1>

## About BotMan Studio

This is a project built for a Jobsity challenge.

## Documentation

Framework
- [Laravel 5.7](https://laravel.com/docs/5.7)

The following dependencies was used in this project
- [BotMan 2.0](http://botman.io)
- [Laravel Auditing 8.0](http://www.laravel-auditing.com/docs/8.0/introduction)

## How to Install

1- Change the .env.example file name to .env and configure the DB parameters

2- Execute "composer install" to install all dependencies

3- Execute "php artisan migrate:fresh" to create the tables on the database

4- Execute "php artisan serve" to initiate the Laravel development server

5- Access [localhost:8000](http://127.0.0.1:8000)

## Keywords

- Signin
- Login
- Logout

*Need to be logged in for the following routes*
- Balance
- Deposit
- Withdraw
- Change currency