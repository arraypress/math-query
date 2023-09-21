<?php
/**
 * Math_Query Utility Functions
 *
 * This functions.php file houses the Math_Query class, which simplifies mathematical queries on a database table.
 * It offers versatile functionality for data analysis and reporting by streamlining querying tasks. Users can specify
 * the table, column, function, date range, and various conditions to retrieve aggregated results with optional result
 * formatting and caching.
 *
 * @package     arraypress/math-query
 * @author      David Sherlock
 * @copyright   Copyright (c) 2023, ArrayPress Limited
 * @license     GPL2+
 * @since       1.0.0
 */

namespace ArrayPress\Utils;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use Exception;
use InvalidArgumentException;

/**
 * Get the result of a mathematical query using the Math_Query class.
 *
 * This function creates an instance of the Math_Query class with the provided query arguments
 * and retrieves the result of the query.
 *
 * @param array $query The query arguments for the Math_Query class.
 *
 * @return mixed The result of the mathematical query, or an error message.
 */
function get_math_query_result( array $query ) {
	try {
		// Check if the 'table' key is provided in the query arguments.
		if ( ! isset( $query['table'] ) ) {
			throw new InvalidArgumentException( 'Table name is required in the query arguments.' );
		}

		// Create an instance of the Math_Query class.
		$math_query = new Math_Query( $query );

		// Retrieve and return the result of the query.
		return $math_query->get_result();
	} catch ( Exception $e ) {
		// Echo the error message directly to the user.
		echo $e->getMessage();

		// Return null or any other value as needed.
		return null;
	}
}
