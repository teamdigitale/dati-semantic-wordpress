<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// All.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-all',
		'title'  => __( 'All', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php' ),
        'meta'  => array(
			'class' => 'oxy-toolbox-view-all oxy-toolbox-parent-of-mini-child',
		),
	)
);

// View All Plugins in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-all-new-tab',
		'parent' => 'oxy-plugins-all',
		'href'   => admin_url( 'plugins.php' ),
		'meta'   => array(
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			'title'  => __( 'View All Plugins in a New Tab' ),
		),
	)
);

// Active.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-active',
		'title'  => __( 'Active', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php?plugin_status=active' ),
        'meta'   => array(
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Active - New Tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-active-new-tab',
		'parent' => 'oxy-plugins-active',
        'href'   => admin_url( 'plugins.php?plugin_status=active' ),
        'meta'   => array(
            'title'  => __( 'Active plugins list in a new tab' ),
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
		),
	)
);

// Inactive.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-inactive',
		'title'  => __( 'Inactive', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php?plugin_status=inactive' ),
        'meta'   => array(
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Inactive - New Tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-inactive-new-tab',
		'parent' => 'oxy-plugins-inactive',
        'href'   => admin_url( 'plugins.php?plugin_status=inactive' ),
        'meta'   => array(
            'title'  => __( 'Inactive plugins list in a new tab' ),
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
		),
	)
);

// Recently active.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-recently-active',
		'title'  => __( 'Recently Active', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php?plugin_status=recently_activated' ),
        'meta'   => array(
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Recently active - New Tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-recently-active-new-tab',
		'parent' => 'oxy-plugins-recently-active',
        'href'   => admin_url( 'plugins.php?plugin_status=recently_activated' ),
        'meta'   => array(
			'title'  => __( 'Recently active plugins list in a new tab' ),
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
		),
	)
);

// Auto-updates Enabled.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-auto-updates-enabled',
		'title'  => __( 'Auto-updates Enabled', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php?plugin_status=auto-update-enabled' ),
        'meta'   => array(
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Auto-updates Enabled - New Tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-auto-updates-enabled-new-tab',
		'parent' => 'oxy-plugins-auto-updates-enabled',
        'href'   => admin_url( 'plugins.php?plugin_status=auto-update-enabled' ),
        'meta'   => array(
			'title'  => __( 'Go to list of plugins that have auto-updates enabled in a new tab' ),
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
		),
	)
);

// Auto-updates Disbled.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-auto-updates-disabled',
		'title'  => __( 'Auto-updates Disabled', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugins.php?plugin_status=auto-update-disabled' ),
        'meta'   => array(
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Auto-updates Disbled - New Tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-auto-updates-disabled-new-tab',
		'parent' => 'oxy-plugins-auto-updates-disabled',
        'href'   => admin_url( 'plugins.php?plugin_status=auto-update-disabled' ),
        'meta'   => array(
			'title'  => __( 'Go to list of plugins that have auto-updates disabled in a new tab' ),
			'target' => '_blank',
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab'
		),
	)
);

// Add New.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-add-new',
		'title'  => __( 'Add New', 'oxy-toolbox' ),
		'parent' => 'oxy-toolbox-plugins',
        'href'   => admin_url( 'plugin-install.php' ),
        'meta'   => array(
			'title' => __( 'Go to Plugins > Add New' ),
		),
	)
);

// Add New > Upload Plugin.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-plugins-add-new-upload',
		'title'  => __( 'Upload Plugin', 'oxy-toolbox' ),
		'parent' => 'oxy-plugins-add-new',
        'href'   => admin_url( 'plugin-install.php?tab=upload' ),
	)
);
