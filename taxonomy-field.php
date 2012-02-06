<?php
/*
 * Copyright (c) 2012, CAMPUS CRUSADE FOR CHRIST
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     Redistributions of source code must retain the above copyright notice, this
 *         list of conditions and the following disclaimer.
 *     Redistributions in binary form must reproduce the above copyright notice,
 *         this list of conditions and the following disclaimer in the documentation
 *         and/or other materials provided with the distribution.
 *     Neither the name of CAMPUS CRUSADE FOR CHRIST nor the names of its
 *         contributors may be used to endorse or promote products derived from this
 *         software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */
?>
<?php

if( !class_exists( 'ACF_Taxonomy_Field' ) && class_exists( 'acf_Field' ) ) :

/**
 * Advanced Custom Fields - Taxonomy Field add-on
 * 
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 * @version 1.0
 */
class ACF_Taxonomy_Field extends acf_Field {
	/**
	 * Base directory
	 * @var string
	 */
	private $base_dir;
	
	/**
	 * Relative Uri from the WordPress ABSPATH constant
	 * @var string
	 */
	private $base_uri_rel;
	
	/**
	 * Absolute Uri
	 * 
	 * This is used to create urls to CSS and JavaScript files.
	 * @var string
	 */
	private $base_uri_abs;
	
	/**
	 * WordPress Localization Text Domain
	 * 
	 * The textdomain for the field is controlled by the helper class.
	 * @var string
	 */
	private $l10n_domain;
	
	/**
	 * Class Constructor - Instantiates a new Taxonomy Field
	 * @param Acf $parent Parent Acf class
	 */
	public function __construct( $parent ) {
		//Call parent constructor
		parent::__construct( $parent );
		
		//Get the textdomain from the Helper class
		$this->l10n_domain = ACF_Taxonomy_Field_Helper::L10N_DOMAIN;
		
		//Base directory of this field
		$this->base_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' );
		
		//Build the base relative uri by searching backwards until we encounter the wordpress ABSPATH
		$root = array_pop( explode( '/', rtrim( ABSPATH, '/' ) ) );
		$path_parts = explode( '/', $this->base_dir );
		$parts = array();
		while( $part = array_pop( $path_parts ) ) {
			if( $part == $root )
				break;
			array_unshift( $parts, $part );
		}
		$this->base_uri_rel = '/' . implode( '/', $parts );
		$this->base_uri_abs = get_site_url( null, $this->base_uri_rel );
		
		$this->name  = 'taxonomy-field';
		$this->title = __( 'Taxonomy', $this->l10n_doamin );
		
		add_action( 'admin_print_scripts', array( &$this, 'admin_print_scripts' ), 12, 0 );
		add_action( 'admin_print_styles',  array( &$this, 'admin_print_styles' ),  12, 0 );
	}
	
	/**
	 * Registers and enqueues necessary CSS
	 * 
	 * This method is called by ACF when rendering a post add or edit screen.
	 * We also call this method on the Acf Field Options screen as well in order
	 * to style our Field options
	 * 
	 * @see acf_Field::admin_print_styles()
	 */
	public function admin_print_styles() {
		global $pagenow;
//		wp_register_style( 'acf-taxonomy-field', $this->base_uri_abs . '/taxonomy-field.css' );
		
		if( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
//			wp_enqueue_style( 'acf-taxonomy-field' );
		}
	}
	
	/**
	 * Registers and enqueues necessary JavaScript
	 * 
	 * This method is called by ACF when rendering a post add or edit screen.
	 * We also call this method on the Acf Field Options screen as well in order
	 * to add the necessary JavaScript for taxonomy selection.
	 * 
	 * @see acf_Field::admin_print_scripts()
	 */
	public function admin_print_scripts() {
		global $pagenow;
//		wp_register_script( 'acf-taxonomy-field', $this->base_uri_abs . '/taxonomy-field.js', array( 'jquery' ) );
		
		if( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
//			wp_enqueue_script( 'acf-taxonomy-field' );
		}
	}
	
	/**
	 * Populates the fields array with defaults for this field type
	 * 
	 * @param array $field
	 * @return array
	 */
	private function set_field_defaults( &$field ) {
		$field[ 'taxonomy' ]        = ( array_key_exists( 'taxonomy', $field ) && isset( $field[ 'taxonomy' ] ) ) ? $field[ 'taxonomy' ] : 'category';
		$field[ 'input_type' ]      = ( array_key_exists( 'input_type', $field ) && isset( $field[ 'input_type' ] ) ) ? $field[ 'input_type' ] : 'select';
		$field[ 'input_size' ]      = ( array_key_exists( 'input_size', $field ) && isset( $field[ 'input_size' ] ) ) ? (int) $field[ 'input_size' ] : 5;
//		$field[ 'allow_new_terms' ] = ( array_key_exists( 'allow_new_terms', $field ) && isset( $field[ 'allow_new_terms' ] ) ) ? (int) $field[ 'allow_new_terms' ] : 0; //false
		$field[ 'set_post_terms' ]  = ( array_key_exists( 'set_post_terms', $field ) && isset( $field[ 'set_post_terms' ] ) ) ? (int) $field[ 'set_post_terms' ] : 1; //true
		return $field;
	}
	
	/**
	 * Creates the taxonomy field for inside post metaboxes
	 * 
	 * @see acf_Field::create_field()
	 */
	public function create_field( $field ) {
		$this->set_field_defaults( $field );
		
		$terms = get_terms( $field['taxonomy'], array( 'hide_empty' => false ) );
		$value = $field[ 'value' ];
		
		if( in_array( $field[ 'input_type' ], array( 'select', 'multiselect' ) ) ) :
		?>
			<select name="<?php echo $field[ 'name' ]; ?>[]" id="<?php echo $field[ 'name' ]; ?>" class="<?php echo $field[ 'class' ]; ?>" <?php echo ( $field[ 'input_type' ] == 'multiselect' ) ? 'multiple="multiple" size="' . $field[ 'input_size' ] . '"' : ''; ?>>
				<?php foreach( $terms as $term ) : ?>
					<option value="<?php echo $term->term_id; ?>" <?php selected( in_array( $term->term_id, $value, true ) ); ?>><?php echo $term->name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php
		endif;
	}
	
	/**
	 * Builds the field options
	 * 
	 * @see acf_Field::create_options()
	 * @param string $key
	 * @param array $field
	 */
	public function create_options( $key, $field ) {
		$this->set_field_defaults( $field );
		
		$taxonomies = get_taxonomies( array(), 'objects' );
		ksort( $taxonomies );
		$tax_choices = array();
		foreach( $taxonomies as $tax )
			$tax_choices[ $tax->name ] = $tax->label;
		
		?>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Taxonomy' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Select the taxonomy to display.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'    => 'select',
							'name'    => "fields[{$key}][taxonomy]",
							'value'   => $field[ 'taxonomy' ],
							'choices' => $tax_choices,
						) );
					?>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Input Method' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( '', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'    => 'select',
							'name'    => "fields[{$key}][input_type]",
							'value'   => $field[ 'input_type' ],
							'class'   => 'taxonomy_input_type',
							'choices' => array(
								'select'      => 'Select',
								'multiselect' => 'Multi-Select',
								//'token'       => 'Input Tokenizer',
							),
						) );
					?>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Set Post Terms' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Add the selected term(s) to the post. The current post must support the selected taxonomy for this to work.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'  => 'true_false',
							'name'  => "fields[{$key}][set_post_terms]",
							'value' => $field[ 'set_post_terms' ],
						) );
					?>
				</td>
			</tr>
<!--
			<tr class="field_option field_option_<?php echo $this->name; ?> taxonomy_add_terms">
				<td class="label">
					<label><?php _e( 'Add New Terms' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Add any new terms to the selected taxonomy.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'  => 'true_false',
							'name'  => "fields[{$key}][allow_new_terms]",
							'value' => $field[ 'allow_new_terms' ],
						) );
					?>
				</td>
			</tr>
-->
			<tr class="field_option field_option_<?php echo $this->name; ?> taxonomy_input_size">
				<td class="label">
					<label><?php _e( 'Multi-Select Size' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'The number of terms to show at once in a multi-select.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'    => 'select',
							'name'    => "fields[{$key}][input_size]",
							'value'   => $field[ 'input_size' ],
							'choices' => array_combine( range( 3, 15, 2 ), range( 3, 15, 2 ) ),
						) );
					?>
				</td>
			</tr>
		<?php
	}
	
	public function update_value( $post_id, $field, $value ) {
		$this->set_field_defaults( $field );
		
		if( $field[ 'set_post_terms' ] ) {
			$terms = array();
			foreach( (array) $value as $item ) {
				if( intval( $item ) > 0 )
					$terms[] = intval( $item );
				else
					$terms[] = strval( $item );
			}
			$value = wp_set_object_terms( $post_id, $terms, $field[ 'taxonomy' ], false );
		}
		
		return parent::update_value( $post_id, $field, $value );
	}
	
	/**
	 * Returns the values of the field
	 * 
	 * @see acf_Field::get_value()
	 * @param int $post_id
	 * @param array $field
	 * @return mixed  
	 */
	public function get_value( $post_id, $field ) {
		$value = (array) parent::get_value( $post_id, $field );
		return $value;
	}
	
	/**
	 * Returns the value of the field for the advanced custom fields API
	 * 
	 * @see acf_Field::get_value_for_api()
	 * @param int $post_id
	 * @param array $field
	 * @return string
	 */
	public function get_value_for_api( $post_id, $field ) {
		return parent::get_value_for_api($post_id, $field);
	}
}

endif; //class_exists 'ACF_Taxonomy_Field'

if( !class_exists( 'ACF_Taxonomy_Field_Helper' ) ) :

/**
 * Advanced Custom Fields - Taxonomy Field Helper
 * 
 * This class is a singleton thats primary job is to register the Taxonomy Field
 * with Advanced Custom Fields. Developers using this field do not need to worry
 * about how to register it with Advanced Custom Fields. Simply include this 
 * php file and the ACF_Taxonomy_Field_Helper does the rest.
 * <code> include_once( rtrim( dirname( __FILE__ ), '/' ) . '/acf-taxonomy-field/taxonomy-field.php' ); </code>
 * 
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 */
class ACF_Taxonomy_Field_Helper {
	/**
	 * Singleton instance
	 * @var ACF_Taxonomy_Field_Helper
	 */
	private static $instance;
	
	/**
	 * Returns the ACF_Taxonomy_Field_Helper singleton
	 * 
	 * <code>$obj = ACF_Taxonomy_Field_Helper::singleton();</code>
	 * @return ACF_Taxonomy_Field_Helper
	 */
	public static function singleton() {
		if( !isset( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	/**
	 * Prevent cloning of the ACF_Taxonomy_Field_Helper object
	 * @internal
	 */
	private function __clone() {
	}
	
	/**
	* WordPress Localization Text Domain
	*
	* Used in wordpress localization and translation methods.
	* @var string
	*/
	const L10N_DOMAIN = 'acf-taxonomy-field';
	
	/**
	 * Language directory path
	 * 
	 * Used to build the path for WordPress localization files.
	 * @var string
	 */
	private $lang_dir;
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->lang_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' ) . '/languages';
		
		add_action( 'init', array( &$this, 'register_field' ), 5, 0 );
		add_action( 'init', array( &$this, 'load_textdomain' ),        2, 0 );
	}
	
	/**
	 * Registers the Field with Advanced Custom Fields
	 */
	public function register_field() {
		if( function_exists( 'register_field' ) ) {
			register_field( 'ACF_Taxonomy_Field', __FILE__ );
		}
	}
	
	/**
	 * Loads the textdomain for the current locale if it exists
	 */
	public function load_textdomain() {
		$locale = get_locale();
		$mofile = $this->lang_dir . '/' . self::L10N_DOMAIN . '-' . $locale . '.mo';
		load_textdomain( self::L10N_DOMAIN, $mofile );
	}
}
endif; //class_exists 'ACF_Taxonomy_Field_Helper'

//Instantiate the Addon Helper class
ACF_Taxonomy_Field_Helper::singleton();