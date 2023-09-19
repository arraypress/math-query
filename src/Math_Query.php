<?php
/**
 * The Math_Query class simplifies mathematical queries on a database table, offering versatile functionality for
 * data analysis and reporting. It streamlines querying tasks, enabling users to specify table, column, function,
 * date range, and various conditions to retrieve aggregated results with optional result formatting and caching.
 *
 * Please note that this library does not support meta queries via joins, and date queries have limitations and may
 * not be fully compatible with data_query and similar methods.
 *
 * Key Features:
 *
 * - Mathematical Operations: Perform common mathematical operations (e.g., SUM, AVG, COUNT) on specified database
 * columns.
 * - Custom Functions: Calculate results using custom aggregate functions, extending beyond standard SQL functions.
 * - Date Range Queries: Filter data within specified date ranges, making it ideal for time-based analysis.
 * - Advanced Conditions: Apply a wide range of conditions, such as IN, NOT IN, minimum/maximum values, and more, to
 * fine-tune queries.
 * - Result Formatting: Customize the presentation of results through user-defined formatting functions.
 * - Caching: Improve performance by enabling result caching, reducing redundant database queries for identical
 * parameters.
 *
 * @example
 * // Basic Usage:
 * $mathQuery = new Math_Query([
 *     'table'  => 'my_table',
 *     'column' => 'my_column',
 * ]);
 * $result = $mathQuery->get_result();
 *
 * @example
 * // Custom Function and Group By:
 * $mathQuery = new Math_Query([
 *     'table'    => 'sales_data',
 *     'column'   => 'revenue',
 *     'function' => 'AVG', // Calculate average instead of SUM
 *     'group_by' => 'year', // Group results by year
 * ]);
 * $result = $mathQuery->get_result();
 *
 * @example
 * // Date Range Query:
 * $mathQuery = new Math_Query([
 *     'table'        => 'sales_data',
 *     'column'       => 'revenue',
 *     'date_column'  => 'transaction_date',
 *     'date_start'   => '2023-01-01',
 *     'date_end'     => '2023-12-31',
 * ]);
 * $result = $mathQuery->get_result();
 *
 * @example
 * // Advanced Conditions:
 * $mathQuery = new Math_Query([
 *     'table'    => 'inventory',
 *     'column'   => 'quantity',
 *     'function' => 'SUM',
 *     'product_id__in' => [101, 102, 103], // IN condition
 *     'location'       => 'warehouse',
 *     'quantity__min'  => 50, // Minimum quantity condition
 * ]);
 * $result = $mathQuery->get_result();
 *
 * @example
 * // Custom Result Formatting:
 * $mathQuery = new Math_Query([
 *     'table'     => 'data',
 *     'column'    => 'value',
 *     'formatter' => function ($result) {
 *         return "$" . number_format($result, 2); // Format the result as currency
 *     },
 * ]);
 * $result = $mathQuery->get_result();
 *
 * @example
 * // Caching Enabled:
 * $mathQuery = new Math_Query([
 *     'table'          => 'orders',
 *     'column'         => 'total_amount',
 *     'enable_caching' => true, // Enable caching
 * ]);
 * $result = $mathQuery->get_result();
 *
 * This class empowers developers to effortlessly extract valuable insights from database tables, making it an
 * invaluable tool for data-driven applications and reports.
 *
 * @package     arraypress/math-query
 * @copyright   Copyright (c) 2023, ArrayPress Limited
 * @license     GPL2+
 * @since       1.0.0
 * @author      David Sherlock
 * @description Easily perform mathematical queries on WordPress database tables.
 */

namespace ArrayPress\Utils;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if the class `Math_Query` is defined, and if not, define it.
 */
if ( ! class_exists( __NAMESPACE__ . '\\Math_Query' ) ) :
	/**
	 * Class Math_Query
	 *
	 * Performs mathematical queries on a database table.
	 */
	class Math_Query {

		/**
		 * Default query variables.
		 *
		 * @var array
		 */
		protected array $default_query_vars;

		/**
		 * Query variables.
		 *
		 * @var array
		 */
		protected $query_vars = array();

		/**
		 * Math_Query constructor.
		 *
		 * @param array $query Query parameters.
		 *
		 * @throws Exception If invalid parameters are provided.
		 */
		public function __construct( array $query = array() ) {
			$this->default_query_vars = array(
				'table'          => '',
				'column'         => '',
				'function'       => 'SUM',
				'date_column'    => 'date_created',
				'date_start'     => '',
				'date_end'       => '',
				'group_by'       => '',
				'formatter'      => null,
				'enable_caching' => true, // Added default value
				'cache_group'    => 'math_query', // Added default value
				'debug'          => false, // Added default value for debugging,
				'context'        => ''
			);

			$this->query_vars = wp_parse_args( $query, $this->default_query_vars );

			$this->query_vars = apply_filters( 'arraypress_math_query_vars', $this->query_vars, $query );

			global $wpdb;

			if ( $this->query_vars['debug'] ) {
				$wpdb->show_errors();
			}

			if ( ! $this->validate_table( $this->query_vars['table'] ) ) {
				throw new \Exception( 'Invalid table name.' );
			}

			if ( ! $this->validate_column( $this->query_vars['table'], $this->query_vars['column'], $this->query_vars['function'] ) ) {
				throw new \Exception( 'Invalid column name.' );
			}

			if ( ! empty( $this->query_vars['date_start'] ) && ! $this->is_valid_date_format( $this->query_vars['date_start'] ) ) {
				throw new \Exception( 'Invalid date format for date_start.' );
			}

			if ( ! empty( $this->query_vars['date_end'] ) && ! $this->is_valid_date_format( $this->query_vars['date_end'] ) ) {
				throw new \Exception( 'Invalid date format for date_end.' );
			}
		}

		/**
		 * Validate the existence of the given table.
		 *
		 * @param string $table Table name.
		 *
		 * @return bool Whether the table exists.
		 */
		protected function validate_table( $table ): bool {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $this->get_table_name( $table ) ) ) === $this->get_table_name( $table );
		}

		/**
		 * Get the column type of the given column in the specified table.
		 *
		 * @param string $table  Table name.
		 * @param string $column Column name.
		 *
		 * @return string|false The column type if it exists, false otherwise.
		 */
		protected function get_column_type( $table, $column ) {
			global $wpdb;
			$columns = $wpdb->get_col( "DESCRIBE " . $this->get_table_name( $table ), 0 );
			if ( in_array( $column, $columns, true ) ) {
				return $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = %s AND COLUMN_NAME = %s",
						$this->get_table_name( $table ),
						$column
					)
				);
			}

			return false;
		}

		/**
		 * Validate the existence and numeric nature of the given column in the specified table.
		 *
		 * @param string $table    Table name.
		 * @param string $column   Column name.
		 * @param string $function Aggregate function.
		 *
		 * @return bool Whether the column exists and is numeric.
		 * @throws Exception If an error occurs during the column validation.
		 */
		protected function validate_column( $table, $column, $function = '' ): bool {
			$column_type = $this->get_column_type( $table, $column );

			if ( $column_type !== false ) {
				if ( ! empty( $function ) && $function !== 'COUNT' && ! $this->is_numeric_column( $column_type ) ) {
					throw new \Exception( 'Invalid column type. Only numeric columns can be used.' );
				}

				return true;
			}

			return false;
		}

		/**
		 * Checks if the column type indicates a numeric nature.
		 *
		 * @param string $column_type Column type.
		 *
		 * @return bool Whether the column type is numeric.
		 */
		protected function is_numeric_column( $column_type ): bool {
			$numeric_types = array( 'tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'float', 'double' );
			$column_type   = strtolower( $column_type );

			foreach ( $numeric_types as $numeric_type ) {
				if ( strpos( $column_type, $numeric_type ) !== false ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Checks if a given date string is in a valid format.
		 *
		 * @param string $date Date string.
		 *
		 * @return bool Whether the date format is valid.
		 */
		protected function is_valid_date_format( $date ): bool {
			$parsed_date = date_parse( $date );

			return $parsed_date['error_count'] === 0 && $parsed_date['warning_count'] === 0;
		}

		/**
		 * Get the result of the database query based on the specified parameters.
		 *
		 * @return mixed Result of the database query.
		 * @throws Exception If an error occurs during the database query.
		 */
		public function get_result() {
			$table       = $this->query_vars['table'];
			$column      = $this->query_vars['column'];
			$function    = $this->query_vars['function'];
			$date_column = $this->query_vars['date_column'];
			$date_start  = $this->query_vars['date_start'];
			$date_end    = $this->query_vars['date_end'];
			$formatter   = $this->query_vars['formatter'];
			$group_by    = ! empty( $this->query_vars['group_by'] ) ? $this->query_vars['group_by'] : '';

			$cache_key   = $this->generate_cache_key();
			$cache_group = $this->get_cache_group();

			// Check if caching is enabled and the result is cached
			if ( $this->is_caching_enabled() && ( $result = wp_cache_get( $cache_key, $cache_group ) ) ) {
				return $result;
			}

			global $wpdb;

			$query = "SELECT ";

			$allowed_functions = array( 'SUM', 'MAX', 'MIN', 'AVG', 'COUNT' );
			if ( ! in_array( $function, $allowed_functions, true ) ) {
				throw new \Exception( 'Invalid function.' );
			}

			$query .= "$function($column) AS result";

			if ( ! empty( $group_by ) ) {
				$query .= ', ' . sanitize_text_field( $group_by );
			}

			$query .= " FROM " . $this->get_table_name( $table );

			$placeholders = array();

			$where_conditions = $this->generate_where_conditions( $placeholders );

			if ( ! empty( $where_conditions ) ) {
				$query .= " WHERE " . implode( ' AND ', $where_conditions );
			}

			$date_conditions = $this->generate_date_conditions( $date_column, $date_start, $date_end, $placeholders );
			$query           .= $date_conditions['query'];
			$placeholders    = array_merge( $placeholders, $date_conditions['placeholders'] );

			// Add GROUP BY clause if provided
			if ( ! empty( $group_by ) ) {
				$query .= ' GROUP BY ' . $group_by;
			}

			// Execute the database query based on GROUP BY
			if ( ! empty( $group_by ) ) {
				$results = $wpdb->get_results( $query, OBJECT );

				if ( $wpdb->last_error ) {
					throw new \Exception( 'Database query error: ' . $wpdb->last_error );
				}

				// Check if there are results
				if ( empty( $results ) ) {
					return array();
				}

				// Format or parse the result based on the output type
				if ( is_callable( $formatter ) ) {
					$formatted_results = array();
					foreach ( $results as $row ) {
						$formatted_results[ $row->{$group_by} ] = call_user_func( $formatter, $row->result );
					}
					$result = $formatted_results;
				} else {
					$result = array();
					foreach ( $results as $row ) {
						$result[ $row->{$group_by} ] = ( $function === 'COUNT' ) ? intval( $row->result ) : floatval( $row->result );
					}
				}
			} else {
				// Execute the non-grouped query using get_var()
				$query  = $wpdb->prepare( $query, $placeholders );
				$result = $wpdb->get_var( $query );

				if ( $wpdb->last_error ) {
					throw new \Exception( 'Database query error: ' . $wpdb->last_error );
				}

				// Check if there is a result
				if ( $result === null ) {
					return null;
				}

				// Format or parse the result based on the output type
				if ( is_callable( $formatter ) ) {
					$result = call_user_func( $formatter, $result );
				} else {
					$result = ( $function === 'COUNT' ) ? intval( $result ) : floatval( $result );
				}
			}

			// Cache the result if caching is enabled
			if ( $this->is_caching_enabled() ) {
				wp_cache_set( $cache_key, $result, $cache_group );
			}

			return $result;
		}


		/**
		 * Generate WHERE conditions for the query.
		 *
		 * @param array $placeholders Array of placeholders for prepared statements.
		 *
		 * @return array Array of WHERE conditions.
		 * @throws Exception If an error occurs during the WHERE conditions generation.
		 */
		protected function generate_where_conditions( &$placeholders ): array {
			$where_conditions = array();

			foreach ( $this->query_vars as $key => $value ) {
				if ( strpos( $key, '__in' ) !== false ) {
					$column_name    = str_replace( '__in', '', $key );
					$condition_type = 'IN';
					$this->generate_in_condition( $column_name, $value, $placeholders, $where_conditions, $condition_type );
				} elseif ( strpos( $key, '__not_in' ) !== false ) {
					$column_name    = str_replace( '__not_in', '', $key );
					$condition_type = 'NOT IN';
					$this->generate_in_condition( $column_name, $value, $placeholders, $where_conditions, $condition_type );
				} else {
					$column_name = $key;

					// Skip if the key is not a new condition
					if ( array_key_exists( $key, $this->default_query_vars ) ) {
						continue;
					}

					if ( ! $this->validate_column( $this->query_vars['table'], $column_name ) ) {
						throw new \Exception( 'Invalid column name: ' . $column_name );
					}

					if ( is_array( $value ) ) {
						if ( isset( $value['min'] ) || isset( $value['max'] ) ) {
							$this->generate_numeric_condition( $column_name, $value, $placeholders, $where_conditions );
						} elseif ( isset( $value['value'] ) && isset( $value['compare'] ) ) {
							$this->generate_numeric_compare_condition( $column_name, $value['compare'], $value['value'], $placeholders, $where_conditions );
						} else {
							throw new \Exception( 'Invalid value provided for condition: ' . $value );
						}
					} else {
						$this->generate_basic_condition( $column_name, $value, $placeholders, $where_conditions );
					}
				}
			}

			return $where_conditions;
		}

		/**
		 * Generate IN or NOT IN condition for the query.
		 *
		 * @param string $column_name      Column name.
		 * @param array  $values           Array of values.
		 * @param array  $placeholders     Array of placeholders for prepared statements.
		 * @param array  $where_conditions Array of WHERE conditions.
		 * @param string $condition_type   Condition type (IN or NOT IN).
		 *
		 * @throws Exception If an error occurs during the condition generation.
		 */
		protected function generate_in_condition( $column_name, $values, &$placeholders, &$where_conditions, $condition_type ) {
			if ( ! is_array( $values ) ) {
				throw new \Exception( 'Values must be an array for ' . $condition_type . ' condition.' );
			}

			$formatted_values = array_map( 'sanitize_text_field', $values );
			$placeholders_str = implode( ', ', array_fill( 0, count( $formatted_values ), '%s' ) );

			$placeholders       = array_merge( $placeholders, $formatted_values );
			$where_conditions[] = "$column_name $condition_type ($placeholders_str)";
		}

		/**
		 * Generate numeric condition (min/max) for the query.
		 *
		 * @param string $column_name      Column name.
		 * @param array  $value            Array containing min and/or max values.
		 * @param array  $placeholders     Array of placeholders for prepared statements.
		 * @param array  $where_conditions Array of WHERE conditions.
		 *
		 * @throws Exception If an error occurs during the condition generation.
		 */
		protected function generate_numeric_condition( $column_name, $value, &$placeholders, &$where_conditions ) {
			$min_value = $value['min'] ?? null;
			$max_value = $value['max'] ?? null;

			if ( $min_value !== null && $max_value !== null ) {
				$this->generate_numeric_range_condition( $column_name, $min_value, $max_value, $placeholders, $where_conditions );
			} elseif ( $min_value !== null ) {
				$this->generate_numeric_compare_condition( $column_name, '>=', $min_value, $placeholders, $where_conditions );
			} elseif ( $max_value !== null ) {
				$this->generate_numeric_compare_condition( $column_name, '<=', $max_value, $placeholders, $where_conditions );
			}
		}

		/**
		 * Generate numeric range condition (min/max) for the query.
		 *
		 * @param string $column_name      Column name.
		 * @param mixed  $min_value        Minimum value for the condition.
		 * @param mixed  $max_value        Maximum value for the condition.
		 * @param array  $placeholders     Array of placeholders for prepared statements.
		 * @param array  $where_conditions Array of WHERE conditions.
		 *
		 * @throws Exception If an error occurs during the condition generation.
		 */
		protected function generate_numeric_range_condition( $column_name, $min_value, $max_value, &$placeholders, &$where_conditions ) {
			$placeholder_min     = $this->get_placeholder_type( $min_value );
			$placeholder_max     = $this->get_placeholder_type( $max_value );
			$formatted_min_value = is_numeric( $min_value ) ? $min_value : sanitize_text_field( $min_value );
			$formatted_max_value = is_numeric( $max_value ) ? $max_value : sanitize_text_field( $max_value );
			$where_conditions[]  = "$column_name >= $placeholder_min AND $column_name <= $placeholder_max";
			$placeholders[]      = $formatted_min_value;
			$placeholders[]      = $formatted_max_value;
		}

		/**
		 * Generate numeric comparison condition for the query.
		 *
		 * @param string $column_name      Column name.
		 * @param string $operator         Comparison operator (e.g., '>', '<', '>=', '<=').
		 * @param mixed  $value            Value for the condition.
		 * @param array  $placeholders     Array of placeholders for prepared statements.
		 * @param array  $where_conditions Array of WHERE conditions.
		 *
		 * @throws Exception If an error occurs during the condition generation.
		 */
		protected function generate_numeric_compare_condition( $column_name, $operator, $value, &$placeholders, &$where_conditions ) {
			$allowed_operators = array( '=', '>', '>=', '<', '<=', '!=' );

			if ( ! in_array( $operator, $allowed_operators, true ) ) {
				throw new \Exception( 'Invalid operator: ' . $operator );
			}

			$placeholder        = $this->get_placeholder_type( $value );
			$formatted_value    = is_numeric( $value ) ? $value : sanitize_text_field( $value );
			$where_conditions[] = "$column_name $operator $placeholder";
			$placeholders[]     = $formatted_value;
		}

		/**
		 * Generate basic condition (equality) for the query.
		 *
		 * @param string $column_name      Column name.
		 * @param mixed  $value            Value for the condition.
		 * @param array  $placeholders     Array of placeholders for prepared statements.
		 * @param array  $where_conditions Array of WHERE conditions.
		 *
		 * @throws Exception If an error occurs during the condition generation.
		 */
		protected function generate_basic_condition( $column_name, $value, &$placeholders, &$where_conditions ) {
			$placeholder_type   = $this->get_placeholder_type( $value );
			$placeholders[]     = $value;
			$where_conditions[] = "$column_name = $placeholder_type";
		}

		/**
		 * Generate date conditions for the query.
		 *
		 * @param string $date_column  Date column name.
		 * @param string $date_start   Start day and time.
		 * @param string $date_end     End day and time.
		 * @param array  $placeholders Array of placeholders for prepared statements.
		 *
		 * @return array {
		 *     Array containing the date conditions and placeholders.
		 *
		 * @type string  $query        Query string for the date conditions.
		 * @type array   $placeholders Array of placeholders for prepared statements.
		 *                             }
		 *
		 * @throws Exception If an error occurs during the date conditions generation.
		 */
		protected function generate_date_conditions( $date_column, $date_start, $date_end, &$placeholders ): array {
			$date_conditions = array(
				'query'        => '',
				'placeholders' => array(),
			);

			if ( ! empty( $date_column ) && ( ! empty( $date_start ) || ! empty( $date_end ) ) ) {
				$date_conditions['query'] = ' AND ';
				if ( ! empty( $date_start ) ) {
					if ( ! $this->is_valid_date_format( $date_start ) ) {
						throw new \Exception( 'Invalid date format for date_start.' );
					}
					$date_conditions['query']          .= "$date_column >= %s";
					$date_conditions['placeholders'][] = $date_start;
				}
				if ( ! empty( $date_end ) ) {
					if ( ! $this->is_valid_date_format( $date_end ) ) {
						throw new \Exception( 'Invalid date format for date_end.' );
					}
					if ( ! empty( $date_start ) ) {
						$date_conditions['query'] .= ' AND ';
					}
					$date_conditions['query']          .= "$date_column <= %s";
					$date_conditions['placeholders'][] = $date_end;
				}
				$placeholders = array_merge( $placeholders, $date_conditions['placeholders'] );
			}

			return $date_conditions;
		}

		/**
		 * Get the placeholder type based on the value type.
		 *
		 * @param mixed $value Value to determine the placeholder type.
		 *
		 * @return string Placeholder type.
		 */
		protected function get_placeholder_type( $value ): string {
			if ( is_int( $value ) ) {
				return '%d';
			} elseif ( is_float( $value ) ) {
				return '%f';
			} else {
				return '%s';
			}
		}

		/**
		 * Get the prefixed table name.
		 *
		 * @param string $table Table name.
		 *
		 * @return string Prefixed table name.
		 */
		protected function get_table_name( $table ): string {
			global $wpdb;

			return $wpdb->prefix . $table;
		}

		/**
		 * Generate a unique cache key based on the query parameters.
		 *
		 * @return string Cache key.
		 */
		protected function generate_cache_key(): string {
			$hash = md5( serialize( $this->query_vars ) );

			return $this->get_cache_group() . '_' . $hash;
		}

		/**
		 * Check if caching is enabled.
		 *
		 * @return bool Whether caching is enabled.
		 */
		protected function is_caching_enabled(): bool {
			return (bool) $this->query_vars['enable_caching'];
		}

		/**
		 * Get the cache group.
		 *
		 * @return string Whether caching is enabled.
		 */
		protected function get_cache_group(): string {
			return ! empty( $this->query_vars['cache_group'] )
				? $this->query_vars['cache_group']
				: 'math_query';
		}

	}

endif;