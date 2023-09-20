<?php
/**
 * Math_Query Date Range Examples
 *
 * This script demonstrates the usage of the Math_Query class to perform various mathematical
 * operations on the "edd_orders" table while applying a date range filter. Operations include
 * counting, summing, averaging, finding the minimum, and finding the maximum.
 *
 * @package     arraypress/math-query
 * @copyright   Copyright (c) 2023, ArrayPress Limited
 * @license     GPL2+
 * @since       1.0.0
 * @author      David Sherlock
 */

// Include the Composer-generated autoload file.
require_once dirname(__FILE__) . '/vendor/autoload.php';

// Include your Math_Query class here or use the appropriate namespace if it's in a different file.
use ArrayPress\Utils\Math_Query;

// Example 1: Count all orders within a specified date range
$query_args = array(
	'table'       => 'edd_orders',
	'function'    => 'COUNT',
	'date_column' => 'date_created',
	'date_start'  => '2023-01-01',
	'date_end'    => '2023-12-31',
);

$math_query       = new Math_Query($query_args);
$count_all_orders = $math_query->get_result();

echo "Total number of orders: $count_all_orders\n"; // Example 1 Output: Total number of orders: 35

// Example 2: Calculate the sum of order totals within a specified date range
$query_args = array(
	'table'       => 'edd_orders',
	'column'      => 'total',
	'function'    => 'SUM', // Default function (can be omitted)
	'date_column' => 'date_created',
	'date_start'  => '2023-01-01',
	'date_end'    => '2023-12-31',
);

$math_query          = new Math_Query($query_args);
$sum_of_order_totals = $math_query->get_result();

echo "Sum of order totals: $sum_of_order_totals\n"; // Example 2 Output: Sum of order totals: 1274.99

// Example 3: Count all orders with a start date but no end date (no specific date range)
$query_args = array(
	'table'       => 'edd_orders',
	'function'    => 'COUNT',
	'date_column' => 'date_created',
	'date_start'  => '2023-01-01',
);

$math_query       = new Math_Query($query_args);
$count_orders_no_end_date = $math_query->get_result();

echo "Total number of orders (no end date): $count_orders_no_end_date\n"; // Example 3 Output: Total number of orders (no end date): 45

// Note: These examples demonstrate how to use a date column and query data within a specific date range.
// Example 3 shows a scenario where there is a start date but no end date specified.