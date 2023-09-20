# WordPress Math Query Database Library

Perform mathematical queries on a WordPress database table using the `Math_Query` class. This library simplifies complex database queries for summing, averaging, and more.

## Installation and set up

The extension in question needs to have a `composer.json` file, specifically with the following:

```json 
{
  "require": {
    "arraypress/math-query": "*"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/arraypress/math-query"
    }
  ]
}
```

Once set up, run `composer install --no-dev`. This should create a new `vendors/` folder
with `arraypress/math-query/` inside.

## Using the Math Query Library

The `Math_Query` class allows you to perform mathematical queries on a WordPress database table. Here are examples of how to use it for various types of queries:

```php 
// Include the Composer-generated autoload file.
require_once dirname(__FILE__) . '/vendor/autoload.php';
```

1. The `vendor/autoload.php` file is included to autoload classes from Composer.

2. The `$external_links` array defines the external links you want to add to your plugin. Each link is an array with the
   following properties:

| Variable Name     | Description                                                | Default Value          | Examples                             |
|-------------------|------------------------------------------------------------|------------------------|--------------------------------------|
| `table`           | Name of the database table to query (no prefix)           | Empty string            | `'edd_orders'`                       |
| `column`          | Numeric column for calculations                           | Empty string            | `'total_sales'`                      |
| `function`        | Mathematical function (e.g., SUM, AVG, MAX)              | `'SUM'`                 | `'AVG'`, `'MAX'`                     |
| `date_column`     | Date-based column for date filtering                      | `'date_created'`        | `'order_date'`                        |
| `date_start`      | Start date in MySQL format (optional)                     | Empty string (optional) | `'2023-01-01 00:00:00'`              |
| `date_end`        | End date in MySQL format (optional)                       | Empty string (optional) | `'2023-12-31 23:59:59'`              |
| `group_by`        | Column to group results by (optional)                     | Empty string (optional) | `'status'`                           |
| `formatter`       | Callback function for result formatting (optional)        | `null`                   | `function($value) { return $value; }` |
| `enable_caching`  | Flag to enable or disable caching                         | `true`                   | `false`                              |
| `cache_group`     | Cache group name                                           | `'math_query'`           | `'custom_cache'`                     |
| `debug`           | Flag for debugging purposes                               | `false`                  | `true`                               |
| `context`         | Context for filter hook                                    | Empty string            | `'my_custom_context'`                |

3. The `$utm_args` array defines the default UTM parameters for all links.

An instance of the `Plugin_Meta` class is created with the provided settings, adding the external links to your
WordPress plugin's admin page.

## Contributions

Contributions to this library are highly appreciated. Raise issues on GitHub or submit pull requests for bug
fixes or new features. Share feedback and suggestions for improvements.

## License

This library is licensed under
the [GNU General Public License v2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).