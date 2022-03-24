# RELEASE NOTES
## v2.0.1
Fix validation of Google geocoding response

## v2.0.0
 - Remove support for PHP 7.2, add support for PHP 8
 - Remove attributes not supported by other functionality
    - Suburb
    - Gallery
    - Allow Store Delivery
 - Code standards fixes
 - Add schema whitelist

## v1.2.9
Copy extension attributes onto the returned item within the DataProvider

## v1.2.8
Better handling of null/empty trading hours values.

## v1.2.7

 - Fix minor bug in region update, and save full name of region to database
 - Fix bug where country wouldn't save on change, only on initial creation
 - Fix bug where trading hours json would double serialize public holiday data
Code cleanup

## v1.2.6

Add ability to disable stockists

## v1.2.5.3

Handle empty gallery entries

## v1.2.5.2

Filter HTML output of gallery entries

## v1.2.5.1

Fix address data empty in GraphQL

## v1.2.5.0

Add new attributes to Stockists

- Description
- URL Key
- Gallery
- Allow store delivery
- Suburb

## v1.2.4.2

Fix opening hours not updating

## 1.2.4.0

The first open source release of the M2 stockists module.
