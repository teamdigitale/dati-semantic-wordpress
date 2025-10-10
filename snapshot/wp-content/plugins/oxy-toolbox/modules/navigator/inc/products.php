<?php

// View All Products.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-wp-products-view-all',
		'title' => __( 'View All Products' ),
		'parent' => 'oxy-toolbox-wp-products',
		'href'  => admin_url( 'edit.php?post_type=product' ),
		'meta'  => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Products in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-wp-products-view-all-new-tab',
		'title' => __( 'View All Products' ),
		'parent' => 'oxy-wp-products-view-all',
		'href'  => admin_url( 'edit.php?post_type=product' ),
		'meta'  => array(
			'target'=> '_blank',
			'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title' => __( 'View All Products in a New Tab' ),
		),
	)
);

// WP_Query arguments.
$args = array(
	'post_type' => array( 'product' ),
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
		$product = wc_get_product( $p->ID );

		$wp_admin_bar->add_node(
			array(
				'id'    => 'oxy-wp-products' . $p->ID,
				'title' => $p->post_title . ' (' . $product->get_price_html() . ')',
				'parent' => 'oxy-toolbox-wp-products',
				'href'  => esc_url( get_edit_post_link( $p ) ),
				'meta'  => array(
					'title' => __( 'Edit this Product in WordPress' ),
					'class' => 'oxy-toolbox-parent-of-mini-child'
				),
			)
		);
		
		// View Product links.
		$wp_admin_bar->add_node(
			array(
				'id'    => 'oxy-wp-products-sub' . $p->ID,
				'title' => 'View: ' . $p->post_title,
				'parent' => 'oxy-wp-products' . $p->ID,
				'href'  => esc_url( get_permalink( $p ) ),
				'meta'  => array(
					'target'=> '_blank',
					'title' => __( 'View on frontend: ' ) . $p->post_title,
					'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-view'
				),
			)
		);
	} // End foreach().
} else {
	// no posts found.
}

// Restore original Post Data.
wp_reset_postdata();