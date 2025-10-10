<?php

$wp_admin_bar->remove_node( 'oxygen_admin_bar_menu' );
$wp_admin_bar->remove_node( 'edit_post_template' );
$wp_admin_bar->remove_node( 'edit_template' );

// Edit with Oxygen or Edit <Template>.
if ( function_exists( 'is_oxygen_edit_post_locked' ) && ! is_oxygen_edit_post_locked() ) {

	// check if this post type is set to be ignored.
	$post_type = get_post_type();
	$ignore    = get_option( 'oxygen_vsb_ignore_post_type_' . $post_type, false );

	if ( $ignore == 'true' ) {
		return;
	}

	global $wp_admin_bar, $wp_the_query;

	$post = $wp_the_query->get_queried_object();

	if ( ! is_admin() ) {

		if ( ! oxygen_vsb_current_user_can_access() ) {
			return;
		}

		// $wp_admin_bar->add_menu( array( 'id' => 'oxygen_admin_bar_menu', 'title' => __( 'Oxygen', 'component-theme' ), 'href' => FALSE, 'parent' => 'oxy-toolbox-oxygen' ) );


		$post_id     = false;
		$template    = false;
		$is_template = false;
		// get archive template
		if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

			if ( is_front_page() ) {
				$post_id = get_option( 'page_on_front' );
			} elseif ( is_home() ) {
				$post_id = get_option( 'page_for_posts' );
			} else {
				$template = ct_get_archives_template();

				if ( $template ) {
					$is_template = true;
				}
			}
		}

		if ( $post_id || ( ! $template && is_singular() ) ) {

			if ( $post_id == false ) {
				$post_id = $post->ID;
			}

			$ct_other_template = get_post_meta( $post_id, 'ct_other_template', true );

			$template = false;

			if ( ! empty( $ct_other_template ) && $ct_other_template > 0 ) { // no template is specified
				// try getting default template
				$template = get_post( $ct_other_template );
			} elseif ( $ct_other_template != -1 ) { // try getting default template if not explicitly set to not use any template at all
				if ( intval( $post_id ) == intval( get_option( 'page_on_front' ) ) || intval( $post_id ) == intval( get_option( 'page_for_posts' ) ) ) {
					$template = ct_get_archives_template( $post_id );

					if ( ! $template ) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
						$template = ct_get_posts_template( $post_id );
					}
				} else {
					$template = ct_get_posts_template( $post_id );

				}
			}

			if ( $template ) {
				$is_template = true;
			} else {
				$is_template = false;
			}
		} elseif ( ! $template ) {

			$template = ct_get_archives_template();

			if ( $template ) {
				$is_template = true;
			}
		}

		$contains_inner_content = false;
		if ( $is_template ) {
			$shortcodes = get_post_meta( $template->ID, 'ct_builder_shortcodes', true );
			if ( $shortcodes ) {
				$contains_inner_content = ( strpos( $shortcodes, '[ct_inner_content' ) !== false );
			}
		}

		if ( $is_template ) {
			if ( is_object( $post ) ) {
				$postShortcodes = get_post_meta( $post->ID, 'ct_builder_shortcodes', true );

				if ( $contains_inner_content && $postShortcodes ) {
					$wp_admin_bar->add_node(
						array(
							'id'     => 'edit_post_template',
							'parent' => 'oxy-toolbox-oxygen',
							'title'  => __( 'Edit with Oxygen', 'component-theme' ),
							'href'   => esc_url( ct_get_post_builder_link( $post->ID ) ) . ( ( $shortcodes && strpos(
								$shortcodes,
								'[ct_inner_content'
							) !== false ) ? '&ct_inner=true' : '' ),
						)
					);
				} else {
					$wp_admin_bar->add_node(
						array(
							'id'     => 'edit_template',
							'parent' => 'oxy-toolbox-oxygen',
							'title'  => __( 'Edit ' . $template->post_title . ' Template', 'component-theme' ),
							'href'   => esc_url( get_edit_post_link( $template->ID ) ),
						)
					);
				}
			}
		} else {
			if ( is_object( $post ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'     => 'edit_post_template',
						'parent' => 'oxy-toolbox-oxygen',
						'title'  => __( 'Edit with Oxygen', 'component-theme' ),
						'href'   => esc_url( ct_get_post_builder_link( $post->ID ) ),
					)
				);
			}
		}
	} // end of is_admin().
}

// Home.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-home',
		'title'  => __( 'Home' ),
		'parent' => 'oxy-toolbox-oxygen',
		'href'   => admin_url( 'admin.php?page=ct_dashboard_page' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Home' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Home in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-home-new-tab',
		'title'  => __( 'Home' ),
		'parent' => 'oxy-toolbox-oxygen-home',
		'href'   => admin_url( 'admin.php?page=ct_dashboard_page' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Home in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Templates.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-templates',
		'title'  => __( 'Templates' ),
		'parent' => 'oxy-toolbox-oxygen',
		'href'   => admin_url( 'edit.php?post_type=ct_template' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Templates' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Templates in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-templates-new-tab',
		'title'  => __( 'Templates' ),
		'parent' => 'oxy-toolbox-oxygen-templates',
		'href'   => admin_url( 'edit.php?post_type=ct_template' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Templates in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings',
		'title'  => __( 'Settings' ),
		'parent' => 'oxy-toolbox-oxygen',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings' ),
		),
	)
);
// Settings - General.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-general',
		'title'  => __( 'General' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > General' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - General - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-general-new-tab',
		'title'  => __( 'General' ),
		'parent' => 'oxy-toolbox-oxygen-settings-general',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > General in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - Client Control.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-client-control',
		'title'  => __( 'Client Control' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=client_control' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > Client Control' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - Client Control - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-client-control-new-tab',
		'title'  => __( 'Client Control' ),
		'parent' => 'oxy-toolbox-oxygen-settings-client-control',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=client_control' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > Client Control in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - Security.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-security',
		'title'  => __( 'Security' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=security_manager' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > Security' ),
		),
	)
);
// Settings - Security - Sign All Shortcodes.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-security-sign-all-shortcodes',
		'title'  => __( 'Sign All Shortcodes' ),
		'parent' => 'oxy-toolbox-oxygen-settings-security',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_sign_shortcodes' ),
	)
);

// Settings - SVG Sets.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-svg-sets',
		'title'  => __( 'SVG Sets' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=svg_manager' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > SVG Sets' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - SVG Sets - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-svg-sets-new-tab',
		'title'  => __( 'SVG Sets' ),
		'parent' => 'oxy-toolbox-oxygen-settings-svg-sets',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=svg_manager' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > SVG Sets in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - Typekit.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-typekit',
		'title'  => __( 'Typekit' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=typekit_manager' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > Typekit' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - Typekit - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-typekit-new-tab',
		'title'  => __( 'Typekit' ),
		'parent' => 'oxy-toolbox-oxygen-settings-typekit',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=typekit_manager' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > Typekit in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - License.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-license',
		'title'  => __( 'License' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=license_manager' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > License' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - License - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-license-new-tab',
		'title'  => __( 'License' ),
		'parent' => 'oxy-toolbox-oxygen-settings-license',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=license_manager' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > License in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - CSS Cache.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-cache',
		'title'  => __( 'CSS Cache' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=cache' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > CSS Cache' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - CSS Cache - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-cache-new-tab',
		'title'  => __( 'CSS Cache' ),
		'parent' => 'oxy-toolbox-oxygen-settings-cache',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=cache' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > CSS Cache in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - Bloat Eliminator.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-bloat',
		'title'  => __( 'Bloat Eliminator' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=bloat' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > Bloat Eliminator' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - Bloat Eliminator - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-bloat-new-tab',
		'title'  => __( 'Bloat Eliminator' ),
		'parent' => 'oxy-toolbox-oxygen-settings-bloat',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=bloat' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > Bloat Eliminator in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

// Settings - Library.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-library',
		'title'  => __( 'Library' ),
		'parent' => 'oxy-toolbox-oxygen-settings',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=library_manager' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Settings > Library' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Settings - Library - new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-settings-library-new-tab',
		'title'  => __( 'Library' ),
		'parent' => 'oxy-toolbox-oxygen-settings-library',
		'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=library_manager' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Settings > Library in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

if ( class_exists ( 'Oxygen_Gutenberg' ) ) {
	// Settings - Gutenberg.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-settings-gutenberg',
			'title'  => __( 'Gutenberg' ),
			'parent' => 'oxy-toolbox-oxygen-settings',
			'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=gutenberg' ),
			'meta'   => array(
				'title' => __( 'Go to Oxygen Settings > Gutenberg' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);
	// Settings - Gutenberg - new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-settings-gutenberg-new-tab',
			'title'  => __( 'Gutenberg' ),
			'parent' => 'oxy-toolbox-oxygen-settings-gutenberg',
			'href'   => admin_url( 'admin.php?page=oxygen_vsb_settings&tab=gutenberg' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Oxygen Settings > Gutenberg in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Export & Import.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-oxy-export-import',
		'title'  => __( 'Export & Import' ),
		'parent' => 'oxy-toolbox-oxygen',
		'href'   => admin_url( 'admin.php?page=ct_export_import' ),
		'meta'   => array(
			'title' => __( 'Go to Oxygen Export & Import' ),
			'class' => 'oxy-toolbox-parent-of-mini-child',
		),
	)
);
// Export & Import in a new tab.
$wp_admin_bar->add_node(
	array(
		'id'     => 'oxy-toolbox-oxygen-oxy-export-import-new-tab',
		'title'  => __( 'Export & Import' ),
		'parent' => 'oxy-toolbox-oxygen-oxy-export-import',
		'href'   => admin_url( 'admin.php?page=ct_export_import' ),
		'meta'   => array(
			'target' => '_blank',
			'title'  => __( 'Go to Oxygen Export & Import in a new tab' ),
			'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
		),
	)
);

if ( post_type_exists( 'oxy_user_library' ) ) {
	// Block Library.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-block-library',
			'title'  => __( 'Block Library' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'edit.php?post_type=oxy_user_library' ),
			'meta'   => array(
				'title' => __( 'Go to Oxygen Block Library' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);
	// Block Library in a new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-block-library-new-tab',
			'title'  => __( 'Block Library' ),
			'parent' => 'oxy-toolbox-oxygen-block-library',
			'href'   => admin_url( 'edit.php?post_type=oxy_user_library' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Oxygen Block Library in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Oxy Toolbox.
if ( class_exists( 'OxyToolbox' ) ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-oxy-toolbox',
			'title'  => __( 'Oxy Toolbox' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'admin.php?page=oxy_toolbox_menu' ),
			'meta'   => array(
				'title' => __( 'Go to Oxy Toolbox' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-oxy-toolbox-new-tab',
			'title'  => __( 'Oxy Toolbox' ),
			'parent' => 'oxy-toolbox-oxygen-oxy-toolbox',
			'href'   => admin_url( 'admin.php?page=oxy_toolbox_menu' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Oxy Toolbox in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Oxy Toolbox - Stylesheets Editor.
if ( class_exists( 'OxyToolbox' ) && 1 === intval( get_option( self::$prefix . 'sseditor_', 0 ) ) ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-stylesheets-editor',
			'title'  => __( 'Stylesheets Editor' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'admin.php?page=oxy_toolbox_sseditor_menu' ),
			'meta'   => array(
				'title' => __( 'Go to Stylesheets Editor' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-stylesheets-editor-new-tab',
			'title'  => __( 'Stylesheets Editor' ),
			'parent' => 'oxy-toolbox-oxygen-stylesheets-editor',
			'href'   => admin_url( 'admin.php?page=oxy_toolbox_sseditor_menu' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Stylesheets Editor in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// OxyExtras.
if ( class_exists( 'OxyExtras' ) ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-oxyextras',
			'title'  => __( 'OxyExtras' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'admin.php?page=oxy_extras_menu' ),
			'meta'   => array(
				'title' => __( 'Go to OxyExtras' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-oxyextras-new-tab',
			'title'  => __( 'OxyExtras' ),
			'parent' => 'oxy-toolbox-oxygen-oxyextras',
			'href'   => admin_url( 'admin.php?page=oxy_extras_menu' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to OxyExtras in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Hydrogen Pack.
if ( defined( 'EPXHYDRO_VER' ) ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-hydrogen-pack',
			'title'  => __( 'Hydrogen Pack' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'admin.php?page=hydrogen-pack' ),
			'meta'   => array(
				'title' => __( 'Go to Hydrogen Pack' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-hydrogen-pack-new-tab',
			'title'  => __( 'Hydrogen Pack' ),
			'parent' => 'oxy-toolbox-oxygen-hydrogen-pack',
			'href'   => admin_url( 'admin.php?page=hydrogen-pack' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Hydrogen Pack in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Swiss Knife.
if ( defined( 'SWK_admin' ) ) {
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-swiss-knife',
			'title'  => __( 'Swiss Knife' ),
			'parent' => 'oxy-toolbox-oxygen',
			'href'   => admin_url( 'admin.php?page=swiss_knife' ),
			'meta'   => array(
				'title' => __( 'Go to Swiss Knife' ),
				'class' => 'oxy-toolbox-parent-of-mini-child',
			),
		)
	);

	// new tab.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'oxy-toolbox-oxygen-swiss-knife-new-tab',
			'title'  => __( 'Swiss Knife' ),
			'parent' => 'oxy-toolbox-oxygen-swiss-knife',
			'href'   => admin_url( 'admin.php?page=swiss_knife' ),
			'meta'   => array(
				'target' => '_blank',
				'title'  => __( 'Go to Swiss Knife in a new tab' ),
				'class'  => 'oxy-toolbox-mini-child oxy-toolbox-mini-child-new-tab',
			),
		)
	);
}

// Restore original Post Data.
wp_reset_postdata();
