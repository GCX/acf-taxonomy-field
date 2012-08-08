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
//Only register the autoload function if the function does not exist yet
if( !function_exists( 'acf_taxonomy_field_autoloader' ) ) {
	/**
	 * Includes classes are they are needed
	 * @param string $classname Class name autoload is searching for
	 */
	function acf_taxonomy_field_autoloader( $classname ) {
		//List of classes and their location
		$classes = array(
			'ACF_Taxonomy_Field'             => '/taxonomy-field.class.php',
			'ACF_Taxonomy_Field_Helper'      => '/taxonomy-field-helper.class.php',
			'ACF_Taxonomy_Field_Walker'      => '/taxonomy-field-walker.class.php',
			'ACF_Taxonomy_Field_Checkbox'    => '/walkers/checkbox.class.php',
			'ACF_Taxonomy_Field_Select'      => '/walkers/select.class.php',
		);
		
		if( array_key_exists( $classname, $classes ) )
			require_once( dirname( __FILE__ ) . $classes[ $classname ] );
	}
	
	//Register the autoload function with SPL
	spl_autoload_register( 'acf_taxonomy_field_autoloader' );
}

//Instantiate the Taxonomy Field helper
ACF_Taxonomy_Field_Helper::singleton();
