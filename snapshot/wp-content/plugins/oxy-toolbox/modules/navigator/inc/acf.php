<?php

// View All ACF Field Groups.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-acf-view-all',
		'title' => __( 'View All Field Groups' ),
		'parent' => 'oxy-toolbox-acf',
		'href'  => admin_url( 'edit.php?post_type=acf-field-group' ),
		'meta'  => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Field Grups in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-acf-view-all-new-tab',
		'parent' => 'oxy-acf-view-all',
		'href'  => admin_url( 'edit.php?post_type=acf-field-group' ),
		'meta'  => array(
			'target'=> '_blank',
			'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title' => __( 'View All Field Groups in a New Tab' ),
		),
	)
);

// WP_Query arguments.
$args = array(
	'post_type' => array( 'acf-field-group' ),
	'order' => self::$order,
	'orderby' => self::$orderby,
	'post_status' => 'publish',
	'posts_per_page' => -1,
);

// The Query.
$query = new WP_Query( $args );

// The Loop.
if ( $query->have_posts() ) {
	foreach ( $query->get_posts() as $p ) {

		$wp_admin_bar->add_node(
			array(
				'id'    => 'oxy-acf' . $p->ID,
				'title' => $p->post_title,
				'parent' => 'oxy-toolbox-acf',
				'href'  => esc_url( get_edit_post_link( $p ) ),
				'meta'  => array(
					'title' => __( 'Edit this Field Group' ),
					'class' => 'oxy-toolbox-parent-of-mini-child'
				),
			)
		);
		
		// Links to open in a new tab.
		$wp_admin_bar->add_node(
			array(
				'id'    => 'oxy-acf-new-tab' . $p->ID,
				'title' => $p->post_title,
				'parent' => 'oxy-acf' . $p->ID,
				'href'  => esc_url( get_edit_post_link( $p ) ),
				'meta'  => array(
					'target'=> '_blank',
					'class' => 'edit-with-oxygen-new-tab',
					'title' => __( 'Edit this Field Group in a new tab' ),
					'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
				),
			)
		);

	} // End foreach().
} else {
	// no posts found.
}

// Restore original Post Data.
wp_reset_postdata();