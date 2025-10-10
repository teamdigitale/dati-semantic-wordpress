<?php

// View All Fluent Forms.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-fluentforms-view-all',
		'title' => __( 'View All Fluent Forms' ),
		'parent' => 'oxy-toolbox-fluentforms',
		'href'  => admin_url( 'admin.php?page=fluent_forms' ),
		'meta'  => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Fluent Forms in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'    => 'oxy-fluentforms-view-all-new-tab',
		'parent' => 'oxy-fluentforms-view-all',
		'href'  => admin_url( 'admin.php?page=fluent_forms' ),
		'meta'  => array(
			'target'=> '_blank',
			'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title' => __( 'View All Fluent Forms in a New Tab' ),
		),
	)
);

$fluentForms = wpFluent()->table( 'fluentform_forms' )
        ->select( ['id', 'title'] )
        ->orderBy( 'id', 'DESC' )
        ->get();

foreach ( $fluentForms as $p ) {
	
	// Form title.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform' . $p->id,
			'title' => $p->title,
			'parent' => 'oxy-toolbox-fluentforms',
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'editor' ), menu_page_url( 'fluent_forms', false ) ) ),
			'meta'  => array(
				'title' => __( 'Edit this Form' ),
			),
		)
	);

	// Edit.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-edit' . $p->id,
			'title' => __( 'Edit' ),
			'parent' => 'oxy-fluentform' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'editor' ), admin_url( 'admin.php?page=fluent_forms' ) ) ),
			'meta'  => array(
				'class' => 'oxy-toolbox-parent-of-mini-child'
			),
		)
	);
	// Edit in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-edit-new-tab' . $p->id,
			'title' => __( 'Edit in a new tab' ),
			'parent' => 'oxy-fluentform-edit' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'editor' ), admin_url( 'admin.php?page=fluent_forms' ) ) ),
			'meta'  => array(
				'target' => '_blank',
				'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
			),
		)
	);

	// Settings.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-settings' . $p->id,
			'title' => __( 'Settings ' ),
			'parent' => 'oxy-fluentform' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'settings', 'sub_route' => 'form_settings#basic_settings' ), menu_page_url( 'fluent_forms', false ) ) ),
			'meta'  => array(
				'class' => 'oxy-toolbox-parent-of-mini-child'
			),
		)
	);
	// Settings in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-settings-new-tab' . $p->id,
			'title' => __( 'Settings in a new tab' ),
			'parent' => 'oxy-fluentform-settings' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'settings', 'sub_route' => 'form_settings#basic_settings' ), menu_page_url( 'fluent_forms', false ) ) ),
			'meta'  => array(
				'target' => '_blank',
				'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
			),
		)
	);

	// Entries.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-entries' . $p->id,
			'title' => __( 'Entries' ),
			'parent' => 'oxy-fluentform' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'entries' ), menu_page_url( 'fluent_forms', false ) ) ),
			'meta'  => array(
				'class' => 'oxy-toolbox-parent-of-mini-child'
			),
		)
	);
	// Entries in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-entries-new-tab' . $p->id,
			'title' => __( 'Entries in a new tab' ),
			'parent' => 'oxy-fluentform-entries' . $p->id,
			'href'  => esc_url( add_query_arg( array( 'form_id' => $p->id, 'route' => 'entries' ), menu_page_url( 'fluent_forms', false ) ) ),
			'meta'  => array(
				'target' => '_blank',
				'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
			),
		)
	);

	// Preview.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-preview' . $p->id,
			'title' => __( 'Preview' ),
			'parent' => 'oxy-fluentform' . $p->id,
			'href'  => sprintf( site_url( '?fluentform_pages=1&preview_id=%s#ff_preview' ), $p->id ),
			'meta'  => array(
				'class' => 'oxy-toolbox-parent-of-mini-child'
			),
		)
	);
	// Preview in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'oxy-fluentform-preview-new-tab' . $p->id,
			'title' => __( 'Preview in a new tab' ),
			'parent' => 'oxy-fluentform-preview' . $p->id,
			'href'  => sprintf( site_url( '?fluentform_pages=1&preview_id=%s#ff_preview' ), $p->id ),
			'meta'  => array(
				'target' => '_blank',
				'class' => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
			),
		)
	);

}

// Restore original Post Data.
wp_reset_postdata();