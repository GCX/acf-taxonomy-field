=== Advanced Custom Fields - Taxonomy Field add-on ===
Contributors: Omicron7
Tags: acf, acf add-on, taxonomy, custom field, taxonomy field
Requires at least: 3.2
Tested up to: 3.4.1
Stable tag: 1.4

Adds a Taxonomy Field to Advanced Custom Fields. Select one or more taxonomy terms and assign them to the post.

== Description ==

This is an add-on for the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/)
WordPress plugin and will not provide any functionality to WordPress unless Advanced Custom Fields is installed
and activated.

The taxonomy field provides a select, multi-select or checkboxes of taxonomy terms (categories, tags, custom taxonomies...)
and the ability to map the selected terms to the post. The post type must support the taxonomy for the mapping to work.
The taxonomy field currently does not provide the ability to add new terms to a taxonomy. The return type of the `get_value()`
api can be changed in the field settings.

= Source Repository on GitHub =
https://github.com/GCX/acf-taxonomy-field

= Bugs, Questions or Suggestions =
https://github.com/GCX/acf-taxonomy-field/issues

= Todo =
* Add ability to add new terms to a taxonomy
* Add more term selection methods (checkboxes, token input).

== Installation ==

The Taxonomy Field plugin can be used as WordPress plugin or included in other plugins or themes.
There is no need to call the Advanced Custom Fields `register_field()` method for this field.

* WordPress plugin
	1. Download the plugin and extract it to `/wp-content/plugins/` directory.
	2. Activate the plugin through the `Plugins` menu in WordPress.
* Added to Theme or Plugin
	1. Download the plugin and extract it to your theme or plugin directory.
	2. Include the `taxonomy-field.php` file in you theme's `functions.php` or plugin file.  
	   `include_once( rtrim( dirname( __FILE__ ), '/' ) . '/acf-taxonomy-field/taxonomy-field.php' );`

== Frequently Asked Questions ==

= I've activated the plugin, but nothing happens! =

Make sure you have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and
activated. This is not a standalone plugin for WordPress, it only adds additional functionality to Advanced Custom Fields.

== Screenshots ==

1. Taxonomy Field.
2. Select the taxonomy to show.
3. Adding a term to a page.

== Changelog ==

= 1.4 =
* Added option to use the terms assigned to the post as the field value.

= 1.3.1 =
* Fixed undefined class constant FIELD_NAME issue. Props dmeehan1968

= 1.3 =
* Fixed issue where ACF value was incorrect when setting terms on a post. Props dmeehan1968
* Fixed issue with repeater returning the same value for every row when Set Post Terms is enabled. Props XedinUnknown
* Added indentation to hierarchical taxonomies in the select or multiselect. Props dmeehan1968
* Added the ability to append or override terms on a post. Props dmeehan1968
* Removed unused code and improved coding practices by using constants.
* Updated localizations

= 1.2 =
* Fixed issue Taxonomy Field not working in a Repeater Field. Props markSal
* Fixed a bug which caused wrong options to be shown in the metabox. Props FunkyM
* Fixed an additional issue with URL generation on Windows hosts.
* Added hierarchical checkboxes input type similar to builting WordPress taxonomy chooser. Props FunkyM
* Added option to choose link, object or ID as return value for field API calls. Props FunkyM
* Updated localizations

= 1.1.1 =
* Fixed issue with path and URI generation on Windows hosts.
* Fixed missing and invalid argument notices for get_field() api call. Thanks Rahe for the patch.

= 1.1 =
* Improved get_value API call. Using `get_value()` now returns a string of term links. Similar to WordPress `get_the_term_list()`.
* Fixed an issue with terms not being pre-selected when editing a post.
* Fixed a localization variable name typo.

= 1.0 =
* Initial Release