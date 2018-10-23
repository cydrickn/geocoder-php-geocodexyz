# Geocode.xyz Geocode Provider

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
|searchmode|**locate**: The location may be a street address/postal code/landmark/ip address/city name/etc (forward geocoding) or a latitude,longitude point (reverse geocoding)<br/>**scantext**: Free form text containing locations.|locate,scantext|locate|yes|
|region|The region parameter defines the region/country to limit the search results for geoparsing functions (scantext) or single response geocoding (locate)|see all available values in (https://geocode.xyz/api)|World|yes|
|auth|The authentication code (for registered/subscribed users).|||no|