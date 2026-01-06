# Aligent Stockists
This module is designed to allow vendors to add physical store information, including addresses and coordinates, and provides functionality for:

 - Store lookup by latitide/longitude
 - Store lookup by URL key (for individual store pages)
 - Rich text descriptions using PageBuilder

## Installation
To install via composer, simply run:

```bash
composer require aligent/magento-stockists-module
```

## Configuration
Geocoding settings can be found under `Stores -> Configuration -> Aligent -> Stockists`.
By default, Google Maps API is the service used, but any URL can be specified.

## Viewing/Adding/Editing Stockists
This module adds a new "Stockists" menu, which gives the following options:

   - All Stockists: View all current stockists in a grid
   - Add Stockist: Go directly to the new stockist form

When adding a new stockist, you must enter the following:

   - Is Active?
   - Identifier
   - Name
   - URL Key
   - Store IDs (choose 1 or more store views)
   - Country
   - State/Province (If the chosen country requires it)
   - Postcode

Additional information such as telephone number, street address, and trading hours can also be added.

## GraphQL

Three GraphQL queries are provided:

   - stockists      
      - Returns a list of stockists within a given radius, ordered by distance
      - Must provide customer's latitude and longitude as input
   - stockist
      - Returns the information for a single stockist given an identifier or URL key
   - searchStockistsByAddress
      - Search stockists by address fields 

## CSV Import 
Allow import Stockists via CSV import. `System -> Import` then select the `Stockists` as entity type.
