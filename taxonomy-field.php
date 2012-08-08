<?php
/*
* Plugin Name: Advanced Custom Fields - Taxonomy Field add-on
* Plugin URI:  https://github.com/GCX/acf-taxonomy-field
* Description: This plugin is an add-on for Advanced Custom Fields. It provides a dropdown of taxonomy terms and the ability to map the selected terms to the post.
* Author:      Brian Zoetewey
* Author URI:  https://github.com/GCX
* Version:     1.4
* Text Domain: acf-taxonomy-field
* Domain Path: /languages/
* License:     Modified BSD
*/
?>
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
 * @version 1.4
 */
class ACF_Taxonomy_Field extends acf_Field {

	/**
	 * Field name
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Field class
	 * @var string
	 */
	const FIELD_CLASS = 'class';

	/**
	 * Field value
	 * @var string
	 */
	const FIELD_VALUE = 'value';

	/**
	 * Field taxonomy type
	 * @var string
	 */
	const FIELD_TAXONOMY = 'taxonomy';

	/**
	 * Field input type
	 * @var string
	 */
	const FIELD_INPUT_TYPE = 'input_type';

	/**
	 * Field input size
	 * @var string
	 */
	const FIELD_INPUT_SIZE = 'input_size';

	/**
	 * Field set post terms
	 * @var string
	 */
	const FIELD_SET_TERMS = 'set_post_terms';

	/**
	 * Field return value type
	 * @var string
	 */
	const FIELD_RETURN_TYPE = 'return_value_type';

	/**
	 * Field use post terms for value
	 * @var string
	 */
	const FIELD_USE_TERMS = 'use_post_terms';

	/**
	 * Input Type select
	 * @var string
	 */
	const INPUT_TYPE_SELECT = 'select';

	/**
	 * Input Type multiselect
	 * @var string
	 */
	const INPUT_TYPE_MULTISELECT = 'multiselect';

	/**
	 * Input Type hierarchical checkboxes
	 * @var string
	 */
	const INPUT_TYPE_CHECKBOX = 'hierarchical';

	/**
	 * Set Post Terms not set
	 * @var string
	 */
	const SET_TERMS_NOT_SET = 'not_set';

	/**
	 * Set Post Terms append
	 * @var string
	 */
	const SET_TERMS_APPEND = 'append';

	/**
	 * Set Post Terms override
	 * @var string
	 */
	const SET_TERMS_OVERRIDE = 'override';

	/**
	 * Return Value Type IDs
	 * @var string
	 */
	const RETURN_TYPE_ID = 'id';

	/**
	 * Return Value Type objects
	 * @var string
	 */
	const RETURN_TYPE_OBJECT = 'object';

	/**
	 * Return Value Type links
	 * @var string
	 */
	const RETURN_TYPE_LINK = 'link';

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
		$this->base_dir = rtrim( dirname( realpath( __FILE__ ) ), DIRECTORY_SEPARATOR );
		
		//Build the base relative uri by searching backwards until we encounter the wordpress ABSPATH
		//This may not work if the $base_dir contains a symlink outside of the WordPress ABSPATH
		$root = array_pop( explode( DIRECTORY_SEPARATOR, rtrim( realpath( ABSPATH ), '/' ) ) );
		$path_parts = explode( DIRECTORY_SEPARATOR, $this->base_dir );
		$parts = array();
		while( $part = array_pop( $path_parts ) ) {
			if( $part == $root )
				break;
			array_unshift( $parts, $part );
		}
		$this->base_uri_rel = '/' . implode( '/', $parts );
		$this->base_uri_abs = get_site_url( null, $this->base_uri_rel );
		
		$this->name  = 'taxonomy-field';
		$this->title = __( 'Taxonomy', $this->l10n_domain );
	}
	
	/**
	 * Populates the fields array with defaults for this field type
	 * 
	 * @param array $field
	 * @return array
	 */
	private function set_field_defaults( &$field ) {
		$field[ self::FIELD_TAXONOMY ]   = ( array_key_exists( self::FIELD_TAXONOMY, $field ) && isset( $field[ self::FIELD_TAXONOMY ] ) ) ? $field[ self::FIELD_TAXONOMY ] : 'category';
		$field[ self::FIELD_INPUT_TYPE ] = ( array_key_exists( self::FIELD_INPUT_TYPE, $field ) && isset( $field[ self::FIELD_INPUT_TYPE ] ) ) ? $field[ self::FIELD_INPUT_TYPE ] : self::INPUT_TYPE_SELECT;
		$field[ self::FIELD_INPUT_SIZE ] = ( array_key_exists( self::FIELD_INPUT_SIZE, $field ) && isset( $field[ self::FIELD_INPUT_SIZE ] ) ) ? (int) $field[ self::FIELD_INPUT_SIZE ] : 5;
		$field[ self::FIELD_USE_TERMS ]  = ( array_key_exists( self::FIELD_USE_TERMS, $field ) && isset( $field[ self::FIELD_USE_TERMS ] ) ) ? (int) $field[ self::FIELD_USE_TERMS ] : 0; //default false

		$field[ self::FIELD_SET_TERMS ]  = ( array_key_exists( self::FIELD_SET_TERMS, $field ) && isset( $field[ self::FIELD_SET_TERMS ] ) ) ? $field[ self::FIELD_SET_TERMS ] : self::SET_TERMS_NOT_SET;
		if( $field[ self::FIELD_SET_TERMS ] == '1' ) $field[ self::FIELD_SET_TERMS ] = self::SET_TERMS_OVERRIDE;
		elseif( $field[ self::FIELD_SET_TERMS ] == '0' ) $field[ self::FIELD_SET_TERMS ] = self::SET_TERMS_NOT_SET;

		$field[ self::FIELD_RETURN_TYPE ] = isset( $field[ self::FIELD_RETURN_TYPE ] ) ? $field[ self::FIELD_RETURN_TYPE ] : self::RETURN_TYPE_LINK;
		return $field;
	}
	
	/**
	 * Creates the taxonomy field for inside post metaboxes
	 * 
	 * @see acf_Field::create_field()
	 */
	public function create_field( $field ) {
		$this->set_field_defaults( $field );
		
		$field[ self::FIELD_VALUE ] = is_array( $field[ self::FIELD_VALUE ] ) ? $field[ self::FIELD_VALUE ] : array();

		if( in_array( $field[ self::FIELD_INPUT_TYPE ], array( self::INPUT_TYPE_SELECT, self::INPUT_TYPE_MULTISELECT ) ) ) :
		?>
			<select name="<?php echo $field[ self::FIELD_NAME ]; ?>[]" id="<?php echo $field[ self::FIELD_NAME ]; ?>" class="<?php echo $field[ self::FIELD_CLASS ]; ?>" <?php echo ( $field[ self::FIELD_INPUT_TYPE ] == self::INPUT_TYPE_MULTISELECT ) ? 'multiple="multiple" size="' . $field[ self::FIELD_INPUT_SIZE ] . '"' : ''; ?>>
				<?php
					wp_list_categories( array(
						'taxonomy'     => $field[ self::FIELD_TAXONOMY ],
						'hide_empty'   => false,
						'hierarchical' => is_taxonomy_hierarchical( $field[ self::FIELD_TAXONOMY ] ),
						'style'        => 'none',
						'walker'       => new ACF_Walker_Taxonomy_Field_List( $field ),
					) );
				?>
			</select>
		<?php
		elseif ( in_array( $field[ self::FIELD_INPUT_TYPE ], array( self::INPUT_TYPE_CHECKBOX ) ) ):
		$id = "{$field[ self::FIELD_NAME ]}-{$field[ self::FIELD_TAXONOMY ]}";
		?>
			<div id="taxonomy-<?php echo $id; ?>" class="categorydiv">
				<div id="<?php echo $id; ?>-all" class="tabs-panel">
					<?php
					$name = ( $field[ self::FIELD_TAXONOMY ] == 'category' ) ? 'post_category' : $field[ self::FIELD_NAME ];
					echo "<input type='hidden' name='{$name}' value='' />";
					?>
					<ul id="<?php echo $id; ?>checklist" class="list:<?php echo $field[ self::FIELD_TAXONOMY ]; ?> categorychecklist form-no-clear">
						<?php 
							wp_terms_checklist( 0, array(
								'name'          => $name,
								'checked_ontop' => false,
								'selected_cats' => $field[ self::FIELD_VALUE ],
								'taxonomy'      => $field[ self::FIELD_TAXONOMY ],
								'walker'        => new ACF_Walker_Taxonomy_Field_Checklist($field)
							) );
						?>
					</ul>
				</div>
			</div>
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
							'name'    => "fields[{$key}][" . self::FIELD_TAXONOMY . "]",
							'value'   => $field[ self::FIELD_TAXONOMY ],
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
							'name'    => "fields[{$key}][" . self::FIELD_INPUT_TYPE . "]",
							'value'   => $field[ self::FIELD_INPUT_TYPE ],
							'class'   => 'taxonomy_input_type',
							'choices' => array(
								self::INPUT_TYPE_SELECT      => __( 'Select', $this->l10n_domain ),
								self::INPUT_TYPE_MULTISELECT => __( 'Multi-Select', $this->l10n_domain ),
								self::INPUT_TYPE_CHECKBOX    => __( 'Hierarchical Checkboxes', $this->l10n_domain ),
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
							'type'    => 'radio',
							'name'    => 'fields[' . $key . '][' . self::FIELD_SET_TERMS . ']',
							'value'   => $field[ self::FIELD_SET_TERMS ],
							'layout'  => 'horizontal',
							'choices' => array(
								self::SET_TERMS_NOT_SET  => __( 'Not Set', $this->l10n_domain),
								self::SET_TERMS_APPEND   => __( 'Append Terms', $this->l10n_domain ),
								self::SET_TERMS_OVERRIDE => __( 'Override Terms', $this->l10n_domain ),
							)
						) );
					?>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Use Post Terms for Field Value' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Pre-populate the field value using the terms assigned to the post.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php
						$this->parent->create_field( array(
							'type'    => 'true_false',
							'name'    => 'fields[' . $key . '][' . self::FIELD_USE_TERMS . ']',
							'value'   => $field[ self::FIELD_USE_TERMS ],
							'message' => __( 'Pre-populate the field value', $this->l10n_domain ),
						) );
					?>
					<p class="description"><?php _e( 'Setting this option will cause the field value as well as the get_value() api call to use the terms assigned to the post as the value of the field. Enabling this option when using a Repeater or the same taxonomy multiple times in an ACF group will cause all the taxonomy fields have the same values, regardless of the values selected.', $this->l10n_domain ); ?></p>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
				<td class="label">
					<label><?php _e( 'Return Value', $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'Choose the field value type returned by API calls.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
					$this->parent->create_field(array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][' . self::FIELD_RETURN_TYPE . ']',
						'value'   => $field[ self::FIELD_RETURN_TYPE ],
						'layout'  => 'horizontal',
						'choices' => array(
							self::RETURN_TYPE_LINK   => __( 'Links', $this->l10n_domain),
							self::RETURN_TYPE_OBJECT => __( 'Objects', $this->l10n_domain ),
							self::RETURN_TYPE_ID     => __( 'Term IDs', $this->l10n_domain ),
						)
					) );
					?>
				</td>
			</tr>
			<tr class="field_option field_option_<?php echo $this->name; ?> taxonomy_input_size">
				<td class="label">
					<label><?php _e( 'Multi-Select Size' , $this->l10n_domain ); ?></label>
					<p class="description"><?php _e( 'The number of terms to show at once in a multi-select.', $this->l10n_domain ); ?></p>
				</td>
				<td>
					<?php 
						$this->parent->create_field( array(
							'type'    => 'select',
							'name'    => "fields[{$key}][" . self::FIELD_INPUT_SIZE . "]",
							'value'   => $field[ self::FIELD_INPUT_SIZE ],
							'choices' => array_combine( range( 3, 15, 2 ), range( 3, 15, 2 ) ),
						) );
					?>
				</td>
			</tr>
		<?php
	}
	
	/**
	 * (non-PHPdoc)
	 * @see acf_Field::update_value()
	 */
	public function update_value( $post_id, $field, $value ) {
		$this->set_field_defaults( $field );
		
		if( in_array( $field[ self::FIELD_SET_TERMS ], array( self::SET_TERMS_APPEND, self::SET_TERMS_OVERRIDE ) ) ) {
			$terms = array();
			foreach( (array) $value as $item ) {
				if( intval( $item ) > 0 )
					$terms[] = intval( $item );
				else
					$terms[] = strval( $item );
			}
			wp_set_object_terms( $post_id, $terms, $field[ self::FIELD_TAXONOMY ], $field[ self::FIELD_SET_TERMS ] == self::SET_TERMS_APPEND );
		}
		
		parent::update_value( $post_id, $field, $value );
	}
	
	/**
	 * Returns the values of the field
	 * 
	 * @see acf_Field::get_value()
	 * @param int $post_id
	 * @param array $field
	 * @return array
	 */
	public function get_value( $post_id, $field ) {
		$value = ( $field[ self::FIELD_USE_TERMS ] ) ?
			wp_get_object_terms( $post_id, $field[ self::FIELD_TAXONOMY ], array( 'fields' => 'ids' ) ) :
			parent::get_value( $post_id, $field );
		$value = is_array( $value ) ? $value : array();
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
		$this->set_field_defaults( $field );

		$value = parent::get_value_for_api( $post_id, $field );
		$value = is_array( $value ) ? $value : array();
		
		$terms = array();
		foreach( $value as $term_id ) {
			$term_id = intval( $term_id );
			switch( $field[ self::FIELD_RETURN_TYPE ] ) {
				case self::RETURN_TYPE_ID:
					$terms[] = $term_id;
					break;
				case self::RETURN_TYPE_OBJECT:
					$terms[] = get_term( $term_id, $field[ self::FIELD_TAXONOMY ] );
					break;
				case self::RETURN_TYPE_LINK:
					$term = get_term( $term_id, $field[ self::FIELD_TAXONOMY ] );
					$terms[] = sprintf(
						'<a href="%1$s" rel="tag">%2$s</a>',
						esc_attr( get_term_link( $term, $field[ self::FIELD_TAXONOMY ] ) ),
						esc_html( $term->name )
					);
					break;
			}
		}
		
		switch( $field[ self::FIELD_RETURN_TYPE ] ) {
			case self::RETURN_TYPE_ID:
			case self::RETURN_TYPE_OBJECT:
				return $terms;
			case self::RETURN_TYPE_LINK:
				//Allow plugins to modify
				$terms = apply_filters( "term_links-{$field[ self::FIELD_TAXONOMY ]}", $terms );
				return implode( '', $terms );
		}
		return false;
	}
}

endif; //class_exists 'ACF_Taxonomy_Field'

if( !class_exists( 'ACF_Walker_Taxonomy_Field_Checklist' ) ) :

class ACF_Walker_Taxonomy_Field_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	private $field;

	function __construct( $field ) {
		$this->field = $field;
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth, $args, $id = 0 ) {
		extract($args);

		if ( empty( $taxonomy ) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = $this->field[ ACF_Taxonomy_Field::FIELD_NAME ];

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}-{$name}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '-' . $name . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

endif; //class_exists 'ACF_Walker_Taxonomy_Field_Checklist'


if( !class_exists( 'ACF_Walker_Taxonomy_Field_List' ) ) :

class ACF_Walker_Taxonomy_Field_List extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	private $field;

	function __construct( $field ) {
		$this->field = $field;
	}
	
	function start_el( &$output, $object, $depth, $args, $current_object_id = 0 ) {
		$output .= '<option value="' . esc_attr( $object->term_id ) . '" ' . selected( in_array( (int) $object->term_id, $this->field[ ACF_Taxonomy_Field::FIELD_VALUE ] ), true, false ) . '>' . str_repeat( '&nbsp;', $depth * 3 ) . esc_attr( $object->name ) . '</option>';
	}
}
endif; //class_exists 'ACF_Walker_Taxonomy_Field_List'


if( !class_exists( 'ACF_Taxonomy_Field_Helper' ) ) :

/**
 * Advanced Custom Fields - Taxonomy Field Helper
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
		
		add_action( 'init', array( &$this, 'register_field' ),  5, 0 );
		add_action( 'init', array( &$this, 'load_textdomain' ), 2, 0 );
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