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

## Using the Plugin Meta Library

The Math_Query class allows you to perform mathematical queries on a WordPress database table. Here are examples of how to use it for various types of queries:

```php 
// Include the Composer-generated autoload file.
require_once dirname(__FILE__) . '/vendor/autoload.php';


```

1. The `vendor/autoload.php` file is included to autoload classes from Composer.

2. The `$external_links` array defines the external links you want to add to your plugin. Each link is an array with the
   following properties:

| Key       | Description                                                                    | Required | Default Value       | Example                         |
|-----------|--------------------------------------------------------------------------------|----------|---------------------|---------------------------------|
| `action`  | Set to true if it's an action link or false for a row meta link.               | Optional | `false`             | `true`                          |
| `label`   | The label or text for the link.                                                | Required | `''` (empty string) | `'Support'`                     |
| `url`     | The URL the link should point to.                                              | Required | `''` (empty string) | `'https://example.com/support'` |
| `utm`     | Set to true to add UTM parameters or false to omit them.                       | Optional | `true`              | `false`                         |
| `new_tab` | Set to true to open the link in a new tab or false to open it in the same tab. | Optional | `true`              | `false`                         |

3. The `$utm_args` array defines the default UTM parameters for all links.

An instance of the `Plugin_Meta` class is created with the provided settings, adding the external links to your
WordPress plugin's admin page.

## Contributions

Contributions to this library are highly appreciated. Raise issues on GitHub or submit pull requests for bug
fixes or new features. Share feedback and suggestions for improvements.

## License

This library is licensed under
the [GNU General Public License v2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).