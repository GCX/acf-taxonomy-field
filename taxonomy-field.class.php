<?php
/**
 * Advanced Custom Fields - Taxonomy Field add-on
 *
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
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
						'walker'       => new ACF_Taxonomy_Field_Select( $field ),
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
								'walker'        => new ACF_Taxonomy_Field_Select( $field ),
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
	 * @return mixed  
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
