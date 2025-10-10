<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_Revisions {

	static $prefix;
	static $mod         = 'revisions_';
	static $title       = 'Revisions';
	static $description = "Enables you to manage Oxygen's revisions.";
	static $link        = 'https://oxyplugins.com/doc/revisions/';

	static function init( $prefix ) {

		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );
		add_action( self::$prefix . 'register_options', array( __CLASS__, 'mod_register_options' ) );
		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		add_action( self::$prefix . self::$mod . 'form_options', array( __CLASS__, 'mod_form_options' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 11, 3 );

	}

	static function save_post( $post_id, $post, $thirdparam = false ) {

		global $wpdb;

		$table = _get_meta_table( 'post' );

		$query = "SELECT count(`meta_id`) FROM $table";
		// Add passed conditions to query.
		$where   = array();
		$where[] = $wpdb->prepare( 'meta_key = %s', 'ct_builder_shortcodes_revisions' );
		$where[] = $wpdb->prepare( 'post_id = %s', $post_id );
		$query  .= ' WHERE ' . implode( ' AND ', $where );
		$count   = $wpdb->get_var( $query );
		$keep    = get_option( self::$prefix . self::$mod . 'max', 99 );

		$limit = $count - $keep;

		if ( $limit <= 0 ) {
			return;
		}

		$query   = "delete FROM $table";
		$where   = array();
		$where[] = $wpdb->prepare( 'meta_key = %s', 'ct_builder_shortcodes_revisions' );
		$where[] = $wpdb->prepare( 'post_id = %s', $post_id );
		$query  .= ' WHERE ' . implode( ' AND ', $where );
		$query  .= ' LIMIT ' . $limit;

		$wpdb->query( $query );

		$query   = "delete FROM $table";
		$where   = array();
		$where[] = $wpdb->prepare( 'meta_key = %s', 'ct_builder_shortcodes_revisions_dates' );
		$where[] = $wpdb->prepare( 'post_id = %s', $post_id );
		$query  .= ' WHERE ' . implode( ' AND ', $where );
		$query  .= ' LIMIT ' . $limit;

		$wpdb->query( $query );

	}

	static function mod_register_options() {
		add_option( self::$prefix . self::$mod . 'max', 99 );
		register_setting( self::$prefix . 'settings', self::$prefix . self::$mod . 'max', array( __CLASS__, 'sanitize_max' ) );
	}


	static function sanitize_max( $max ) {

		if ( is_numeric( $max ) ) {
			return intval( $max );
		}

		return 99; // default.
	}


	static function mod_form_options() {
		?>

		<button type="button" class="collapsible"></button>
		<div class="module-settings">
			<div class="module-settings-wrap">
				<div>
					<?php _e( 'Number of latest revisions to keep per post' ); ?>: 
					<input id="<?php echo self::$prefix . self::$mod; ?>max" name="<?php echo self::$prefix . self::$mod; ?>max" type="text" value="<?php echo get_option( self::$prefix . self::$mod . 'max' ); ?>" />
				</div>
			</div>
		</div>

		<?php
	}
}

Oxy_Toolbox_Revisions::init( self::PREFIX );
