<?php

// View All Pages.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-pages-view-all',
		'title'  => __( 'View All Pages' ),
		'parent' => 'oxy-toolbox-oxy-pages',
		'href'   => admin_url( 'edit.php?post_type=page' ),
		'meta'   => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Pages in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-pages-view-all-new-tab',
		// 'title' => __( 'View All Pages' ),
		'parent' => 'oxy-pages-view-all',
		'href'   => admin_url( 'edit.php?post_type=page' ),
		'meta'   => array(
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title'  => __( 'View All Pages in a New Tab' ),
		),
	)
);
global $wpdb;

$query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'page' AND post_status='publish' ORDER BY " . self::$orderby . ' ' . self::$order;

$results = $wpdb->get_results( $query );


foreach ( $results as $p ) {

	if ( get_option( 'page_for_posts' ) == $p->ID || get_option( 'page_on_front' ) == $p->ID ) {
		$generic_view = ct_get_archives_template( $p->ID ); // true, for exclude templates of type inner_content

		if ( ! $generic_view ) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
			$generic_view = ct_get_posts_template( $p->ID );
		}
	} else {
		$generic_view = ct_get_posts_template( $p->ID ); // true, exclude templates of type inner_content
	}

	$ct_other_template = get_post_meta( $p->ID, 'ct_other_template', true );

	// check if the other template contains ct_inner_content
	$shortcodes = false;

	if ( $ct_other_template && $ct_other_template > 0 ) {
		$shortcodes = get_post_meta( $ct_other_template, 'ct_builder_shortcodes', true );
	} elseif ( $generic_view && $ct_other_template != -1 ) {
		$shortcodes = get_post_meta( $generic_view->ID, 'ct_builder_shortcodes', true );
	}

	$ct_inner = ( ( $shortcodes && strpos( $shortcodes, '[ct_inner_content' ) !== false ) && intval( $ct_other_template ) !== -1 ) ? '&ct_inner=true' : '';

	$edit_url = esc_url( ct_get_post_builder_link( $p->ID ) ) . $ct_inner;

	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-page' . $p->ID,
			'title'  => $p->post_title,
			'parent' => 'oxy-toolbox-oxy-pages',
			'href'   => $edit_url,
			'meta'   => array(
				'title' => __( 'Edit this Page with Oxygen' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// Links to open in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-page-new-tab' . $p->ID,
			'title'  => $p->post_title,
			'parent' => 'oxy-page' . $p->ID,
			'href'   => esc_url( $edit_url ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Edit this Page with Oxygen in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}
