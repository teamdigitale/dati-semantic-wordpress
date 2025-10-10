<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Oxy_Toolbox_TOC {

	static $prefix;
	static $mod         = 'toc_';
	static $title       = 'Table of Contents (frontend)';
	static $description = 'Builds a Table of Contents dynamically based on the headings on the page.';
	static $link        = 'https://oxyplugins.com/doc/table-of-contents/';

	static function init( $prefix ) {

		self::$prefix = $prefix;

		$oxy_toolbox_mod = new Oxy_Toolbox_Mod( $prefix, self::$mod, self::$title, self::$description, self::$link );

		// this will block the rest of the mod to load, if it is not checked.
		if ( 0 === intval( get_option( self::$prefix . self::$mod, 0 ) ) || OxyToolboxLicense::is_activated_license() !== true ) {
			return;
		}

		if ( ! shortcode_exists( 'oxy_toc' ) ) {
			add_action( self::$prefix . 'enqueue_scripts', array( __CLASS__, 'mod_scripts' ), 1 );

			add_shortcode( 'oxy_toc', array( __CLASS__, 'oxy_toc_callback' ) );
		}
	}

	static function mod_scripts( $version ) {
		if ( defined( 'SHOW_CT_BUILDER' ) ) {
			return;
		}

		wp_register_style( self::$prefix . self::$mod . 'style', plugins_url( 'css/style.css', __FILE__ ), array(), $version );

		wp_register_script( self::$prefix . self::$mod . 'toc', plugins_url( 'js/jquery.toc.min.js', __FILE__ ), array( 'jquery' ), $version, true );

		wp_register_script( self::$prefix . self::$mod . 'script', plugins_url( 'js/script.js', __FILE__ ), array(), $version, true );
	}

	static function oxy_toc_callback( $atts ) {
		$atts = shortcode_atts(
			array(
				'title'     => 'Table of Contents',
				'tag'       => 'ul',
				'content'   => '.ct-inner-content',
				'headings'  => 'h1,h2,h3',
				'collapsed' => false,
			),
			$atts,
			'oxy_toc'
		);

		wp_enqueue_style( self::$prefix . self::$mod . 'style' );

		wp_enqueue_script( self::$prefix . self::$mod . 'toc' );

		wp_enqueue_script( self::$prefix . self::$mod . 'script' );

		if ( '' !== $atts['title'] ) {
			$atts['title'] = '<div class="oxy-toc"><button type="button" class="collapsible' . ( false === $atts['collapsed'] ? ' active' : '' ) . '">' . $atts['title'] . '</button>';
		} else {
			$atts['title'] = '<div class="oxy-toc"><button type="button" class="collapsible' . ( false === $atts['collapsed'] ? ' active' : '' ) . '"></button>';
		}

		if ( '' === $atts['tag'] ) {
			$atts['tag'] = 'ul';
		}

		if ( '' === $atts['content'] ) {
			$atts['content'] = '.ct-inner-content';
		}

		return sprintf( '%s<div class="toc-list"><%s data-toc="%s" data-toc-headings="%s" class="toc"></%s></div></div>', $atts['title'], $atts['tag'], $atts['content'], $atts['headings'], $atts['tag'] );
	}

}

Oxy_Toolbox_TOC::init( self::PREFIX );
