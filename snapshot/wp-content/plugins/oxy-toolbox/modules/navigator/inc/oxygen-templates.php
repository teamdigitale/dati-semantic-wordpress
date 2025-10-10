<?php

// View All Templates.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-templates-view-all',
		'title'  => __( 'View All Templates' ),
		'parent' => 'oxy-toolbox-oxy-templates',
		'href'   => admin_url( 'edit.php?post_type=ct_template' ),
		'meta'   => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Templates in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-templates-view-all-new-tab',
		// 'title' => __( 'View All Templates' ),
		'parent' => 'oxy-templates-view-all',
		'href'   => admin_url( 'edit.php?post_type=ct_template' ),
		'meta'   => array(
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title'  => __( 'View All Templates in a New Tab' ),
		),
	)
);

global $wpdb;

$query = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'ct_template' AND post_status='publish' ORDER BY " . self::$orderby . ' ' . self::$order;

$results = $wpdb->get_results( $query );


foreach ( $results as $p ) {
	$ct_template_type = get_post_meta( $p->ID, 'ct_template_type', true );

	$ct_parent_template = get_post_meta( $p->ID, 'ct_parent_template', true );

	$shortcodes = '';

	if ( $ct_parent_template && $ct_parent_template > 0 ) {
		$shortcodes = get_post_meta( $ct_parent_template, 'ct_builder_shortcodes', true );
	}

	$ct_inner = ( $shortcodes && strpos( $shortcodes, '[ct_inner_content' ) !== false ) ? '&ct_inner=true' : '';

	$edit_url = ct_get_post_builder_link( $p->ID ) . $ct_inner;

	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-template' . $p->ID,
			'title'  => $p->post_title,
			'parent' => 'oxy-toolbox-oxy-templates',
			'href'   => esc_url( $edit_url ),
			'meta'   => array(
				'title' => __( 'Edit this Template' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// Links to open in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-template-new-tab' . $p->ID,
			'title'  => $p->post_title,
			'parent' => 'oxy-template' . $p->ID,
			'href'   => esc_url( $edit_url ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Edit this Template with Oxygen in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
} // End foreach().
