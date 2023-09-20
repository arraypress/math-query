<?php
/**
 * Math Query Column Examples
 *
 * This PHP script provides examples demonstrating the usage of the Math_Query class for performing various
 * mathematical
 * operations on the "edd_orders" table, including column selections and filtering using "__in" and "__not_in"
 * conditions.
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

// Example 1: Count orders with a 'complete' status
$query_args = array(
	'table'    => 'edd_orders',
	'function' => 'COUNT',
	'status'   => 'complete'
);

$math_query            = new Math_Query( $query_args );
$count_complete_orders = $math_query->get_result();

echo "Total number of 'complete' status orders: $count_complete_orders\n"; // Example 1 Output: Total number of 'complete' status orders: 35

// Example 2: Sum the 'total' column for orders with 'pending' or 'refunded' statuses
$query_args = array(
	'table'      => 'edd_orders',
	'column'     => 'total',
	'function'   => 'SUM', // Default function (can be omitted)
	'status__in' => array( 'pending', 'refunded' )
);

$math_query          = new Math_Query( $query_args );
$sum_selected_orders = $math_query->get_result();

echo "Sum of 'total' for 'pending' and 'refunded' status orders: $sum_selected_orders\n"; // Example 2 Output: Sum of 'total' for 'pending' and 'refunded' status orders: 1274.99

// Example 3: Sum the 'total' column for orders with statuses other than 'refunded'
$query_args = array(
	'table'          => 'edd_orders',
	'column'         => 'total',
	'function'       => 'SUM', // Default function (can be omitted)
	'status__not_in' => array( 'refunded' )
);

$math_query              = new Math_Query( $query_args );
$sum_non_refunded_orders = $math_query->get_result();

echo "Sum of 'total' for orders with statuses other than 'refunded': $sum_non_refunded_orders\n"; // Example 3 Output: Sum of 'total' for orders with statuses other than 'refunded': 1274.99

// Note: These examples demonstrate how to select specific columns in the table and filter based on specific statuses.