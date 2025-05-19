# ***<p style="text-align:center;">Presupuestote</p>***
***
## About
This project is an API for manage budgets.

**It is still in development**

## Overview
- Manage clients, employees and suppliers.
- Clients can have multiple budgets with different states.
- Budgets can have multiple works.
- Works can have multiple materials.
- You can manage materials and suppliers with invoices.
- Suppliers have invoices.
- Inside an invoice you register the price and quantity of each material.
- Material can be sort by subcategories and categories.
- You can manage your employees and salaries.
- You can add payments to control your incomes and expenses.
- A payment can be added to a budget, invoice or salary.

##  Main Features
- Can calculate budget cost and profit automatically.
- Can calculate works cost by the price of each material.
- Materials can be sort by subcategories and categories.
- Can manage a history of stock and price of each material.
- Clients, employees and suppliers have a payment history.
- Each you can easily know if you are making profit or loss.
- Can calculate the total of an invoice.
- Can easily manage your employees and its salaries.

## Installation
### Must have Mysql, PHP 8, Composer 2 and Laravel installed.
1) Clone the repository
2) Run `composer install` to install the dependencies.
3) Run `npm install` to install the frontend dependencies.
4) Run `php artisan key:generate` to generate the application key.
5) Create a `.env` file and set the database connection parameters.
6) Create the database, and run `php artisan migrate` to create the tables.
7) If you want can run `php artisan db:seed` to seed the database with some test data.
8) Run `php artisan serve` to start the server.
9) Use an http client like Postman to test the API.

[Original project](https://github.com/Valuncho/PresupuestitoBack) 
