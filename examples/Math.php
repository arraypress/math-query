<?php
/**
 * Math_Query Examples
 *
 * This script demonstrates the usage of the Math_Query class to perform various mathematical
 * operations on the "edd_orders" table, such as counting, summing, averaging, finding the
 * minimum, and finding the maximum.
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

// Example 1: Count all orders
$query_args = array(
	'table'    => 'edd_orders',
	'function' => 'COUNT',
);

$math_query       = new Math_Query( $query_args );
$count_all_orders = $math_query->get_result();

echo "Total number of orders: $count_all_orders\n"; // Example 1 Output: Total number of orders: 35

// Example 2: Calculate the sum of order totals
$query_args = array(
	'table'    => 'edd_orders',
	'column'   => 'total',
	'function' => 'SUM', // Default function (can be omitted)
);

$math_query          = new Math_Query( $query_args );
$sum_of_order_totals = $math_query->get_result();

echo "Sum of order totals: $sum_of_order_totals\n"; // Example 2 Output: Sum of order totals: 1274.99

// Example 3: Calculate the average order total
$query_args = array(
	'table'    => 'edd_orders',
	'column'   => 'total',
	'function' => 'AVG',
);

$math_query          = new Math_Query( $query_args );
$average_order_total = $math_query->get_result();

echo "Average order total: $average_order_total\n"; // Example 3 Output: Average order total: 36.42

// Example 4: Find the minimum order total
$query_args = array(
	'table'    => 'edd_orders',
	'column'   => 'total',
	'function' => 'MIN',
);

$math_query      = new Math_Query( $query_args );
$min_order_total = $math_query->get_result();

echo "Minimum order total: $min_order_total\n"; // Example 4 Output: Minimum order total: 10.99

// Example 5: Find the maximum order total
$query_args = array(
	'table'    => 'edd_orders',
	'column'   => 'total',
	'function' => 'MAX',
);

$math_query      = new Math_Query( $query_args );
$max_order_total = $math_query->get_result();

echo "Maximum order total: $max_order_total\n"; // Example 5 Output: Maximum order total: 199.99