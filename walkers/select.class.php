<?php
class ACF_Taxonomy_Field_Select extends ACF_Taxonomy_Field_Walker {
	
	
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= sprintf(
			'<select name="%1$s" id="%2$s" class="%3$s">',
			esc_attr( $this->field[ ACF_Taxonomy_Field::FIELD_NAME ] . '[]' ),
			esc_attr( $this->field[ ACF_Taxonomy_Field::FIELD_NAME ] ),
			esc_attr( $this->field[ ACF_Taxonomy_Field::FIELD_CLASS ] )
		);
	}
	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '</select>';
	}
	
	public function start_el( &$output, $object, $depth, $args, $current_object_id = 0 ) {
		$output .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $object->term_id ),
			selected( in_array( (int) $object->term_id, $this->field[ ACF_Taxonomy_Field::FIELD_VALUE ] ), true, false ),
			str_repeat( '&nbsp;', $depth * 3 ) . esc_html( $object->name )
		);
	}
}
