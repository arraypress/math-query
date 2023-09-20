# WordPress Math Query Database Library

Perform mathematical queries on a WordPress database table using the `Math_Query` class. This library simplifies complex
database queries for summing, averaging, and more.

**Key Features:**

* **Schema Validation:** The library automatically validates table schemas and ensures column data types match, providing peace of mind when crafting queries.
* **Built-in Caching:** Enjoy the benefits of built-in caching to boost performance, and if needed, effortlessly disable it to suit your specific needs.
* **Context-Aware WordPress Filters:** Harness the potential of context-aware WordPress filters that enhance your querying capabilities and adapt to various scenarios seamlessly.
* **Universal Compatibility:** Whether you're working with default or custom database tables, the Math_Query class is your reliable companion for any data source.
* **Robust Error Handling:** Experience error and exception handling that guides you when incorrect data is passed in, making debugging and troubleshooting a breeze.

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

The `Math_Query` class allows you to perform mathematical queries on a WordPress database table. Here are examples of
how to use it for various types of queries:

### Including the Vendor Library

Before using the Math_Query class, you need to include the Composer-generated autoload file. This file ensures that the required dependencies and classes are loaded into your PHP script. You can include it using the following code:

```php 
// Include the Composer-generated autoload file.
require_once dirname(__FILE__) . '/vendor/autoload.php';
```

### Counting Rows
```php
$query_args = array(
    'table'    => 'edd_orders',
    'function' => 'COUNT',
);

$math_query       = new Math_Query( $query_args );
$count_all_orders = $math_query->get_result();

echo "Total number of orders: $count_all_orders\n"; 

```
In this example, we use the `Math_Query` class to count all records in the `edd_orders` table. The function parameter is set to 'COUNT,' indicating a count operation. Since no specific column is specified, it counts all records in the table. The result is stored in `$count_all_orders`, and we print the total number of orders.

### Summation of Order Totals
```php
$query_args = array(
    'table'    => 'edd_orders',
    'column'   => 'total',
    'function' => 'SUM', // Default function (can be omitted)
);

$math_query          = new Math_Query( $query_args );
$sum_of_order_totals = $math_query->get_result();

echo "Sum of order totals: $sum_of_order_totals\n";
```
In this example, we calculate the sum of order totals from the `total` column in the `edd_orders` table. We specify the function parameter as `SUM` to perform a sum operation. The column parameter specifies the column from which the sum is calculated, which is 'total' in this case. The result is stored in `$sum_of_order_totals`, and we print the sum.

The `Math_Query` class supports various mathematical functions that you can use to perform operations on your data. The accepted functions include:

* SUM (default)
* MIN
* MAX
* AVG
* COUNT

### Summation of 'total' for 'pending' and 'refunded' Status Orders
```php
$query_args = array(
	'table'      => 'edd_orders',
	'column'     => 'total',
	'function'   => 'SUM', // Default function (can be omitted)
	'status__in' => array( 'pending', 'refunded' )
);

$math_query          = new Math_Query( $query_args );
$sum_selected_orders = $math_query->get_result();

echo "Sum of 'total' for 'pending' and 'refunded' status orders: $sum_selected_orders\n";
```
n this example, we sum the "total" column for orders with 'pending' or 'refunded' statuses from the `edd_orders` table. The function parameter is set to 'SUM' to perform a sum operation. We use the status__in parameter to filter orders with either 'pending' or 'refunded' statuses. The result, which is the sum of "total" values for selected orders, is stored in $sum_selected_orders, and we print the sum.

### Summation of 'total' for Orders with Statuses Other Than 'Refunded'
```php
$query_args = array(
	'table'          => 'edd_orders',
	'column'         => 'total',
	'function'       => 'SUM', // Default function (can be omitted)
	'status__not_in' => array( 'refunded' )
);

$math_query              = new Math_Query( $query_args );
$sum_non_refunded_orders = $math_query->get_result();

echo "Sum of 'total' for orders with statuses other than 'refunded': $sum_non_refunded_orders\n";
```
In this example, we sum the "total" column for orders with statuses other than 'refunded' from the "edd_orders" table. The function parameter is set to 'SUM' to perform a sum operation. We use the status__not_in parameter to filter orders that are not 'refunded.' The result, which is the sum of "total" values for non-'refunded' orders, is stored in $sum_non_refunded_orders, and we print the sum.

### Counting Orders Within a Specified Date Range
```php
$query_args = array(
    'table'       => 'edd_orders',
    'function'    => 'COUNT',
    'date_column' => 'date_created',
    'date_start'  => '2023-01-01',
    'date_end'    => '2023-12-31',
);

$math_query       = new Math_Query($query_args);
$count_all_orders = $math_query->get_result();

echo "Total number of orders: $count_all_orders\n";
```
In this example, we use the Math_Query class to count all orders within a specified date range in the "edd_orders" table. The function parameter is set to 'COUNT' for a count operation. We specify the date column using the date_column parameter and set the date range using date_start and date_end parameters. The result, which is the total number of orders within the specified date range, is stored in $count_all_orders, and we print the count.

### Counting Orders Within a Numeric Range
```php
$query_args = array(
    'table'    => 'edd_orders',
    'function' => 'COUNT',
    'total'    => array(
        'min' => 10,
        'max' => 30
    )
);

$math_query            = new Math_Query( $query_args );
$count_in_range_orders = $math_query->get_result();

echo "Total number of orders within the range: $count_in_range_orders\n";
```
In this example, we use the Math_Query class to count orders within a specified numeric range in the "edd_orders" table. The function parameter is set to 'COUNT' for a count operation. We specify the numeric range using the total parameter with 'min' and 'max' values. The result, which is the total number of orders within the specified range, is stored in $count_in_range_orders, and we print the count.

### Summation of Order Totals with Comparison
```php
// Example 2: Calculate the sum of order totals where the 'total' value is greater than 10
$query_args = array(
    'table'    => 'edd_orders',
    'column'   => 'total',
    'function' => 'SUM', // Default function (can be omitted)
    'total'    => array(
        'value'   => 10,
        'compare' => '>' // Accepted operators '=', '>', '>=', '<', '<=', '!='
    )
);

$math_query          = new Math_Query( $query_args );
$sum_greater_than_10 = $math_query->get_result();

echo "Sum of order totals where 'total' is greater than 10: $sum_greater_than_10\n";
```
In this example, we calculate the sum of order totals from the "total" column in the "edd_orders" table. We specify the function parameter as 'SUM' to perform a sum operation. Additionally, we apply a condition using the total parameter, specifying that we want to sum values greater than 10 with the '>' comparison operator. The result is stored in $sum_greater_than_10, and we print the sum.

### Counting Orders Grouped by 'status'
```php
// Example 1: Count all orders grouped by 'status'
$query_args = array(
    'table'    => 'edd_orders',
    'function' => 'COUNT',
    'group_by' => 'status',
);

$math_query       = new Math_Query( $query_args );
$count_all_orders = $math_query->get_result();

// Display the result using print_r
echo "Count of all orders grouped by 'status':\n";
print_r( $count_all_orders );

// Or display the result using echo
echo "\nCount of all orders grouped by 'status':\n";
foreach ( $count_all_orders as $status => $count ) {
    echo "Status: $status, Count: $count\n";
}
```
In this example, we use the `Math_Query` class to count all orders in the "edd_orders" table and group them by 'status.' The function parameter is set to 'COUNT' for a count operation. The group_by parameter is used to group the results by the 'status' column. We retrieve the results into $count_all_orders and then display them using both print_r and echo. This allows you to see the count of orders for each 'status' category.

## Contributions

Contributions to this library are highly appreciated. Raise issues on GitHub or submit pull requests for bug
fixes or new features. Share feedback and suggestions for improvements.

## License

This library is licensed under
the [GNU General Public License v2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).