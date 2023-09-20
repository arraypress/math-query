<?php
/**
 * Math_Query Group By Examples
 *
 * This script demonstrates the usage of the Math_Query class to perform various mathematical
 * operations on the "edd_orders" table, such as counting and summing, with the ability to
 * group results by a specific column.
 *
 * @package     arraypress/math-query
 * @copyright   Copyright (c) 2023, ArrayPress Limited
 * @license     GPL2+
 * @since       1.0.0
 * @author      David Sherlock
 */

// Include the Composer-generated autoload file.
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

// Include your Math_Query class here or use the appropriate namespace if it's in a different file.
use ArrayPress\Utils\Math_Query;

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

// Example 2: Calculate the sum of order totals grouped by 'status'
$query_args = array(
	'table'    => 'edd_orders',
	'column'   => 'total',
	'function' => 'SUM', // Default function (can be omitted),
	'group_by' => 'status',
);

$math_query          = new Math_Query( $query_args );
$sum_of_order_totals = $math_query->get_result();

// Display the result using print_r
echo "\nSum of order totals grouped by 'status':\n";
print_r( $sum_of_order_totals );

// Or display the result using echo
echo "\nSum of order totals grouped by 'status':\n";
foreach ( $sum_of_order_totals as $status => $sum ) {
	echo "Status: $status, Sum: $sum\n";
}
