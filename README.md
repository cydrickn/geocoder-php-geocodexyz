# Geocode.xyz Geocode Provider

[![PHP from Packagist](https://img.shields.io/packagist/php-v/cydrickn/geocoder-php-geocodexyz.svg)](https://packagist.org/packages/cydrickn/geocoder-php-geocodexyz)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/cydrickn/geocoder-php-geocodexyz.svg)](https://packagist.org/packages/cydrickn/geocoder-php-geocodexyz)
[![Software License](https://img.shields.io/packagist/l/cydrickn/geocoder-php-geocodexyz.svg)](LICENSE)
[![Run Status](https://api.shippable.com/projects/5bd06d7956f6dd0700fbb734/badge?branch=master)]()
[![Coverage Badge](https://api.shippable.com/projects/5bd06d7956f6dd0700fbb734/coverageBadge?branch=master)]()

This project is a provider for 
[Geocoder-PHP](https://github.com/geocoder-php/Geocoder) to 
enable the consumption of the Geocode.xyz API.

The API detailed in [Geocode.xyz documentation](https://geocode.xyz/api)

### Install

```bash
composer require cydrickn/geocoder-php-geocodexyz
```

### Options

|Key|Description|Available Value|Default|Customizable in Query Data|
|---|-----------|---------------|-------|--------------------------|
|geomode| **strictmode**: prevent the geocoder from making too many guesses on your input.<br/>**nostrict**: Will return all matched locations, even those with low confidence | strictmode, nostrict | nostrict | yes |
|region|The region parameter defines the region/country to limit the search results for geoparsing functions (scantext) or single response geocoding (locate)|see all available values in (https://geocode.xyz/api)|World|yes|
|auth|The authentication code (for registered/subscribed users).|||no|