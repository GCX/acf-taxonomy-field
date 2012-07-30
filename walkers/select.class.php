<?php
class ACF_Taxonomy_Field_Select extends ACF_Taxonomy_Field_Walker {
	
	
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .=
			'<select ' .
			'name="' . $this->field[ ACF_Taxonomy_Field::FIELD_NAME ] . '"';
/*		<select name="<?php echo $field[ self::FIELD_NAME ]; ?>[]" id="<?php echo $field[ self::FIELD_NAME ]; ?>" class="<?php echo $field[ self::FIELD_CLASS ]; ?>" <?php echo ( $field[ self::FIELD_INPUT_TYPE ] == self::INPUT_TYPE_MULTISELECT ) ? 'multiple="multiple" size="' . $field[ self::FIELD_INPUT_SIZE ] . '"' : '';>'*/
	}
	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		
	}
	
	public function start_el( &$output, $object, $depth, $args, $current_object_id = 0 ) {
		$output .= '<option value="' . esc_attr( $object->term_id ) . '" ' . selected( in_array( (int) $object->term_id, $this->field[ ACF_Taxonomy_Field::FIELD_VALUE ] ), true, false ) . '>' . str_repeat( '&nbsp;', $depth * 3 ) . esc_attr( $object->name ) . '</option>';
	}
}
