# Alchemy - Rest bundle

[![Build Status](https://travis-ci.org/alchemy-fr/rest-bundle.svg?branch=master)](https://travis-ci.org/alchemy-fr/rest-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alchemy-fr/rest-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alchemy-fr/rest-bundle/?branch=master)

## Features

- Provides automatic date parameter parsing using a predefined format and timezone
- Provides automatic sort and pagination parameter parsing
- Provides standardized error responses in AJAX/JSON contexts

## Configuration

Enable the bundle by adding it to the app kernel.

### Available parameters

The following application parameters allow you to control the behavior of the the bundle:

- `alchemy_rest.content_types`: An array of content type names for which to enable exception handling and formatting.
- `alchemy_rest.date_timezone`: Name of the timezone that will be used to parse date parameters.
- `alchemy_rest.date_format`: Format string that will be used to parse date parameters. See the PHP documentation of 
`\DateTime::createFromFormat()` for valid format strings.

## Usage

### Automatic date parsing

To activate date conversions on request parameters, you must explicitly define which parameters will be parsed as dates
on a per-route basis in your routing files.

#### Example

Assuming that your requests will contain a `from` and a `to` parameter:

```yml
my_application.api_route:
    pattern: /api/route
    defaults:
        _dates: [ to, from ]
```

You can now type-hint your controller method as follows:
 
```php
class MyController 
{
    public function index(\DateTimeInterface $from, \DateTimeInterface $to) 
    {
        // Do something with those dates...    
    }
}
```

### Automatic sort and pagination

To activate automatic sorting and pagination parameter parsing, you must explicitly activate them on a per-route basis
in your routing files.

#### Simple example

```yml
my_application.api_route:
    pattern: /api/route
    defaults:
        _paginate: true
        _sort: true
```

You can now type-hint your controller method as follows:
 
```php
class MyController 
{
    public function index(PaginationOptions $pagination, SortOptions $sort) 
    {
        // Do something...    
    }
}
```
