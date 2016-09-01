# ListOnce API Wrapper for PHP
This library should be considered alpha and could change at any time. It is recommended that you specify a commit when using composer.

## Installation

```
composer require thegallagher/list-once:dev-master@dev
```

## Usage

```php
<?php
$listOnce = ListOnce\Factory::makeListOnce($apiKey);

// Get one listing
$listing = $listOnce->getListing($listingId);
echo $listing->listing_id;
echo $listing->headline;
echo $listing->description;

// Search listings
$listings = $listOnce->searchListings([
    'suburb' => 'Sydney',
    'property_type' => 'residential',
    'listing_type' => 'sale',
    'max_price' => 900000
]);
foreach ($listings as $listing) {
    echo $listing->listing_id;
    echo $listing->headline;
    echo $listing->description;
}

// And much more. See src/Provider/ListOnce.php
?>
```

## Todo
* Cached provider does not work. It uses a previous version of the library
* Finish implemeting the Message class
* Provider interface
* Add methods to entities which can grab related entities and pages

## License
The library is open-sourced software licensed under the MIT license.
