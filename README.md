# Copernica API PHP Bindings/Implementation

The goal of this package was to make it more usable when writing small synchronization scripts or just communicating to the Copernica API in a RESTful way.

You will need an access token for your application from the 
Copernica Dashboard which can be found [here.](https://www.copernica.com/nl/applications)

Example of how to use the package.

Follow the available methods and function calls from the [official Copernica Documentation](https://www.copernica.com/nl/documentation/rest-api)

```php
$api = new \TomKriek\CopernicaAPI\CopernicaAPI('access token here');

$api->database(1)->fields()->get();
```
This will fetch all fields for the database with ID 1

The package is in a super infancy stage with missing tests and not all API exceptions being caught and processed correctly.
Feel free to create issues or fork the repo and make PRs to improve or enhance it.
