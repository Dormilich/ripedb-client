# RipeDB-Client

A PHP library to communicate with the RIPE NCC database.

## Requirements

The RipeDB-Client requires PHP 5.4 up to PHP 7.1. The connection object might have further 
preconditions.

## Installation

You can install the RIPE client via composer

    composer require dormilich/ripedb-client:^1.1

or by cloning this repository.

### Tests

To run the offline tests run:

    phpunit

on the command line from the project’s root directory.

There is also an online test you can run if you have an internet connection, which runs 
some basic operations on the RIPE TEST database. 

    phpunit --group live

Should these tests fail with a HTTP status code of 409 then the used IP range (127.0.0.0/31) 
already exists in the TEST database and has to be deleted before running the test again.

## Setup

In order to create the HTTP connection with the RIPE REST Service you need to create a 
connection object that implements the `ClientAdapter` interface. Depending on your PHP 
version, or your own preference you can use any existing library or write one using 
PHP’s curl or socket functions. 

If you’re not convenient doing this you can use the `Guzzle6Adapter` from the `tests/Test` 
folder (although you might want to change the namespace). However, be aware that Guzzle 6 
requires PHP 5.5 or above.

## Usage

For the web service there are two options you can set before using it:

* environment - whether to connect to the RIPE (`WebService::PRODUCTION`) or TEST 
(`WebService::SANDBOX`) database. Per default, it connects to the TEST database.
* password - for any modifying operation (create/update/delete) you must provide 
the password for the object’s maintainer. The default password is the one for the 
TEST database’s primary maintainer.

For more information check out the [RIPE REST API Documentation](https://github.com/RIPE-NCC/whois/wiki/WHOIS-REST-API) and the [RIPE Database Documentation](https://www.ripe.net/manage-ips-and-asns/db/support/documentation/ripe-database-documentation)

### Setting up the web service object

```php
// create the connection object
$client = new Client(…);

// create the web service object
$ripe   = new WebService($client, [
	'environment' => WebService::PRODUCTION,
	'password'    => 'your maintainer password',
]);

// you can set also these options separately
$ripe   = new WebService($client);
$ripe->setEnvironment(WebService::PRODUCTION);
```

### Create a RIPE DB entry

```php
try {
	// create a RIPE object
	$me = new Person;

	// setting attributes via array style
	$me['person'] = 'John Doe';

	// setting multiple-valued attributes via array style
	$me['phone']  = [
		'+1 234 56789', 
		'+1 432 98765', 
	];

	// setting attributes via method
	$me
		->addAttribute('address', 'Any Street 1')
		->addAttribute('address', 'Anytown')
	;

	// create object in DB
	$me = $ripe->create($me);

	// display result
	echo '<pre>', $me, '</pre>';
}
catch (RPSLException $e) {
	// errors regarding the setup of the RIPE object
}
// using Guzzle 6 exceptions as example, 
// your client implementation may use different exceptions
catch (BadResponseException $e) {
	$errors = WebService::getErrors($e->getResponse()->getBody()));
}
```

Note: the webservice will set the *source* attribute depending on its setting 
so you don’t need to set it yourself except when you want to use the serializer 
or the `isValid()` method before that.

### Update a RIPE DB entry

```php
try {
	// create a RIPE object with the object’s primary key
	$jd = new Person('JD123-RIPE');

	// fetch the object from the DB
	$jd = $ripe->read($jd);

	// modify the object
	$jd['e-mail'] = 'john.doe@example.com';

	// save the changes
	$jd = $ripe->update($jd);
}
catch (RPSLException $e) {
	// errors regarding the setup of the RIPE object
}
catch (BadResponseException $e) {
	$errors = WebService::getErrors($e->getResponse()->getBody()));
}
```

Note: each RIPE object contains at least the *created* and *last-modified* generated attributes. 
The latter of them is (obviously) only actual after the update therefore *update()* returns the 
latest object instance.

### RIPE references

Some attributes contain references to other RIPE objects (e.g. *tech-c*, *admin-c*, _mnt-*_). 
When you fetch an object from the RIPE database, for these attributes a special value object 
(`AttributeValue`) is created that can provide you the type and lookup object (an object with 
the primary key set to be used in the `read()` method) of the referenced object.


### RIPE comments

The RIPE DB uses the hash sign (`#`) for denoting comments. When fetching an attribute with comments, 
these are transmitted separately from the attribute value. For these case the `AttributeValue` object 
will be used as well. When accessing the attribute value as string, the comment will be appended.

## Notes

Object validation uses RIPE DB version 1.86
