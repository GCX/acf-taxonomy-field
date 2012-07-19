Advanced Custom Fields - Taxonomy Field add-on
==============================================

Adds a Taxonomy Field to Advanced Custom Fields. Select one or more taxonomy terms and assign them to the post.

Description
-----------

This is an add-on for the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/)
WordPress plugin and will not provide any functionality to WordPress unless Advanced Custom Fields is installed
and activated.

The taxonomy field provides a select, multi-select or checkboxes of taxonomy terms (categories, tags, custom taxonomies...)
and the ability to map the selected terms to the post. The post type must support the taxonomy for the mapping to work.
The taxonomy field currently does not provide the ability to add new terms to a taxonomy. The return type of the `get_value()`
api can be changed in the field settings.

### Source Repository on GitHub
https://github.com/GCX/acf-taxonomy-field

### Bugs, Questions or Suggestions
https://github.com/GCX/acf-taxonomy-field/issues

Installation
------------

The Taxonomy Field plugin can be used as a WordPress plugin or included in other plugins or themes.
There is no need to call the Advanced Custom Fields `register_field()` method for this field.

* WordPress plugin
	1. Download the plugin and extract it to `/wp-content/plugins/` directory.
	2. Activate the plugin through the `Plugins` menu in WordPress.
* Added to Theme or Plugin
	1. Download the plugin and extract it to your theme or plugin directory.
	2. Include the `taxonomy-field.php` file in you theme's `functions.php` or plugin file.  
	   `include_once( rtrim( dirname( __FILE__ ), '/' ) . '/acf-taxonomy-field/taxonomy-field.php' );`

Frequently Asked Questions
--------------------------

### I've activated the plugin, but nothing happens!

Make sure you have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and
activated. This is not a standalone plugin for WordPress, it only adds additional functionality to Advanced Custom Fields.

Todo
----
* Add ability to add new terms to a taxonomy
* Add more term selection methods (token input).
