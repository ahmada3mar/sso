
<p  align="center"><a  href="https://hyperpay.com"  target="_blank">
<img src="https://www.hyperpay.com/wp-content/uploads/2020/04/cropped-011-300x155.png"  width="400"> &nbsp&nbsp&nbsp&nbsp
<img  src="https://www.aciworldwide.com/wp-content/uploads/2021/05/cropped-android-chrome-512x512-1-1.png"  width="200">
</a></p>

# Connect In

Connect in is a laravel package to handle ACI request & response

#### requirements

* composer version >= 2.6
* Laravel version >= 7
* jenssegers/laravel-mongodb ( will auto-download when install the connect-in )
* mongodb server

#### features

* Handle ACI request and response
* Logging request & response into Mongodb
* Validate requests
* Easy modification ( configurable )
* Create transaction migration
* Create merchant migration

### Resources

* [ACI Documentation](https://wordpresshyperpay.docs.oppwa.com/)

* [Laravel 8 Documentation](https://laravel.com/docs/8.x)

* [Laravel Mongodb Documentation](https://github.com/jenssegers/laravel-mongodb)

### Indexes

* [Installation](#installation)

* [Customize end-points](#customize-end-points)

* [Change default success response](#Change-default-success-response)

* [Customize Transactions Table](#customize-transactions-table)

* [Customize Merchants Table](#customize-merchants-table)

* [Customize the Controller](#customize-the-controller)(#javascript-and-css)

## Installation

After create laravel 8 project , modify ***composer.json*** which located on project's root

```json
....
"repositories": [
     {
      "type":  "git",
      "url":  "http://gitlab.hyperpay.com/packages/connect-in.git"
     } 
    ]

```

run the following commands

``` php
composer require hyperpay/connect-in
```

``` php
composer update
```

configure the mongodb connections in the  ***.env*** ( if you on localhost skip this step  )

```
MONGO_DB_HOST=127.0.0.1
MONGO_DB_PORT=27017
MONGO_DB_DATABASE="same as your project name"
MONGO_DB_USERNAME=''
MONGO_DB_PASSWORD=''
```

## Customize end-points

to edit the default end-points , simply you can publish connect-in config file via artisan command

```bash
php artisan vendor:publish --provider=Hyperpay\ConnectIn\ConnectInServiceProvider --tag=config  --force
```

this command will generate a config file named ***connect-in.php*** inside **config** directory

  ```
  -config
   -connect-in.php
  ```

scroll down to **end_points**
and change payment and refund links

## Change default success response

in **connect-in.php** you will find ***default_response***

```php
'default_response'  => [
  'aci_code'  =>  '000.200.000',
  'description'  =>  'Transaction Successful',
]
```

change aci_code and description as you like based on [ACI Result Codes](https://connectin.docs.oppwa.com/reference/resultCodes)

## Customize Transactions Table

to change default transaction table , publish migrations files using

```bash
php artisan vendor:publish --provider=Hyperpay\ConnectIn\ConnectInServiceProvider --tag=migrations  --force
```

this command will  publish migrations files inside **database\migrations**

modify **2022_03_09_121105_create_transactions_table.php**

***default migration :***

```php
Schema::create('transactions',  function (Blueprint  $table) {
 $table->id();
 $table->string('amount');
 $table->string('currency');
 $table->string('authentication_entityId')->index('authentication_entityId');
 $table->foreign('authentication_entityId')->on('merchants')->references('authentication_entityId');
 $table->string('UUID')->index('UUID');
 $table->string('merchantTransactionId');
 $table->longText('notificationUrl');
 $table->longText('shopperResultUrl');
 $table->enum('status'  , [1,2,3,4,5])->default(1);
 $table->timestamps();

});
```

## Customize Merchants Table

  After publish migrations files
  
  modify **2022_03_09_121105_create_transactions_table.php**

***default migration :***

```php
Schema::create('merchants',  function (Blueprint  $table) {
 $table->id();
 $table->string('name');
 $table->string('email');
 $table->string('authentication_entityId')->index();
 $table->string('access_token');
 $table->string('authentication_userId');
 $table->string('authentication_password');
 $table->string('aci_secret');
 $table->integer('created_by');
 $table->timestamps();

});
```

## Customize the Controller

  run

```bash
php artisan vendor:publish --provider=Hyperpay\ConnectIn\ConnectInServiceProvider --tag=controller--force
```

this command will generate a controller named **ConnectInController.php** inside ***app/Http/Controllers***

 ***default controller :***

```php

 public  function  payment(ConnectInRequest  $request)
 {
  $data  =  $request->all();
  extract($data);
  $data['UUID'] =  $customParameters['UUID'];
  Transaction::create($data);
    return  $request->response(ConnectIn::CREATED);
 }

 public  function  refund(Request  $request,  $transaction)
 {
    //
 }

```

#### default controller contains tow methods

```php
 payment(ConnectInRequest  $request)
```

to handle ACI payment request
this method accept  one arguments ConnectInRequest
>***ConnectInRequest***  is a custom request made to handle ACI request validation

 ```php
 refund(Request  $request,  $transaction)
```

to handle ACI refund request
