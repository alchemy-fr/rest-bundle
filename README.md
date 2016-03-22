# Alchemy - Rest bundle 

[![License](https://img.shields.io/packagist/l/alchemy/rest-bundle.svg?style=flat-square)](https://github.com/alchemy-fr/rest-bundle/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/alchemy/rest-bundle.svg?style=flat-square)](https://packagist.org/packages/alchemy/rest-bundle)
[![Travis](https://img.shields.io/travis/alchemy-fr/rest-bundle.svg?style=flat-square)](https://travis-ci.org/alchemy-fr/rest-bundle)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/alchemy-fr/rest-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/alchemy-fr/rest-bundle/?branch=master)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/alchemy-fr/rest-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/alchemy-fr/rest-bundle/)
[![Packagist](https://img.shields.io/packagist/dt/alchemy/rest-bundle.svg?style=flat-square)](https://packagist.org/packages/alchemy/rest-bundle/stats)

## Features

- Provides automatic date parameter parsing using a predefined format and timezone
- Provides automatic sort and pagination parameter parsing
- Provides standardized error responses in AJAX/JSON contexts

## Configuration

Enable the bundle by adding it to the app kernel.

By default, all listeners are enabled. You can add the following section to your `config.yml` to alter the behavior
of the listeners:

*Note* The following configuration matches the default settings

```yml
alchemy_rest:
    dates:
        enabled: true
        format: Y-m-d H:i:s
        timezone: UTC
    exceptions:
        enabled: true
        content-types: [ "application/json" ]
        # Set this to null to use default transformer, or use the key of a service implementing
        # Alchemy\Rest\Response\ExceptionTransformer
        transformer: null
    sort:
        enabled: true
        sort_parameter: sort
        direction_parameter: dir
        mutli_sort_parameter: sorts
    pagination:
        enabled: true
        limit_parameter: limit
        offset_parameter: offset
```

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

### Transforming controller results into JSON responses:

This listener is always activated. To use it, you must first write a Transformer (see the League/Fractal documentation
for information on transformers), and define it as a tagged service in your dependency injection configuration:

```yml
services:
    my_transformer:
        class: My\Transformer
        tags: 
            - { name: alchemy_rest.transformer, alias: my_transformer }
```

Then in your routing file, you need to specify the transformer for a given route:

```yml
my_application.api_route:
    pattern: /api/route
    defaults:
        _rest:
            decode_request: true # Enabled by default, decodes JSON request bodies into 
            encode_response: true
            transform: my_transformer
            list: false
```

You can use the `list` parameter in your route defaults to specifiy whether the controller result should be handled
as a list or as a simple object. If your controller returns an instance of PagerFanta (you must include the library
in your project as it is an optional dependency), the response will automatically include a `meta` property containing
a `pagination` property with the pagination metadata.
