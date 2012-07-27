<?php
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
	 * This method must be called before ACF does init
	 */
	public function register_field() {
		if( function_exists( 'register_field' ) )
			register_field( 'ACF_Taxonomy_Field', dirname( __FILE__ ) . '/taxonomy-field.php' );
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
