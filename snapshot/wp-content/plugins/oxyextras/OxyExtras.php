<?php

if ( class_exists( 'OxyExtras' ) ) {
	return;
}

class OxyExtras {

	public $modules = array();
	public $prefix;
	function __construct( $prefix ) {

		if ( true !== OxyExtrasLicense::is_activated_license() ) {
			return;
		}
		$this->prefix = $prefix;
		$this->set_files();

		add_action( 'admin_init', array( $this, 'register_options' ) );
		add_action( $this->prefix . 'form_options', array( $this, 'options_form' ) );

		$this->load_files();

		// Iframe Scripts
		add_action( 'oxygen_enqueue_iframe_scripts', array( $this, 'iframe_scripts' ) );

		// frontend Scripts
		add_action( 'wp_footer', array( $this, 'frontend_scripts' ), 12 );


		// UI style (Oxygen v4+ only)
		if ( function_exists('do_oxygen_elements') ) {
			add_action( 'oxygen_enqueue_ui_scripts', array( $this, 'ui_styles' ) );
		}

		// Register section.
		add_action( 'oxygen_add_plus_sections', array( $this, 'register_add_plus_section' ) );

		// Register Sub Section.
		add_action( 'oxygen_add_plus_extras_section_content', array( $this, 'register_add_plus_subsections' ) );

	}

	function register_add_plus_section() {

		// Register 'Extras' Accordian Section.
		CT_Toolbar::oxygen_add_plus_accordion_section( 'extras', __( 'Extras' ) );

	}

	function register_add_plus_subsections() {

		ob_start();
		do_action( 'oxygen_add_plus_extras_interactive' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'Interactive', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

		ob_start();
		do_action( 'oxygen_add_plus_extras_single' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'Single Posts', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

		ob_start();
		do_action( 'oxygen_add_plus_extras_other' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'Other', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

		ob_start();
		do_action( 'oxygen_add_plus_extras_dynamic' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'Dynamic Text', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

		ob_start();
		do_action( 'oxygen_add_plus_extras_wordpress' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'WordPress', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

		ob_start();
		do_action( 'oxygen_add_plus_extras_woo' );
		$result = ob_get_clean();
		if ( ! empty( trim( $result ) ) ) {
			?>
			<h2><?php _e( 'Woocommerce', 'oxygen' ); ?></h2>
			<?php
			echo $result;
		}

	}

	function register_options() {
		foreach ( $this->modules as $key => $module ) {
			add_option( $this->prefix . $key, 0 );
			register_setting( $this->prefix . 'settings', $this->prefix . $key, array( $this, 'sanitize_enable' ) );
		}

	}

	function sanitize_enable( $enable ) {

		if ( is_numeric( $enable ) && intval( $enable ) === 1 ) {
			return 1;
		}

		return 0; // default
	}

	function options_form() {
		foreach ( $this->modules as $key => $module ) {
			?>


			<tr valign="top"<?php echo get_option( $this->prefix . $key ) === '1' ? ' class="active"' : ' class="inactive"'; ?>>
				<th class="check-column">
					<input id="<?php echo $this->prefix . $key; ?>" name="<?php echo $this->prefix . $key; ?>" type="checkbox" value="1" <?php checked( get_option( $this->prefix . $key ), 1 ); ?> />
				</th>
				<td class="plugin-title column-primary">
					<?php echo '<strong>' . $module['title'] . '</strong>'; ?>
				</td>
				<th class="doc-link-th" style="text-align: right; padding-right: 10px;">
					<?php echo '<p class="doc-link"><a target="_blank" href="https://oxyextras.com/docs/' . $module['doclinkslug'] . '/">Doc</a></p>'; ?>
				</th>
			</tr>


			<?php
		}

	}

	function set_files() {
		$this->modules = array(
			'adjecent_posts'           => array(
				'title'       => 'Adjacent Posts',
				'file'        => 'components/adjacent-posts.php',
				'doclinkslug' => 'adjacent-posts',
			),
			'alert_box'                => array(
				'title'       => 'Alert Box',
				'file'        => 'components/alert-box.php',
				'doclinkslug' => 'alert-box',
			),
			'author_box'               => array(
				'title'       => 'Author Box',
				'file'        => 'components/author-box.php',
				'doclinkslug' => 'author-box',
			),
			'back_to_top'              => array(
				'title'       => 'Back To Top',
				'file'        => 'components/back-to-top.php',
				'doclinkslug' => 'back-to-top',
			),
			'burger_trigger'           => array(
				'title'       => 'Burger Trigger',
				'file'        => 'components/burger-trigger.php',
				'doclinkslug' => 'burger-trigger',
			),
			'carousel-builder'         => array(
				'title'       => 'Carousel Builder',
				'file'        => 'components/carousel.php',
				'doclinkslug' => 'carousel-builder',
			),
			'cartcount'                => array(
				'title'       => 'Cart Counter',
				'file'        => 'components/cart-count.php',
				'doclinkslug' => 'cart-counter',
			),
			'circular-progress'        => array(
				'title'       => 'Circular Progress',
				'file'        => 'components/circular-progress.php',
				'doclinkslug' => 'circular-progress',
			),
			'content-switcher'         => array(
				'title'       => 'Content Switcher',
				'file'        => 'components/content-switcher.php',
				'doclinkslug' => 'content-switcher',
			),
			'content-timeline'         => array(
				'title'       => 'Content Timeline',
				'file'        => 'components/content-timeline.php',
				'doclinkslug' => 'content-timeline',
			),
			'copy_to_clipboard'           => array(
				'title'       => 'Copy to Clipboard',
				'file'        => 'components/copy-to-clipboard.php',
				'doclinkslug' => 'copy-to-clipboard',
			),
			'copyright_text'           => array(
				'title'       => 'Copyright Year',
				'file'        => 'components/copyright-text.php',
				'doclinkslug' => 'copyright-year',
			),
			'counter'                  => array(
				'title'       => 'Counter',
				'file'        => 'components/counter.php',
				'doclinkslug' => 'counter',
			),
			'countdown'                   => array(
				'title'       => 'Countdown Timer',
				'file'        => 'components/countdown.php',
				'doclinkslug' => 'countdown-timer',
			),
            'dynamictabs'                  => array(
				'title'       => 'Dynamic Tabs',
				'file'        => 'components/tabs.php',
				'doclinkslug' => 'dynamic-tabs',
			),
			'pro_login'                => array(
				'title'       => 'Extras Login Form',
				'file'        => 'components/pro-login.php',
				'doclinkslug' => 'extras-login-form',
			),
			'fluent_form'              => array(
				'title'       => 'Fluent Form',
				'file'        => 'components/fluent-form.php',
				'doclinkslug' => 'fluent-forms',
			),
			'gutenberg_reusable_block' => array(
				'title'       => 'Gutenberg Reusable Block',
				'file'        => 'components/gutenberg-reusable-block.php',
				'doclinkslug' => 'reusable-block',
			),
			'header_search'            => array(
				'title'       => 'Header Search',
				'file'        => 'components/header-search.php',
				'doclinkslug' => 'header-search',
			),
			'horizontal_slide_menu'            => array(
				'title'       => 'Horizontal Slide Menu',
				'file'        => 'components/horizontal-slide-menu.php',
				'doclinkslug' => 'horizontal-slide-menu',
			),
            'hotspots'            => array(
				'title'       => 'Hotspots / Popovers',
				'file'        => 'components/hotspots.php',
				'doclinkslug' => 'hotspots-popovers',
			),
			'infinite-scroller'        => array(
				'title'       => 'Infinite Scroller',
				'file'        => 'components/infinite-scroll.php',
				'doclinkslug' => 'infinite-scroller',
			),
			'interactive_cursor'        => array(
				'title'       => 'Interactive Cursor',
				'file'        => 'components/interactive-cursor.php',
				'doclinkslug' => 'interactive-cursor',
			),
            'lightbox'        => array(
				'title'       => 'Lightbox',
				'file'        => 'components/lightbox.php',
				'doclinkslug' => 'lightbox',
			),
			'lottie'                   => array(
				'title'       => 'Lottie Animation',
				'file'        => 'components/lottie.php',
				'doclinkslug' => 'lottie-animation',
			),
			'mega-menu'                => array(
				'title'       => 'Mega Menu / Dropdown',
				'file'        => 'components/mega-menu.php',
				'doclinkslug' => 'mega-menu',
			),
			'minicart'                 => array(
				'title'       => 'Mini Cart',
				'file'        => 'components/mini-cart.php',
				'doclinkslug' => 'mini-cart',
			),
			'off_canvas_wrapper'       => array(
				'title'       => 'Off Canvas',
				'file'        => 'components/off-canvas-wrapper.php',
				'doclinkslug' => 'off-canvas',
			),
			'post_modified_date'       => array(
				'title'       => 'Post Modified Date',
				'file'        => 'components/post-modified-date.php',
				'doclinkslug' => 'post-modified-date',
			),
			'post_terms'               => array(
				'title'       => 'Post Terms',
				'file'        => 'components/post-terms.php',
				'doclinkslug' => 'post-terms',
			),
			'preloader'                => array(
				'title'       => 'Preloader',
				'file'        => 'components/preloader.php',
				'doclinkslug' => 'preloader-builder',
			),
			'pro_accordion'            => array(
				'title'       => 'Pro Accordion',
				'file'        => 'components/pro-accordion.php',
				'doclinkslug' => 'pro-accordion',
			),
			'media-player'             => array(
				'title'       => 'Pro Media Player',
				'file'        => 'components/media-player.php',
				'doclinkslug' => 'pro-media-player',
			),
			'read_more'                => array(
				'title'       => 'Read More / Less',
				'file'        => 'components/read-more.php',
				'doclinkslug' => 'read-more-less',
			),
			'reading_progress_bar'     => array(
				'title'       => 'Reading Progress Bar',
				'file'        => 'components/reading-progress-bar.php',
				'doclinkslug' => 'reading-progress-bar',
			),
			'reading_time'             => array(
				'title'       => 'Reading Time',
				'file'        => 'components/reading-time.php',
				'doclinkslug' => 'reading-time',
			),
			'slide_menu'               => array(
				'title'       => 'Slide Menu',
				'file'        => 'components/slide-menu.php',
				'doclinkslug' => 'slide-menu',
			),
			'social_share_buttons'     => array(
				'title'       => 'Social Share Buttons',
				'file'        => 'components/social-share-buttons.php',
				'doclinkslug' => 'social-share',
			),
			'toc'                   => array(
				'title'       => 'Table of Contents',
				'file'        => 'components/toc.php',
				'doclinkslug' => 'table-of-contents',
			),
			'toggle'                   => array(
				'title'       => 'Toggle Switch',
				'file'        => 'components/toggle.php',
				'doclinkslug' => 'toggle-switch',
			),			

		);
	}

	function load_files() {

		foreach ( $this->modules as $key => $module ) {
			// this will block the rest of the mod to load, if it is not checked.
			if ( 0 === intval( get_option( $this->prefix . $key, 0 ) ) ) {
				continue;
			}
			include_once $module['file'];
		}

	}

	function iframe_scripts() {
        // Loading all default styles inside builder, so when components are used as resusable elements they still show correct styles inside the builder.
		wp_enqueue_style( 'accordion-css', plugin_dir_url( __FILE__ ) . 'components/assets/accordion.css' );
        wp_enqueue_style( 'default-css', plugin_dir_url( __FILE__ ) . 'components/assets/default.css' );
        wp_enqueue_style( 'hamburgers-css', plugin_dir_url( __FILE__ ) . 'components/assets/hamburgers.css' );
        wp_enqueue_style( 'flickity-css', plugin_dir_url( __FILE__ ) . 'components/assets/flickity/flickity.css' );
        wp_enqueue_style( 'preloader-css', plugin_dir_url( __FILE__ ) . 'components/preloader.css' );
        wp_enqueue_style( 'preloader-css', plugin_dir_url( __FILE__ ) . 'components/assets/vime/vime.css' );
        wp_enqueue_style( 'skeletabs-css', plugin_dir_url( __FILE__ ) . 'components/assets/skeletabs/skeletabs.css' );
	}

	function ui_styles() {
		wp_enqueue_style( 'ui-css', plugin_dir_url( __FILE__ ) . 'components/assets/ui.css' );
	}

	function frontend_scripts() {

		if ( !defined("SHOW_CT_BUILDER") && defined( 'WPGB_VERSION' ) ) {

			wp_enqueue_script( 'gridbuildersupport', plugin_dir_url( __FILE__ ) . 'includes/js/gridbuildersupport.js', '', '1.0.2', true );
			
		}

	}


}
