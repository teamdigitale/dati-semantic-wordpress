<?php

// View All Posts.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-wp-posts-view-all',
		'title'  => __( 'View All Posts' ),
		'parent' => 'oxy-toolbox-wp-posts',
		'href'   => admin_url( 'edit.php' ),
		'meta'   => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Posts in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-wp-posts-view-all-new-tab',
		// 'title' => __( 'Edit Current Post in a New Tab' ),
		'parent' => 'oxy-wp-posts-view-all',
		'href'   => admin_url( 'edit.php' ),
		'meta'   => array(
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title'  => __( 'View All Posts in a New Tab' ),
		),
	)
);

global $wpdb;

$query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'post' AND post_status='publish' ORDER BY post_date DESC";

$results = $wpdb->get_results( $query );


foreach ( $results as $p ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-wp-posts' . $p->ID,
			'title'  => $p->post_title . ' (' . get_post_time( 'j M', false, $p->ID, false ) . ')',
			'parent' => 'oxy-toolbox-wp-posts',
			'href'   => esc_url( get_edit_post_link( $p ) ),
			'meta'   => array(
				'title' => __( 'Edit this Post in WordPress' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

		// View Post links.
		$wp_admin_bar->add_node(
			array(
				'id'     => 'oxy-wp-posts-sub' . $p->ID,
				'title'  => 'View: ' . $p->post_title,
				'parent' => 'oxy-wp-posts' . $p->ID,
				'href'   => esc_url( get_permalink( $p->ID ) ),
				'meta'   => array(
					'target' => '_blank',
					'title'  => __( 'View on frontend: ' ) . $p->post_title,
					'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-view',
				),
			)
		);
}
