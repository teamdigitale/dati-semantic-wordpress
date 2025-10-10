<?php

// View All Pages.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-wp-pages-view-all',
		'title'  => __( 'View All Pages' ),
		'parent' => 'oxy-toolbox-wp-pages',
		'href'   => admin_url( 'edit.php?post_type=page' ),
		'meta'   => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Pages in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-wp-pages-view-all-new-tab',
		// 'title' => __( 'Edit Current Page in a New Tab' ),
		'parent' => 'oxy-wp-pages-view-all',
		'href'   => admin_url( 'edit.php?post_type=page' ),
		'meta'   => array(
			'target' => '_blank',
			// 'class' => 'view-all-pages-new-tab view-all-items-new-tab',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title'  => __( 'View All Pages in a New Tab' ),
		),
	)
);


global $wpdb;

$query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'page' AND post_status='publish' ORDER BY post_title ASC";

$results = $wpdb->get_results( $query );


foreach ( $results as $p ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-wp-pages' . $p->ID,
			'title'  => $p->post_title,
			'parent' => 'oxy-toolbox-wp-pages',
			'href'   => esc_url( get_edit_post_link( $p ) ),
			'meta'   => array(
				'title' => __( 'Edit this Page in WordPress' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// View Page links.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-wp-pages-sub' . $p->ID,
			'title'  => 'View: ' . $p->post_title,
			'parent' => 'oxy-wp-pages' . $p->ID,
			'href'   => esc_url( get_permalink( $p->ID ) ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'View: ' ) . $p->post_title,
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-view',
			),
		)
	);
}
