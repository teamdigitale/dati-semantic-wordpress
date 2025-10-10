<?php
/*
Plugin Name: OxyExtras
Description: Component Library for Oxygen.
Version: 1.4.7
Author: OxyExtras
Author URI: https://oxyextras.com
*/


if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Oxy_Extras_Plugin_Updater' ) ) {
	// load our custom updater.
	include dirname( __FILE__ ) . '/includes/oxy-extras-updater.php';
}

require_once 'includes/oxy-extras-license.php';


class OxyExtrasPlugin {

	const PREFIX    = 'oxy_extras_';
	const TITLE     = 'OxyExtras';
	const VERSION   = '1.4.7';
	const STORE_URL = 'https://oxyextras.com';
	const ITEM_ID   = 240;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'oxy_extras_init' ) );
		OxyExtrasLicense::init( self::PREFIX, self::TITLE, self::STORE_URL, self::ITEM_ID );

		add_action( 'activate_' . plugin_basename( __FILE__ ), array( __CLASS__, 'activate' ), 10, 2 );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 11 );
		add_action( 'admin_init', array( __CLASS__, 'plugin_updater' ), 0 );

		/* admin setting styles */
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_settings_styles'] );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ), 11 );

		add_filter( 'plugin_action_links_' . basename( __DIR__ ) . '/' . basename( __FILE__ ), array( __CLASS__, 'settings_link' ) );

		register_uninstall_hook( plugin_basename( __FILE__ ) , array( __CLASS__, 'clean_db_on_uninstall' ) );

	}

	public static function scripts() {
		if ( ! defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_enqueue_script( self::PREFIX . 'builder', plugins_url( 'includes/js/builder.js', __FILE__ ), array( 'ct-angular-main' ), self::VERSION, true );

		if ( defined( 'OXYGEN_IFRAME' ) ) {
			wp_enqueue_script( self::PREFIX . 'script', plugins_url( 'includes/js/script.js', __FILE__ ), array( 'ct-angular-main' ), self::VERSION, true );
			
			
		}
	}

	/* Settings page styles */
	public static function admin_settings_styles( $hook_suffix ) {
		wp_enqueue_style( 'x_admin_css', plugins_url( 'includes/css/admin.css', __FILE__ ), false, '1.0.0' );
	}

	public static function activate( $plugin ) {
		if ( ! defined( 'CT_FW_PATH' ) ) {
			die( '<p>\'Oxygen builder\' must be installed and activated, in order to activate \'' . self::TITLE . '\'</p>' );
		}
	}

	public static function admin_menu() {

		$users_access_list = get_option( 'oxygen_vsb_options_users_access_list', array() );

		if ( isset( $users_access_list[get_current_user_id()] ) && 'true' !== $users_access_list[get_current_user_id()][0] ) {
			return;
		}

		global $menu;
		$menu_exists = false;

		foreach ( $menu as $item ) {
			if ( array_search( 'ct_dashboard_page', $item ) !== false ) {
				$menu_exists = true;
				break;
			}
		}

		if ( $menu_exists === false ) {
			add_menu_page( self::TITLE, self::TITLE, 'manage_options', self::PREFIX . 'menu', array( __CLASS__, 'menu_item' ) );
		} else {
			add_submenu_page( 'ct_dashboard_page', self::TITLE, self::TITLE, 'manage_options', self::PREFIX . 'menu', array( __CLASS__, 'menu_item' ) );
		}
	}

	public static function menu_item() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false;
		?>
	<div class="wrap oxyextras-settings">
		<h2 class="nav-tab-wrapper">
		<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=settings" class="nav-tab<?php echo ( $tab === false || $tab == 'settings' ) ? ' nav-tab-active' : ''; ?>">Settings</a>
		<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=license" class="nav-tab<?php echo $tab == 'license' ? ' nav-tab-active' : ''; ?>">License</a>
		<a href="?page=<?php echo self::PREFIX . 'menu'; ?>&amp;tab=changelog" class="nav-tab<?php echo $tab == 'changelog' ? ' nav-tab-active' : ''; ?>">Changelog</a>
		</h2>

		<?php
		if ( $tab === 'license' ) {
			OxyExtrasLicense::license_page();
		} elseif ( 'changelog' === $tab ) {
			self::changelog_page();
		} else {
			self::settings_page();
		}
		?>
	</div>
		<?php
	}

	public static function settings_page() {
		?>
	<div style="display: flex; justify-content: space-between; align-items: center; max-width: calc(100% - 278px);">
		<h2><?php echo self::TITLE . ' ' . __( 'General Settings' ); ?></h2>
		<p>ONLY ENABLE THE COMPONENTS YOU WANT TO USE. <a href="https://oxyextras.com/shortcode-depth-issue/" target="_blank">LEARN MORE</a></p>
	</div>
	<div class="form-plugin-links" style="display: flex;">
	  <form method="post" action="options.php" style="width: 100%;">

		<?php settings_fields( self::PREFIX . 'settings' ); ?>
		<table class="wp-list-table widefat plugins">
		  <thead>
		  <tr>
			<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Activate All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary">Enable All</th><td></td></tr>
		  </thead>
		  <tbody>
			<?php do_action( self::PREFIX . 'form_options' ); ?>
		  </tbody>
		  <thead>
		  <tr>
			<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Activate All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary">Enable All</th><td></td></tr>
		  </thead>
		</table>
		
		<?php submit_button(); ?>
	  </form>
	  <div class="plugin-links" style="margin-left: 40px; width: 300px;">
		  <ul>
			<li style="margin-bottom: 20px;">OxyExtras v<?php echo self::VERSION; ?></li>
			<li style="margin-bottom: 20px;"><a style="display: flex; align-items: center; line-height: 1;" target="_blank" href="https://www.facebook.com/groups/oxyextras/"><svg style="width: 14px; height: 14px; margin-right: 5px;" width="14" height="14" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook-f" class="svg-inline--fa fa-facebook-f fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg> Facebook Group</a></li>
			<li><a style="display: flex; align-items: center; line-height: 1;" target="_blank" href="https://oxyextras.com/support/"><svg style="width: 14px; height: 14px; margin-right: 5px;" width="14" height="14" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="life-ring" class="svg-inline--fa fa-life-ring fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm168.766 113.176l-62.885 62.885a128.711 128.711 0 0 0-33.941-33.941l62.885-62.885a217.323 217.323 0 0 1 33.941 33.941zM256 352c-52.935 0-96-43.065-96-96s43.065-96 96-96 96 43.065 96 96-43.065 96-96 96zM363.952 68.853l-66.14 66.14c-26.99-9.325-56.618-9.33-83.624 0l-66.139-66.14c66.716-38.524 149.23-38.499 215.903 0zM121.176 87.234l62.885 62.885a128.711 128.711 0 0 0-33.941 33.941l-62.885-62.885a217.323 217.323 0 0 1 33.941-33.941zm-52.323 60.814l66.139 66.14c-9.325 26.99-9.33 56.618 0 83.624l-66.139 66.14c-38.523-66.715-38.5-149.229 0-215.904zm18.381 242.776l62.885-62.885a128.711 128.711 0 0 0 33.941 33.941l-62.885 62.885a217.366 217.366 0 0 1-33.941-33.941zm60.814 52.323l66.139-66.14c26.99 9.325 56.618 9.33 83.624 0l66.14 66.14c-66.716 38.524-149.23 38.499-215.903 0zm242.776-18.381l-62.885-62.885a128.711 128.711 0 0 0 33.941-33.941l62.885 62.885a217.323 217.323 0 0 1-33.941 33.941zm52.323-60.814l-66.14-66.14c9.325-26.99 9.33-56.618 0-83.624l66.14-66.14c38.523 66.715 38.5 149.229 0 215.904z"></path></svg> Support</a></li>
		</ul>
	  </div>
	</div>
		<?php
	}

	public static function changelog_page() {
		?>
		<h2>OxyExtras Changelog</h2>
		<div style="background-color: #fff; padding: 15px 20px; border: 1px solid #ccd0d4">
			<h3 style="margin-top: 0.5em;">1.4.6 ( Mar 27, 2024 )</h3>
			<ul>
				<li>[Pro Accordion] - Added an option to open the first accordion item when being used inside a repeater.</li>
				<li>[Carousel Builder] - Sync functionality can now be used with carousels inside repeaters.</li>
				<li>[Gutenberg Reusable Block] - Fixed deprecation notice.</li>
			</ul>
			
			<h3>1.4.5 ( Mar 13, 2024 )</h3>
			<ul>
				<li>[General] - Security improvement.</li>
			</ul>
			
			<h3>1.4.4 ( Mar 12, 2024 )</h3>
			<ul>
				<li>[Table of Contents] - Added option to use heading text as heading IDs inside content.</li>
				<li>[Slide Menu] - Added option to auto close menu if hashlink menu item clicked.</li>
				<li>[Content Timeline] - Now supports being inside nested repeaters.</li>
				<li>[Social Share Buttons] - Changed defaults to use X icon.</li>
				<li>[Carousel Builder] - Fixed issue with with carousel's preview mode showing </li>error in builder if autoplay setting has no value.</li>
				<li>[Reading time] - Fixed issue where default words per minute value wasn't being used inside repeater when inside builder.</li>
				<li>[General] - Security patch.</li>
			</ul>
			
			<h3>1.4.3 ( May 31, 2022 )</h3>
			<ul>
				<li>[General] - Small update to all icons to better match Oxygen v4 UI.</li>
				<li>[Carousel Builder] - Prevent Repeater divs wrapping onto multiple lines inside the builder.</li>
				<li>[Read More] - Added seperate control for "transparent" gradient color.</li>
			</ul>
			
			<h3>1.4.2 ( May 07, 2022 )</h3>
			<ul>
				<li>[Pro Media Player] Fixed SVG controls in the media player not showing up.</li>
			</ul>

			<h3>1.4.1 ( May 06, 2022 )</h3>
			<ul>
				<li>[Horizontal Slide Menu] - New component for horizontal sliding menus (mmenu.js).</li>
				<li>[Interactive Cursor] - New component for adding a custom cursor that can interact with other elements.</li>
				<li>[General] - Added compatibility for Oxygen v4's JSON for all nestable components.</li>
				<li>[General] - Added compatibility for use with Oxygen v4's new Repeater changes.</li>
				<li>[General] - Multiple elements that use dynamic data now support HTML / WYSIWYG.</li>
				<li>[General] - Multiple elements now have specific aria-label controls for buttons.</li>
				<li>[General] - Component options now cleared from database after uninstalling.</li>
				<li>[OffCanvas] - Better accessibility incl. support for "inert", will add aria-controls to click trigger etc.</li>
				<li>[OffCanvas] - Will now automatically hide in the builder if not editing the template that contains it.</li>
				<li>[Lightbox] - Added option to allow users to swipe/flick through grouped slides.</li>
				<li>[Lightbox] - Added option to infinite loop grouped slides.</li>
				<li>[Lightbox] - Added option to include thumbnail gallery if using manual links with grouped images.</li>
				<li>[Lightbox] - "Returning focus" option now supports having multiple click triggers on page.</li>
				<li>[Content Timeline] - Added "meta content inner" for more layout possibilities.</li>
				<li>[Carousel Builder] - Now possible to use sub fields from inside Metabox group fields for image galleries.</li>
				<li>[Carousel Builder] - Added the ability to enable/disable SRCSET for images in galleries.</li>
				<li>[Mega Menu] - Added flyout menu specific style controls for easier styling of mobile menu.</li>
				<li>[Mega Menu] - Now supports anchor link (scroll to) behaviour in menu links that have no dropdown.</li>
				<li>[Countdown] - Added UTC offsets to timezone option.</li>
				<li>[Table of Contents] - HTML tag can now be changed (div, nav or custom).</li>
				<li>[Popovers] - Added support for use with dynamic content added by infinite scroller.</li>
				<li>[Pro Accordion] - Added "inherit" as default button color to prevent iOS Safari showing the blue color as default if no link color has yet been set.</li>
				<li>[Burger Trigger] - Hover opacity on burger lines now customizable.</li>
				<li>[Lottie] - Lottie version bump (addresses issue where some Lottie files would flicker in Safari).</li>
				<li>[Carousel Builder] - Fixed issue with arrow placement where "auto" wasn't accepted as a value.</li>
				<li>[Read More] - Fixed an issue with the content sometimes not being visible inside Repeaters (in the builder).</li>
				<li>[Slide Menu] - Fixed an issue causing menu ID not being fetched with dynamic data.</li>
				<li>[Content Timeline] - Fix an issue where icon size control wouldn't be applied.</li>
				<li>[Pro Media Player] - Fixed an issue with full screen mode not taking up the whole viewport in Safari.</li>
				<li>[Lightbox] - Added a fix for AutomaticCSS users where a min-height on the body tag was being applied inside of lightbox iframe.</li>
			</ul>

			<h3>1.4.0 ( December 08, 2021 )</h3>
			<ul>
				<li>[Content Timeline] - Added an option to change the scroll position for classes to be added.</li>
				<li>[Content Timeline] - Added more style controls for marker.</li>
				<li>[Content Timeline] - Performance improvements for the timeline scroll animation.</li>
				<li>[Mega menu] - Added an option to have the mega menu close whenever hash links are clicked inside a dropdown.</li>
				<li>[Carousel] - Fixed the issue with the carousel not initializing if the script is minified/combined.</li>
			</ul>
			
			<h3>1.3.9 ( December 06, 2021 )</h3>
			<ul>
				<li>[Content Timeline] - New component for adding animated vertical timelines.</li>
				<li>[Carousel] - Added patch to fix Flickity's resizing issues with iOS 15.</li>
				<li>[Carousel] - Added option to choose cell selector for repeater carousels (allows support for the case when duplicate IDs are removed from repeaters).</li>
				<li>[Carousel] - Added "random order" option to all galleries.</li>
				<li>[Carousel] - Added setting to disable Flickity's "imagesLoaded" option.</li>
				<li>[Carousel] - Arrows/dots now both disabled automatically if there is only one slide / group of cells.</li>
				<li>[Pro Media Player] - Added new control for supporting dynamic data to populate the poster images.</li>
				<li>[Popovers] - Added support for use within WP Grid Builder AJAX added content.</li>
				<li>[Lightbox] - Added legacy support for IE11 browser.</li>
				<li>[Fluent Form] - More styling control for radio buttons that use images.</li>
				<li>[Mega Menu] - Added option to disable the "click for dropdown to remain open" (hides the hashlinks also).</li>
				<li>[Mega Menu] - Prevents mobile styles from being applied to desktop view if user rotates device with mobile menu still open.</li>
				<li>[Read More] - Fixed issue with Galaxy A21s devices miscalculating the content height and not always expanding.</li>
				<li>[Table of Contents] Prefix for auto-IDs added to content headings, 'toc-' can now be customized.</li>
				<li>[Table of Contents] - Fixed heading icon links not having the the correct URL when used on nested sub pages.</li>
				<li>[Mini Cart] - Added option to disable buttons and more specific style options.</li>
				<li>[Cart counter] - Added option to open/close on hover as well as click.</li>
				<li>[Reading Progress bars] - Added better support for when using multiple progress bars.</li>
				<li>[Countdown] - Fixed couple of bugs with plural/single and countdown remaining invisible.</li>
				<li>[Social Share] - Fixed link issue with Facebook icon linking other elements.</li>
				<li>[Off Canvas] - Tweaked the staggered animation to prevent rare issue where user could open offcanvas before animations had time to reset.</li>
			</ul>
			
			<h3>1.3.8 ( August 17, 2021 )</h3>
			<ul>
				<li>[Countdown Timer] - Now accepts both YYYY/MM/DD and YYYY-MM-DD formats.</li>
				<li>[Hotspots] - Prevent the SSL error being displayed in cases where the SSL certificate could not be verified on the site.</li>
				<li>[Carousel Builder] - Hashlink option now will override the initial cell setting (to allow for hashlinking between pages).</li>
				<li>[Dynamic Tabs] - Fixed layout issue with Meta Box clone fields.</li>
				<li>[Read More] - Better support for use inside Oxygen's tabs component (no flicker).</li>
			</ul>

			<h3>1.3.7 ( July 26, 2021 )</h3>
			<ul>
				<li>[Countdown Timer] - New component for countdown timer, 'evergreen, recurring, fixed dates'.</li>
				<li>[Header Search] - Added option to prevent scrolling when search is open.</li>
				<li>[Header Search] - Added option to require input before submitting.</li>
				<li>[Lightbox] - Added option to prepend the lightbox to body element instead of inside the component (useful if needing the lightbox component inside carousels & other transformed containers).</li>
				<li>[Lottie] - Can now use any dynamic data as Lottie JSON URL.</li>
				<li>[Back to Top] - Added option to make it keyboard focusable.</li>
				<li>[Mega Menu] - Added current menu ancestor styles to dropdown links.</li>
				<li>[Mega Menu] - Edit - removed auto-scroll back up to menu if dropdown is open.</li>
				<li>[Infinite scroller] - Custom mode now allows for defining a custom container for the content.</li>
			</ul>
			
			<h3>1.3.6 ( May 30, 2021 )</h3>
			<ul>
				<li>[Fluent Form] - Fixed the issue preventing default CSS to be visible inside the builder.</li>
				<li>[General] - Fixed the issue affecting WP Grid Builder layout where no facets were found on the page.</li>
			</ul>
			
			<h3>1.3.5 ( May 28, 2021 )</h3>
			<ul>
				<li>[Pro Accordion] – Added support for Metabox Term Meta &amp; formatting WYSIWYG fields.</li>
				<li>[Dynamic Tabs] – Added support for Metabox Term Meta &amp; formatting WYSIWYG fields.</li>
				<li>[Social Share] – Updated line network brand colors.</li>
				<li>[Social Share] – Added an option for popup behavior.</li>
				<li>[Preloader] – Added an option to close on user clicking.</li>
				<li>[Lightbox] – Now supports using the Read More component inside lightboxes.</li>
				<li>[Pro Media Player] – If inside carousel, now automatically ensures carousel always has the correct size for aspect ratio.</li>
				<li>[Popovers] – More styling controls.</li>
				<li>[MegaMenu] – Added current menu link typography for flyout menus.</li>
				<li>[Copyright Year] – Replaced ‘&amp;nbsp;’ with space to avoid the large visible gap.</li>
				<li>[Carousel] – Fixed the issue with not continuing autoplay after scrolling on mobile.</li>
				<li>[Pro Accordion] – Fixed the issue with global heading color overriding accordion header color.</li>
				<li>[General] – Multiple in-builder performance improvements.</li>
				<li>[General] – Out-of-the-box support for WPGridBuilder loaded content (carousel, tabs, accordion, read more, lightbox).</li>
			</ul>
			
			<h3>1.3.4 ( April 29, 2021 )</h3>
			<ul>
				<li>[Table of Contents] - New component for adding an automatic table of contents.</li>
				<li>[Pro Accordion] - Added an option to close all other accordion items within a container (not just sibling).</li>
				<li>[Pro Accordion] - Added support for Meta Box user fields and settings pages.</li>
				<li>[Dynamic Tabs] - Added support for Meta Box user fields and settings pages.</li>
				<li>[Carousel] - Added support for Meta Box user fields and settings pages.</li>
				<li>[Social Share] - Added a width setting to allow for equal width buttons.</li>
				<li>[Social Share] - Added 'Line' as a new network.</li>
				<li>[Lightbox] - Small close button now fully stylable.</li>
				<li>[Login Form] - Finer controls over element styles.</li>
				<li>[Pro Media Player] - Added option to load VimeJS direct from CDN.</li>
				<li>[Pro Media Player] - Fixed an issue causing some external mp4s not being able to be fetched.</li>
				<li>[Pro Accordion] - Fixed counter font-family not being applied.</li>
				<li>[Cart Counter] - Fixed the issue that was causing some styles not to be applied in Gutenberg.</li>
				<li>[Carousel] - Fixed the issue with an old version of Safari v9 (2015) not being draggable.</li>
				<li>[Carousel] - Fixed the issue causing navigation arrows to behave strangely when inside Repeaters.</li>
				<li>[Read More] - Fixed the issue with Read More sometimes not opening immediately if there is a lot of content.</li>
			</ul>
			
			<h3>1.3.3 ( April 07, 2021 )</h3>
			<ul>
				<li>[Dynamic Tabs] - Added Meta Box support (cloneable group fields).</li>
				<li>[Dynamic Tabs] - Now can use ACF option pages and ACF field from other pages.</li>
				<li>[Pro Accordion] - Added Meta Box support (cloneable group fields).</li>
				<li>[Carousel] - Added Meta Box support for galleries (image fields).</li>
				<li>[Carousel] - Added WooCommerce product images gallery.</li>
				<li>[Carousel] - Added object-fit control for changing how the images fit into the cells.</li>
				<li>[Carousel] - Carousel now completely hidden on frontend if gallery is empty or no gallery found.</li>
				<li>[Lightbox]- Now supports carousel builders inside "inline" lightboxes. </li>
				<li>[Fluent Form] - Added style controls for 'step' progress type.</li>
				<li>[Popovers] - Added option to be open by default, added transitions when moving placement.</li>
				<li>[Popovers] - More sensible defaults (fallback as "auto").</li>
				<li>[Infinite Scroller] - Added "page content" option for breaking up posts/pages via page breaks (i.e., layered linking).</li>
				<li>[Infinite Scroller] - Added support for using custom classes in Easy Posts (not just oxy-post).</li>
				<li>[Cart Counter] - Added option to hide counter if there are no cart items.</li>
				<li>[Slide Menu] - Menu can now be selected via dynamic data.</li>
				<li>[OffCanvas] - Allowed for including slide menu top-level items in stagger animation.</li>
				<li>[OffCanvas] - Fixed issue where preventing site scrolling wasn't working on iOS.</li>
				<li>[Author Box] - Allowed no content for the name prefix.</li>
				<li>[Social Share] - Fixed issue with icon background color overlapping the button when border radius added.</li>
				<li>[General] - Fixed a couple of typos, added icons and added more helper descriptions.</li>
			</ul>
			
			<h3>1.3.2 ( March 11, 2021 )</h3>
			<ul>
				<li>[Hotspots] – New component – Adds container for adding hotspots on images.</li>
				<li>[Popovers] – New component – To create individual hotspots, popovers, or tooltips for other elements.</li>
				<li>[Mega Menu] – Added support for use in custom headers (just needs header tag).</li>
				<li>[Mega Menu] – Added hover/active/focus border styling to dropdown links.</li>
				<li>[Social Share] – Added Xing as a new network.</li>
				<li>[Fluent Forms] – Added more style settings and slight UI change for fewer clicks.</li>
				<li>[Carousel Builder] – Added alignment for images inside the cells for galleries.</li>
				<li>[Carousel Builder] – Added full support for using carousels inside of Repeaters.</li>
				<li>[Carousel Builder] – ACF gallery data can now be from other pages or ACF options page.</li>
				<li>[Carousel Builder] – Draggable can now be disabled for Fade carousels.</li>
				<li>[Carousel Builder] – Added an option for the gallery images to preserve aspect ratio when in full screen mode.</li>
				<li>[Carousel Builder] – Fixed an issue with WooCommerce cell widths sometimes not being visible inside the builder.</li>
				<li>[Pro Accordion] – Now can be used inside Repeaters to create larger accordions using any dynamic data.</li>
				<li>[Pro Media Player] – VimeJS now comes packaged inside the plugin (no external CDN).</li>
				<li>[Lightbox] – Added support for being used in AJAX content.</li>
				<li>[Read More] – Added support for being used in AJAX content.</li>
				<li>[Dynamic Tabs] – Fixed an issue with some style changes not being immediately visible inside the builder.</li>
				<li>[Off Canvas] – No longer prevents Lottie animations from animating if being used directly as the click trigger.</li>
				<li>[Off Canvas] – Fixed an issue with smooth scroll (Oxygen v3.7+) not smooth for hashlinks inside Off Canvas.</li>
			</ul>
			
			<h3>1.3.1 ( February 11, 2021 )</h3>
			<ul>
				<li>[Dynamic Tabs] - Hash suffix now optional/customizable for creating custom hashlinks to individual tab content (page/#componentID-1-suffix, page/#componentID-2-suffix).</li>
				<li>[Dynamic Tabs] - Added support for using in reusable components.</li>
				<li>[Infinite Scroller] - Added "custom" option, so any containers can be used to pull in content from a next page by following any links.</li>
				<li>[Infinite Scroller] - Added option to retrigger scroll animations when new content loaded.</li>
				<li>[Carousel Builder] - Fixed issue "use as navigation" not syncing correctly.</li>
				<li>[Lottie] - Fixed issue with scroll offsets not calculating correctly when using more than two scroll animations.</li>
			</ul>
			
			<h3 style="margin-top: 0.5em;">1.3.0 ( February 09, 2021 )</h3>
			<ul>
				<li>[Dynamic Tabs] - New component for adding tabs populated by repeater fields. (accordion option for mobile).</li>
				<li>[Carousel Builder] - Added option to auto-calc cell width based on number of visible cells needed.</li>
				<li>[Carousel Builder] - Added option to resume autoplay X milliseconds after ending user interaction.</li>
				<li>[Carousel Builder] - Galleries now support image lazy loading.</li>
				<li>[Carousel Builder] - Added support for syncing carousels (2-way syncing).</li>
				<li>[Carousel Builder] - Added option to fade in carousel only after initializing is finished, to help prevent FOUC.</li>
				<li>[OffCanvas] - Added burger syncing for animating two separate burger triggers.</li>
				<li>[Infinite Scroller] - Now supports being used with Masonry / Isotope layouts (including when using filters).</li>
				<li>[Header Search] - Added support for use with Polylang.</li>
				<li>[Header Search] - Added option to add text to the search button.</li>
				<li>[Lightbox] - Manual mode now supports elements added to page dynamically (i.e., posts in infinite scroller, WPGridbuilder etc).</li>
				<li>[Mega Menu] - Added horizontal alignments & margins for more control over positioning of dropdown links.</li>
				<li>[Lightbox] - More animation options (can now be previewed inside the builder).</li>
				<li>[Lightbox] - Images no longer stretch inside the lightbox in the builder.</li>
				<li>[Lightbox] - Fixed the issue with inline lightboxes not scrollable on some iPads.</li>
				<li>[Carousel Builder] - Fixed the issue with background lazy loading needing image lazy loading also being enabled.</li>
				<li>[OffCanvas] - Fixed the conflict when using "prevent scroll" with push type offcanvas.</li>
			</ul>
			
			<h3>1.2.9 ( January 25, 2021 )</h3>
			<ul>
				<li>[Lightbox] - New component for visually building lightboxes, support for AJAX, iFrame, Inline, Video, Image Galleries or external links.</li>
				<li>[Carousel Builder] - Added support for background-image lazy loading.</li>
				<li>[Carousel Builder] - Added link option for lightbox support on galleries.</li>
				<li>[Carousel Builder] - Added caption support for galleries.</li>
				<li>[Carousel Builder] - Added option to retrigger AOS animations inside carousel.</li>
				<li>[Circular Progress] - Now can build pie chart style progress bars (using scale & new 'butt' ending controls).</li>
				<li>[Mega Menu] - Added mobile menu settings to allow for use with mobile sticky headers.</li>
				<li>[Mega Menu] - Added dynamic data option to dropdown links</li>
				<li>[Fluent Form] - Added "checkbox display" for preventing GDPR & T&Cs checkboxes being misaligned with other checkboxes.</li>
				<li>[Read More] - Added support for being used inside closed tabs.</li>
				<li>[General] - Minor UI cleanup, for more consistency across components.</li>
			</ul>
			
			<h3>1.2.8 ( December 22, 2020 )</h3>
			<ul>
				<li>[Off Canvas] – Added push type Offcanvas.</li>
				<li>[Mega Menu] – Added current page link color and typography controls.</li>
				<li>[Carousel Builder] – Disabling pause Autoplay on hover now supported for fade carousels.</li>
				<li>[Off Canvas] – Box-shadow now hidden when Offcanvas closed.</li>
				<li>[Mega Dropdown] – Issue fixed with links not clickable if no dropdown and when reveal on mouseover disabled.</li>
				<li>[Read More / Less] – Fixed issue with read more link not clickable in the latest Firefox.</li>
				<li>[Pro Accordion] – Fixed error if attempting to use Dynamic items without ACF Pro already being active.</li>
			</ul>
			
			<h3>1.2.7 ( December 16, 2020 )</h3>
			<ul>
				<li>[General] OxyExtras now respects Oxygen's Client Control. Admin menu item won't show for Edit Only roles and users. Also, only the elements specified under "Enable Elements" checkbox at Oxygen &gt; Client Control will appear in the editor for Edit Only users.</li>
				<li>[Lottie] JS now gets enqueued on init to prevent an error inside the builder when multiple lotties are used.</li>
			</ul>
			
			<h3>1.2.6 ( December 10, 2020 )</h3>
			<ul>
				<li>[Carousel Builder] – Lazy loading now available when using custom elements as cells.</li>
				<li>[Mega Menu] – Dropdown alignments &amp; positioning adjustable across screen sizes.</li>
				<li>[Mega Dropdown] – Link text can be removed if just wanting an icon.</li>
				<li>[Mega Dropdown] – Link URL can be disabled for mobile menu.</li>
				<li>[Mega Dropdown] – Dropdown can be set to be expanded by default when mobile menu opened.</li>
				<li>[Counter / Circular Progress] – Can now be used inside repeaters.</li>
				<li>[Burger Trigger] – Added control over touch events for touch screen devices.</li>
				<li>[Slide Menu] – Sub menu toggles now functional inside the builder.</li>
				<li>[General] – Fixed issue with components as reusable templates where styles weren’t added inside the builder.</li>
				<li>[General] – In-builder performance, no JS from active components loaded inside the builder unless added on that specific page.</li>
				<li>[Pro Media Player] – Fixed issue with audio playback not starting correctly in Safari</li>
				<li>[Mega Menu] – Fixed issue with long menu text preventing the click to toggle the containers on mobile.</li>
			</ul>
			
			<h3>1.2.5 ( December 01, 2020 )</h3>
			<ul>
				<li>[Mega Menu] – New component for adding mega menu style dropdowns in header builder.</li>
				<li>[General] – Slight JS adjustments to ensure full support for jQuery 3.5.1.</li>
				<li>[Fluent Form] – Added separate style controls for GDPR.</li>
				<li>[Social Share] – Customisable share URL’s and more control over the text in email title and body.</li>
				<li>[Slide Menu] – New “mega menu list” option for displaying columned menu lists inside Mega Menus.</li>
				<li>[Pro Media Player] – Removed aspect ratio setting for Vimeo (now automatic).</li>
				<li>[Carousel Builder] – Added option for auto cell grouping and percentage cell grouping.</li>
				<li>[Carousel Builder] – Carousel now horizontally scrollable inside builder while in edit mode.</li>
				<li>[Off Canvas] – Built-in support for linking offcanvas’ to ensure both offcanvas’ close together.</li>
				<li>[Off Canvas] – Auto-close when hashlink clicked is now optional.</li>
				<li>[Pro Media Player] – Fixed PHP warning “Undefined variable: vime_player_selector”.</li>
			</ul>

			<h3>1.2.4 ( November 11, 2020 )</h3>
			<ul>
				<li>[Carousel Builder] – Hotfix for making parallax work again.</li>
			</ul>

			<h3>1.2.3 ( November 11, 2020 )</h3>
			<ul>
				<li>[Carousel Builder] – No longer need to specify cell selector when using Repeaters.</li>
				<li>[Carousel Builder] – Added option to disable pause Auto Play On Hover.</li>
				<li>[Carousel Builder] – Performance improvements inside the builder. Preview mode works faster.</li>
				<li>[Carousel Builder] – Added “Prioritize property” option for Galleries for choosing either dynamic height or widths.</li>
				<li>[Pro Accordion] – Added support for ACF option pages and using fields from specific page IDs.</li>
				<li>[Pro Media Player] – Added loop option for videos.</li>
				<li>[Pro Media Player] – Fixed dynamic data button not working correctly for Audio URL field.</li>
				<li>[Carousel Builder] – Fixed incorrect description text, “image URL” replaced with “image ID” for ACF galleries.</li>
				<li>[Slide Menu] – Fixed issue with schema markup sometimes preventing the menu text being clickable.</li>
				<li>[Read More] – Fixed gradient sometimes remaining visible when fade gradient disabled.</li>
			</ul>
			
			<h3>1.2.2 ( November 04, 2020 )</h3>
			<ul>
				<li>[Carousel] – Added fade transition carousel type.</li>
				<li>[Carousel] – Added support for WP media library galleries.</li>
				<li>[Carousel] – Added option to remove pause-on-hover when using ticker mode.</li>
				<li>[Slide Menu] – Added current menu item styles and the ability to have the current menu item visible on page load.</li>
				<li>[Header Search] – Added expand form, slide reveal and accessibility controls options.</li>
				<li>[Read More] – Allow expanding inside builder for easier access to elements inside.</li>
				<li>[Read More] – Now dynamic. If content isn’t taller than the expanded height, read more button (and gradient) will automatically not be visible.</li>
				<li>[Read More] – Can now be used inside Repeaters without issue.</li>
				<li>[Read More] – Added icons for the read more link, a fade transition and more control over the gradient overlay.</li>
				<li>[Infinite Scroller] – Now dynamic. If not enough posts found for there to be a second page, infinite scroll won’t run (and read more button automatically hidden),</li>
				<li>[Alert Box] – Added click trigger and show/hide alert functions for programmatically triggering alerts.</li>
				<li>[Alert Box] – Added “header notice” alert type.</li>
				<li>[Adjacent Post] – Added alt tags to post images.</li>
				<li>[Accordion Pro] – Added option to turn off automatic sibling accordion item closing.</li>
				<li>[Pro Accordion] Fixed issue with accordion buttons being triggered too quickly when scrolling on iOS.</li>
				<li>[Slide Menu] – Fixed issue with hash links not triggering the sub menu.</li>
				<li>[Carousel] – Fixed issue with force equal heights being overridden by other CSS.</li>
				<li>[Carousel] – Fixed issue with high z-index on dots causing them to be visible over the top of modals.</li>
			</ul>
			
			<h3>1.2.1 ( October 26, 2020 )</h3>
			<ul>
				<li>[General optimization] – Less default CSS output for multiple components where possible.</li>
				<li>[General optimization] – Improved reliability/usability of some components inside the builder.</li>
				<li>[General optimization] – Added browser-performance setting to carousel / Off Canvas for smoother transforms.</li>
				<li>[Carousel Builder] – Added support for lazy loading images in cells when using Repeaters or Easy Posts.</li>
				<li>[Pro Accordion] – Dynamic mode will now show demo content inside builder if no ACF data found for each field (only in the builder for easier styling).</li>
				<li>[Pro Accordion] – Can now preview toggle animation and active style changes inside the builder by clicking the accordion header.</li>
				<li>[Burger Trigger] – Burger animation will now always be previewable inside the builder.</li>
			</ul>

			<h3>1.2.0 ( October 21, 2020 )</h3>
			<ul>
				<li>[Pro Accordion] – New component for adding dynamic accordions.</li>
				<li>[Circular Progress] – Drop Shadows &amp; Inner circle control added for more fancy styling.</li>
				<li>[Pro Media Player] – Added custom poster image option when using YouTube &amp; Vimeo.</li>
				<li>[Pro Media Player] – Added new UI layout with custom play / pause icons.</li>
				<li>[Pro Media Player] – Added autoplay, autopause &amp; more Vimeo options.</li>
				<li>[Carousel Builder] – ACF gallery image now automatically the same height.</li>
				<li>[Carousel Builder] – Add option to force equal height for cells.</li>
				<li>[Carousel Builder] – Fixed issue when heights overriding fullscreen mode.</li>
				<li>[Preloader] – Fixed pre-loader being visible if used on pages editable in Gutenberg.</li>
				<li>[Author Box] – Fixed issue with website link not displaying.</li>
				<li>[Offcanvas] – Fixed issue with inner-animations sometimes not resetting.</li>
			</ul>			

			<h3>1.1.9 ( October 07, 2020 )</h3>
			<ul>
				<li>[Circular Progress] – New component for adding animated circular progress bars.</li>
				<li>[Pro Media Player] – New component for adding lazy loading videos &amp; audio with customizable UI.</li>
				<li>[Carousel Builder] – Added support for ACF gallery.</li>
				<li>[Carousel Builder] – Added ‘fullscreen’ option.</li>
				<li>[Carousel Builder] – Added styles for disabled navigation (when no more cells to navigate to).</li>
				<li>[Counter] – Counter will now show the end number instead of the start number inside the Oxygen builder.</li>
				<li>[Post Terms] – CPT taxonomies now included in taxonomy dropdown.</li>
				<li>[Fluent Form] – Fixed issue with characters no being visible inside form dropdown.</li>
				<li>[Slide Menu] – Fixed issue with arrow triggering menu link when viewing on Chrome mobile view.</li>
				<li>[Adjacent Posts] – Fixed issue with ‘stack posts’ setting causing some CSS from components to not be included.</li>
				<li>[Read More / Less] – Added gradient fade option.</li>
			</ul>

			<h3>1.1.8 ( September 15, 2020 )</h3>
			<ul>
				<li>
					[Infinite Scroller] – New component for applying infinite scrolling to
					Easy Posts/ or Repeaters or Products Lists.
				</li>
				<li>
					[Carousel Builder] – Added carousel “ticker” option for allowing
					continuous movement.
				</li>
				<li>
					[OffCanvas] – Fixed an issue with hash links not scrolling after closing
					offcanvas.
				</li>
				<li>
					[OffCanvas] – Fixed staggered animations not resetting when revealing
					offcanvas from the top.
				</li>
				<li>[General] – Support for dynamic data added to multiple components.</li>
				<li>
					[General] – Selector fields now support attribute selectors, eg
					.class[attr=value]
				</li>
				<li>
					[General] – Components list on the plugin settings page now in
					alphabetical order.
				</li>
			</ul>
			<h3>1.1.7 ( August 17, 2020 )</h3>
			<ul>
				<li>
					[Carousel Builder] – Added support for Easy Posts, Woo Components or
					using custom elements as cells.
				</li>
				<li>
					[Carousel Builder] – Added the ability to turn off carousel
					functionality at any breakpoint.
				</li>
				<li>
					[Carousel Builder] – Added Scale &amp; transition controls for page
					dots.
				</li>
				<li>
					[Carousel Builder] – Added support for parallax elements (using the
					Repeater).
				</li>
				<li>[Lottie] – JSON now lazy loaded (can be disabled).</li>
				<li>
					[Off Canvas] – Added ‘staggered animation’ option for inner elements
					using Oxygen’s scroll animation.
				</li>
				<li>
					[Preloader] – Fixed a rare issue where some elements would appear before
					the preloader.
				</li>
				<li>
					[Burger Trigger] – Fixed an issue with some unpredictable behavior when
					used with slide menu.
				</li>
				<li>[Fluent Form] – Fixed hover opacity for the submit button.</li>
				<li>
					[General] – Fixed an issue with the text fields not allowing quotes.
				</li>
			</ul>
			<h3>1.1.6 ( August 17, 2020 )</h3>
			<ul>
				<li>
					[Carousel Builder] – New component for visually building carousels (for
					use with repeater component).
				</li>
				<li>
					[Offcanvas] – Any elements inside the offcanvas can now make use of
					Oxygen’s scroll animations, triggered when the offcanvas is opened
					(rather than on page load).
				</li>
				<li>
					[Slide Menu] – Added new menu alignment controls and focus controls for
					the sub menu buttons.
				</li>
				<li>
					[Lottie] – Added option to render animation as &lt;canvas&gt; instead of
					&lt;svg&gt; (to prevent rare cases of some animations flickering in
					Safari).
				</li>
			</ul>
			<h3>1.1.5 ( August 06, 2020 )</h3>
			<ul>
				<li>
					[Fluent Form] – Fixed padding issue on phone/mobile field when flag is
					disabled.
				</li>
				<li>
					[Cart Counter] – Fixed issue with cart number not updating (present only
					in v1.1.4).
				</li>
			</ul>
			<h3>1.1.4 ( August 05, 2020 )</h3>
			<ul>
				<li>
					[Toggle Switch] New component for switching content or toggling classes.
				</li>
				<li>
					[Content Switcher] New component allowing to switch between two elements
					(for use with the toggle switch).
				</li>
				<li>
					[Burger Trigger] – Added ‘scale’ changing size of burger independent of
					size of the button.
				</li>
				<li>
					[Cart counter] – Accessibility improvement – Dropdown cart now can be
					accessed by keyboard.
				</li>
				<li>[Counter] – Number fields now accept dynamic data.</li>
				<li>
					[Off Canvas] – Accessibility improvement – Now able to change the focus
					when offcanvas opened to any selector inside.
				</li>
				<li>
					[Off Canvas] – Trigger can now be from inside dynamically added content.
				</li>
				<li>[Slide Menu] – Added site navigation schema markup option.</li>
				<li>[Fluent Form] – Colour controls added for Invalid input state.</li>
				<li>[Fluent Form] – Label Typography font weight issue fixed.</li>
				<li>[Fluent Form] – Smart UI checkbox issue fixed on iPhone.</li>
				<li>
					[Fluent Form] – Fixed issue with form dropdown not appearing in Oxygen
					if a form name contained disallowed words.
				</li>
				<li>[Fluent Form] – Added Form ID control which accepts dynamic data.</li>
				<li>
					[Social Share] – Added Support for Pinterest, WhatsApp &amp; Telegram.
				</li>
				<li>
					[Social Share] – Fixed issue with email share link when post titles
					contained certain characters.
				</li>
				<li>[Header Search] – Fixed a W3C Validator issue.</li>
				<li>[Lottie] – ACF field can now be used to get the JSON URL.</li>
				<li>
					[Lottie] – Cursor position control can now be relative to any container
					element.
				</li>
				<li>
					[Lottie] – Added MouseOver control (similar to hover but with frame
					control &amp; reverse animation when use stops hovering.
				</li>
				<li>
					[General] Small performance improvements for users on Oxygen v3.4+ (less
					inline JS output where possible).
				</li>
				<li>[General] In-builder performance improvements (loading less JS).</li>
				<li>[General] Added support ready for Oxygen v3.5 (new preset code).</li>
			</ul>
			<h3>1.1.3 ( June 25, 2020 )</h3>
			<ul>
				<li>
					Added checkboxes in the plugin’s settings page so that only selected
					components can be added to the Oxygen editor.
				</li>
			</ul>
			<h3>1.1.2 ( June 24, 2020 )</h3>
			<ul>
				<li>
					[Alert Box] – Can now be used as a header notification bar, with
					‘SlideUp’ close option added.
				</li>
				<li>[Fluent Form] – Compatibility with v3.6.0.</li>
				<li>[Header Search] – More control over icon positioning.</li>
				<li>
					[Mini Cart] – Fixed scrollbar issue, more controls for positioning of
					inner elements.
				</li>
				<li>
					[Off Canvas] – Added z-index controls for both backdrop &amp; inner
					content.
				</li>
			</ul>
			<h3>1.1.1 ( June 18, 2020 )</h3>
			<ul>
				<li>
					[Off Canvas] – Fixed an issue with the chosen selector not triggering
					Off Canvas.
				</li>
				<li>[Preloader] – Fixed slight glitchy animation on iPhones.</li>
			</ul>
			<h3>1.1 ( June 18, 2020 )</h3>
			<ul>
				<li>[Cart Counter] New component for displaying Woocommerce Cart Count.</li>
				<li>[Mini Cart] New component for displaying Woocommerce Mini Cart.</li>
				<li>
					[Preloader] – New component for building preloaders to hide FOUC or
					FOUT.
				</li>
				<li>
					[Fluent Form] – Added style controls for Payment Summary and Checkable
					Grids.
				</li>
				<li>
					[Fluent Form] – Added “General Styles” for overall form typography,
					button transitions.
				</li>
				<li>[Off Canvas] – Slide from Top/Button now available.</li>
				<li>
					[Off Canvas] – Can now be triggered from inside a modal if click trigger
					is also a trigger for closing the modal.
				</li>
				<li>
					[Slide Menu] – Prevent issue with browsers auto-scrolling with hashlinks
					&amp; sub menu items collapsing height.
				</li>
			</ul>
			<h3>1.0.9 ( June 13, 2020 )</h3>
			<ul>
				<li>
					[Fluent Form] Added support for Smart UI styling, added more style
					options and renamed controls to match official Fluent Forms.
				</li>
				<li>[Fluent Form] Fixed code issue if FF not active.</li>
				<li>
					[Reading Time] Added “Text After (Singular)” and “Text After (Plurarl)”
					settings for customizable After text.
				</li>
				<li>
					[Off Canvas] Menu items with hash links inside Off Canvas can now open
					submenu by clicking entire menu item.
				</li>
				<li>
					Fixed license activation/deactivation issue especially after site has
					been migrated to a new location.
				</li>
				<li>A few other general code polishes.</li>
			</ul>
			<h3>1.0.8 ( June 10, 2020 )</h3>
			<ul>
				<li>
					[Adjacent Posts] Fixed an issue with the next post showing even if
					empty.
				</li>
			</ul>
			<h3>1.0.7 ( June 09, 2020 )</h3>
			<ul>
				<li>
					[Fluent Form] Added a check to ensure that there will be no errors if
					Fluent Forms is not active.
				</li>
			</ul>
			<h3>1.0.6 ( June 09, 2020 )</h3>
			<ul>
				<li>
					[Fluent Form] Added dropdown for selecting form by name, instead of ID.
				</li>
				<li>
					[Lottie Animation] Added Click Animation Trigger with optional reverse
					second clicks.
				</li>
				<li>
					[Lottie Animation] Easier controls with sliders for frames and speed
					&amp; width / height.
				</li>
				<li>
					[Back to Top] Added ability to include any element inside button to
					build manually.
				</li>
				<li>[Back to Top] Added option to be visible only when scrolling up.</li>
				<li>[Off Canvas] Fixed issue with iPhone 5/6 with backdrop.</li>
				<li>
					[Off Canvas] Added auto close if any hash links clicked from inside off
					canvas.
				</li>
				<li>
					[Adjacent Posts] Prev/next posts can now be split across two components
					for more flexible positioning.
				</li>
				<li>[Alert Box] Now can add divs inside without issue.</li>
			</ul>
			<h3>1.0.5 ( June 05, 2020 )</h3>
			<ul>
				<li>
					[Fluent Form] Added more focus styles to forms (&amp; more style options
					for multi step forms).
				</li>
				<li>
					[Lottie Animation] Added Top/Bottom offset controls to scroll
					animations.
				</li>
				<li>[Lottie Animation] Added cursor position based animation.</li>
				<li>
					[Lottie Animation] Added the ability for sync scrolling to any
					container.
				</li>
				<li>[Lottie Animation] Added the ability to toggle loop on/off.</li>
				<li>
					[Lottie Animation] Fixed an issue with scrolling not working with
					multiple animations.
				</li>
			</ul>
			<h3>1.0.4 ( June 04, 2020 )</h3>
			<ul>
				<li>Fixed – button hover issue in Back to Top.</li>
				<li>
					Fixed – Off Canvas builder visibility causing elements to be
					unclickable.
				</li>
				<li>New – Option to add custom aria label to Burger Trigger button.</li>
				<li>Fixed – Prevent duplicate IDs on search icons.</li>
				<li>
					Edit – Removed the ability to change Slide Menu type in media queries to
					prevent issues.
				</li>
			</ul>
			<h3>1.0.3 ( June 03, 2020 )</h3>
			<ul>
				<li>Fixed duplicate ID on icons in the Adjacent Posts component.</li>
				<li>
					Fixed a bug in the Slide Menu component causing it not to function
					properly when hidden by default.
				</li>
				<li>
					Moved “Open / Close Trigger selector” setting in Off Canvas component to
					the Primary tab for easier access.
				</li>
				<li>
					Changed container elements’ tags from “span” to “div” in Back to Top
					component’s output.
				</li>
				<li>
					Added a screenshot under the License Key form showing where the extras
					added by the plugin can be found.
				</li>
			</ul>
			<h3>1.0.2 ( June 02, 2020 )</h3>
			<ul>
				<li>Fixed Burger Trigger and Off Canvas components not appearing.</li>
			</ul>
			<h3>1.0.1 ( June 02, 2020 )</h3>
			<ul>
				<li>Added a link to Settings under the plugin name.</li>
			</ul>
			<h3>1.0.0 ( June 02, 2020 )</h3>
			<ul>
				<li>Initial release.</li>
			</ul>
		</div>
		<?php
	}

	public static function oxy_extras_init() {

		// check if Oxygen installed & active.
		if ( ! class_exists( 'OxygenElement' ) ) {
			return;
		}

		if ( version_compare( CT_VERSION, '3.2', '<' ) || version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {

			add_action( 'admin_notices', array( __CLASS__, 'oxy_extras_admin_versions_notice' ) );
			return;

		}

		require_once 'OxyExtrasEl.php';
		require_once 'OxyExtras.php';

		$OxyExtras = new OxyExtras( self::PREFIX );

	}

	public static function clean_db_on_uninstall() {

		foreach ( wp_load_alloptions() as $option => $value ) {
			if ( strpos( $option, 'oxy_extras_' ) === 0 ) {
				delete_option( $option );
			}
		}

	}

	function oxy_extras_admin_versions_notice() {
		?>

		  <div class="notice notice-warning">
			  <p><?php _e( 'OxyExtras needs Oxygen Builder 3.2+ and WordPress 4.7+ to work.', 'oxyextras' ); ?></p>
		  </div>
		<?php
	}

	public static function plugin_updater() {
		// retrieve our license key from the DB.
		$license_key = trim( get_option( self::PREFIX . 'license_key' ) );

		// setup the updater.
		$edd_updater = new Oxy_Extras_Plugin_Updater(
			self::STORE_URL,
			__FILE__,
			array(
				'version'   => self::VERSION, // current version number
				'license'   => $license_key, // license key (used get_option above to retrieve from DB)
				'item_id'   => self::ITEM_ID, // ID of the product
				'item_name' => self::TITLE,
				'author'    => 'OxyExtras', // author of this plugin
				'url'       => home_url(),
				'beta'      => false,
			)
		);
	}

	public static function settings_link( $links ) {
		$url = esc_url(
			add_query_arg(
				'page',
				self::PREFIX . 'menu',
				get_admin_url() . 'admin.php'
			)
		);

		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

		// Adds the link to the beginning of the array.
		array_unshift(
			$links,
			$settings_link
		);

		return $links;
	}

}

OxyExtrasPlugin::init();
