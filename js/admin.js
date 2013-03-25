jQuery(function($) {

	//$("select.taxonomy-field").multiselect().multiselectfilter();

	// http://www.advancedcustomfields.com/resources/getting-started/adding-custom-javascript-jquery-for-fields/
	$(document).on('acf/setup_fields', function(e, div){
 
		// div is the element with new html.
		// on first load, this is the $('#poststuff')
		// on adding a repeater row, this is the tr

		$(div).find('.taxonomy-field').each(function(){

			$(this).filter("select").multiselect().multiselectfilter();
			
		});

	});


});