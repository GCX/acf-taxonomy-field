<?php
/**
 * Base walker class for all Input Types in ACF Taxonomy Field.
 * 
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 */
class ACF_Taxonomy_Field_Walker extends Walker {
	protected $field;

	public function __construct( $field ) {
		$this->field     = $field;
		$this->tree_type = 'category';
		$this->db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	}
}
