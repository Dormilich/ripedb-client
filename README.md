# RipeDB-Client

A PHP library to communicate with the RIPE NCC database.

## Requirements

The RipeDB-Client requires PHP 5.4 or above. The connection object might have further 
preconditions.

## Installation

Install the RIPE client via composer

    composer install dormilich/ripe-client

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
version or your own preference you can use any existing library or write one using 
PHP’s curl or socket functions. 

If you’re not convienient doing this you can use the `Guzzle6Adapter` from the `tests/Test` 
folder (although you might want to change the namespace). However, be aware that Guzzle 6 
requires PHP 5.5 or above.

## Usage

For the web service there are two options you can set before using it:

* environment - whether to connect to the RIPE (`WebService::PRODUCTION`) or TEST 
(`WebService::SANDBOX`) database. Per default it connects to the TEST database.
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
	// using setAttribute() you can only set a single value
	// even if it is a multiple-valued attribute
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

### Supported WHOIS methods

```php
Object public function WebService::read ( Object $object [, array $options = array("unfiltered") ] )
```
Fetch a RIPE object by its primary key. For this lookup the input object does not have to be valid.

Supported options are:
* unfiltered (include all available fields)
* unformatted (do not strip down whitespace)

```php
Object public function WebService::create ( Object $object )
```
Create a new RIPE object.

```php
Object public function WebService::update ( Object $object )
```
Update an existing RIPE object. Mind that the object is updated as a whole, so it doesn’t suffice to 
set only the fields to be changed. Therefore you should always fetch a fresh copy of the object 
from the database using (at least) the *unfiltered* option.

```php
Object public function WebService::delete ( Object $object )
```
Delete a RIPE object by its primary key. For this action the input object does not have to be valid.

```php
array public function WebService::versions ( Object $object )
```
Fetch the history of a RIPE object. For this lookup the input object does not have to be valid.

The returned array consists of the object’s revision number as array key and the modification date 
followed by the modification type.

Note: Object versions are not available for `Person` and `Role` objects.

```php
Object public function WebService::version ( Object $object, integer $version )
```
Fetch a given version of a RIPE object. The available version numbers can be fetched through the 
`versions()` method.

Note: Object versions are not available for `Person` and `Role` objects.

```php
integer public function WebService::search ( string $value [, array $params ] )
```
Search in the RIPE database for a given value. The optional search parameters define further 
search constraints.

The method returns the number of found results and the search results are available through 
the `getAllResults()` method.

Search options:
* inverse-attribute
* include-tag 
* exclude-tag
* type-filter
* flags

cf. [RIPE Database Query](https://apps.db.ripe.net/search/query.html)

```php
string public function WebService::abuseContact ( mixed $value )
```
This method looks up the abuse contact email address for a single IP address, `Inetnum`, `Inet6num`, 
or `AutNum` (autonomous system) object.

```php
Dummy public function WebService::getObjectFromTemplate ( mixed $name )
```
Create an empty RIPE object (without type contraints) according the the definitions as 
retrieved from the database. 

### Web service methods

```php
boolean public function WebService::isProduction ()
```
Returns `true` if the web service is in production mode.

```php
string public function WebService::getPassword ()
```
Get the currently set password.

```php
WebService public function WebService::setPassword ()
```
Set the password. Only required for the `create()`, `update()`, and `delete()` methods.

```php
string public function WebService::getEnvironment ()
```
Get the current environment name.

```php
WebService public function WebService::setEnvironment ()
```
Set the current environment by name. Safest way is to use the class constants 
`WebService::PRODUCTION` and `WebService::SANDBOX`.

```php
Object public function WebService::getResult ()
```
Return the (first) result object or `false` if the result set is empty.

```php
array public function WebService::getAllResults ()
```
Return an array of all result objects. 

```php
array public static function WebService::getErrors ( string $body )
```
Parse a response body for RIPE error messages.


## Notes

* Object validation uses RIPE DB version 1.80
