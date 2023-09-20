<?php
/**
 * Math Query Utility Functions
 *
 * The Math_Query class simplifies mathematical queries on a database table, offering versatile functionality for
 * data analysis and reporting. It streamlines querying tasks, enabling users to specify table, column, function,
 * date range, and various conditions to retrieve aggregated results with optional result formatting and caching.
 *
 * This utility provides a convenient way to perform mathematical queries on WordPress database tables.
 * Please note that this library does not support meta queries via joins, and date queries have limitations and may
 * not be fully compatible with data_query and similar methods.
 *
 * @package     arraypress/math-query
 * @subpackage  Utils
 * @since       1.0.0
 * @link        https://github.com/arraypress/math-query
 * @license     GPL2+
 * @author      David Sherlock
 */

namespace ArrayPress\Utils;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Exception;

/**
 * Perform a mathematical query on a database table.
 *
 * This function allows you to easily perform mathematical queries on WordPress database tables by specifying
 * essential parameters such as 'table' and 'column' in the query array.
 *
 * @param array $query Query parameters including 'table' and 'column'.
 *
 * @return mixed Result of the mathematical query.
 * @throws Exception If required parameters are missing or if an error occurs during the query.
 */
function math_query( array $query = array() ) {
	// Check if 'table' and 'column' are provided in the query
	if ( empty( $query['table'] ) || empty( $query['column'] ) ) {
		throw new Exception( 'Missing required parameters. Both "table" and "column" must be provided in the query.' );
	}

	try {
		// Instantiate the Math_Query class
		$math_query = new Math_Query( $query );

		// Perform the mathematical query and return the result
		return $math_query->get_result();
	} catch ( Exception $e ) {
		// An error occurred during the query, so throw an exception
		throw $e;
	}
}
