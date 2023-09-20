<?php
/**
 * Math Query Numeric Range Examples
 *
 * This PHP script provides examples showcasing the usage of the Math_Query class for performing various mathematical
 * operations on the "edd_orders" table, including counting, summing, averaging, finding the minimum, and finding the
 * maximum. These examples also illustrate numeric range comparisons.
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

// Example 1: Count orders within a specified numeric range
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

echo "Total number of orders within the range: $count_in_range_orders\n"; // Example 1 Output: Total number of orders within the range: 35

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

echo "Sum of order totals where 'total' is greater than 10: $sum_greater_than_10\n"; // Example 2 Output: Sum of order totals where 'total' is greater than 10: 1274.99

// Note: These examples demonstrate numeric range comparisons for counting and summing operations.