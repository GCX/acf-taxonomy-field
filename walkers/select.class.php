<?php
class ACF_Taxonomy_Field_Select extends ACF_Taxonomy_Field_Walker {
	function start_el( &$output, $object, $depth, $args, $current_object_id = 0 ) {
		$output .= '<option value="' . esc_attr( $object->term_id ) . '" ' . selected( in_array( (int) $object->term_id, $this->field[ ACF_Taxonomy_Field::FIELD_VALUE ] ), true, false ) . '>' . str_repeat( '&nbsp;', $depth * 3 ) . esc_attr( $object->name ) . '</option>';
	}
}
