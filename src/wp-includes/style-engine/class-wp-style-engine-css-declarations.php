<?php
/**
 * WP_Style_Engine_CSS_Declarations
 *
 * Holds, sanitizes and prints CSS rules declarations
 *
 * @package WordPress
 * @subpackage StyleEngine
 * @since 6.1.0
 */

/**
 * Class WP_Style_Engine_CSS_Declarations.
 *
 * Holds, sanitizes, processes and prints CSS declarations for the style engine.
 *
 * @access private
 * @since 6.1.0
 */
class WP_Style_Engine_CSS_Declarations {

	/**
	 * An array of CSS declarations (property => value pairs).
	 *
	 * @since 6.1.0
	 *
	 * @var array
	 */
	protected $declarations = array();

	/**
	 * Constructor for this object.
	 *
	 * If a `$declarations` array is passed, it will be used to populate
	 * the initial $declarations prop of the object by calling add_declarations().
	 *
	 * @since 6.1.0
	 *
	 * @param array $declarations An array of declarations (property => value pairs).
	 */
	public function __construct( $declarations = array() ) {
		$this->add_declarations( $declarations );
	}

	/**
	 * Add a single declaration.
	 *
	 * @since 6.1.0
	 *
	 * @param string $property The CSS property.
	 * @param string $value    The CSS value.
	 *
	 * @return WP_Style_Engine_CSS_Declarations Returns the object to allow chaining methods.
	 */
	public function add_declaration( $property, $value ) {

		// Sanitize the property.
		$property = $this->sanitize_property( $property );
		// Bail early if the property is empty.
		if ( empty( $property ) ) {
			return $this;
		}

		// Trim the value. If empty, bail early.
		$value = trim( $value );
		if ( '' === $value ) {
			return $this;
		}

		// Add the declaration property/value pair.
		$this->declarations[ $property ] = $value;

		return $this;
	}

	/**
	 * Remove a single declaration.
	 *
	 * @since 6.1.0
	 *
	 * @param string $property The CSS property.
	 *
	 * @return WP_Style_Engine_CSS_Declarations Returns the object to allow chaining methods.
	 */
	public function remove_declaration( $property ) {
		unset( $this->declarations[ $property ] );
		return $this;
	}

	/**
	 * Add multiple declarations.
	 *
	 * @since 6.1.0
	 *
	 * @param array $declarations An array of declarations.
	 *
	 * @return WP_Style_Engine_CSS_Declarations Returns the object to allow chaining methods.
	 */
	public function add_declarations( $declarations ) {
		foreach ( $declarations as $property => $value ) {
			$this->add_declaration( $property, $value );
		}
		return $this;
	}

	/**
	 * Remove multiple declarations.
	 *
	 * @since 6.1.0
	 *
	 * @param array $properties An array of properties.
	 *
	 * @return WP_Style_Engine_CSS_Declarations Returns the object to allow chaining methods.
	 */
	public function remove_declarations( $properties = array() ) {
		foreach ( $properties as $property ) {
			$this->remove_declaration( $property );
		}
		return $this;
	}

	/**
	 * Get the declarations array.
	 *
	 * @since 6.1.0
	 *
	 * @return array
	 */
	public function get_declarations() {
		return $this->declarations;
	}

	/**
	 * Filters a CSS property + value pair.
	 *
	 * @since 6.1.0
	 *
	 * @param string $property The CSS property.
	 * @param string $value    The value to be filtered.
	 * @param string $spacer   The spacer between the colon and the value. Defaults to an empty string.
	 *
	 * @return string The filtered declaration as a single string.
	 */
	protected static function filter_declaration( $property, $value, $spacer = '' ) {
		$filtered_value = wp_strip_all_tags( $value, true );
		if ( '' !== $filtered_value ) {
			return safecss_filter_attr( "{$property}:{$spacer}{$filtered_value}" );
		}
		return '';
	}

	/**
	 * Filters and compiles the CSS declarations.
	 *
	 * @since 6.1.0
	 *
	 * @param bool   $should_prettify Whether to add spacing, new lines and indents.
	 * @param number $indent_count    The number of tab indents to apply to the rule. Applies if `prettify` is `true`.
	 *
	 * @return string The CSS declarations.
	 */
	public function get_declarations_string( $should_prettify = false, $indent_count = 0 ) {
		$declarations_array  = $this->get_declarations();
		$declarations_output = '';
		$indent              = $should_prettify ? str_repeat( "\t", $indent_count ) : '';
		$suffix              = $should_prettify ? ' ' : '';
		$suffix              = $should_prettify && $indent_count > 0 ? "\n" : $suffix;
		$spacer              = $should_prettify ? ' ' : '';

		foreach ( $declarations_array as $property => $value ) {
			$filtered_declaration = static::filter_declaration( $property, $value, $spacer );
			if ( $filtered_declaration ) {
				$declarations_output .= "{$indent}{$filtered_declaration};$suffix";
			}
		}
		return rtrim( $declarations_output );
	}

	/**
	 * Sanitize property names.
	 *
	 * @since 6.1.0
	 *
	 * @param string $property The CSS property.
	 *
	 * @return string The sanitized property name.
	 */
	protected function sanitize_property( $property ) {
		return sanitize_key( $property );
	}
}
