<?php
/**
 * Habeas CLE Custom Post Types.
 *
 * Here we register the program's 7 entities. We also define the "capability
 * groups" shared by several CPTs, to avoid repeating permissions and keep
 * everything in sync with roles.php.
 *
 * @package Habeas_CLE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The plugin's capability groups (SINGLE SOURCE OF TRUTH).
 *
 * Instead of giving each CPT its own set of permissions, we group them:
 *   - 'content'     : the whole curriculum (Program, Week, Module, Practice
 *                     Scenario, Template, Schedule Event).
 *   - 'case_update' : the case updates (Case Update).
 *
 * roles.php reads THIS SAME function to grant the permissions, so the names
 * never drift apart between "register" and "grant".
 *
 * @return array<string, array{singular:string, plural:string}>
 */
function hcle_capability_types() {
	return array(
		'content'     => array(
			'singular' => 'hcle_content',
			'plural'   => 'hcle_contents',
		),
		'case_update' => array(
			'singular' => 'hcle_case_update',
			'plural'   => 'hcle_case_updates',
		),
	);
}

/**
 * Builds the `capabilities` array for register_post_type().
 *
 * Maps the "meta caps" (edit_post, read_post, delete_post) and the "primitive
 * caps" (edit_posts, publish_posts, ...) to custom names. With
 * map_meta_cap => true, WordPress automatically translates "can they edit THIS
 * post?" using these primitives.
 *
 * @param string $singular Singular capability name (e.g. hcle_content).
 * @param string $plural   Plural capability name (e.g. hcle_contents).
 * @return array<string, string>
 */
function hcle_build_caps( $singular, $plural ) {
	return array(
		// Meta caps (evaluated per concrete post via map_meta_cap).
		'edit_post'              => "edit_{$singular}",
		'read_post'              => "read_{$singular}",
		'delete_post'            => "delete_{$singular}",

		// Primitive caps (granted to roles in roles.php).
		'edit_posts'             => "edit_{$plural}",
		'edit_others_posts'      => "edit_others_{$plural}",
		'publish_posts'          => "publish_{$plural}",
		'read_private_posts'     => "read_private_{$plural}",
		'delete_posts'           => "delete_{$plural}",
		'delete_private_posts'   => "delete_private_{$plural}",
		'delete_published_posts' => "delete_published_{$plural}",
		'delete_others_posts'    => "delete_others_{$plural}",
		'edit_private_posts'     => "edit_private_{$plural}",
		'edit_published_posts'   => "edit_published_{$plural}",
		'create_posts'           => "edit_{$plural}",
	);
}

/**
 * Flat list of ALL the primitive caps of a group.
 *
 * roles.php uses it to grant all of a group's permissions to Instructor and
 * Administrator at once.
 *
 * @param string $plural Plural capability name.
 * @return string[]
 */
function hcle_primitive_caps( $plural ) {
	return array(
		"edit_{$plural}",
		"edit_others_{$plural}",
		"publish_{$plural}",
		"read_private_{$plural}",
		"delete_{$plural}",
		"delete_private_{$plural}",
		"delete_published_{$plural}",
		"delete_others_{$plural}",
		"edit_private_{$plural}",
		"edit_published_{$plural}",
	);
}

/**
 * Definition of the 7 CPTs.
 *
 * Each entry states which capability group it belongs to and that it appears
 * under the admin's "Habeas CLE" parent menu.
 *
 * @return array<string, array<string, mixed>>
 */
function hcle_post_type_definitions() {
	return array(
		// 1) PROGRAM — the top-level container (the 4-week program).
		'hcle_program'     => array(
			'group'    => 'content',
			'singular' => __( 'Program', 'habeas-cle' ),
			'plural'   => __( 'Programs', 'habeas-cle' ),
			'icon'     => 'dashicons-welcome-learn-more',
			'slug'     => 'program',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
		),

		// 2) WEEK — each of the program's 4 weeks.
		'hcle_week'        => array(
			'group'    => 'content',
			'singular' => __( 'Week', 'habeas-cle' ),
			'plural'   => __( 'Weeks', 'habeas-cle' ),
			'icon'     => 'dashicons-calendar-alt',
			'slug'     => 'week',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
		),

		// 3) MODULE — lessons/units within a week.
		'hcle_module'      => array(
			'group'    => 'content',
			'singular' => __( 'Module', 'habeas-cle' ),
			'plural'   => __( 'Modules', 'habeas-cle' ),
			'icon'     => 'dashicons-book',
			'slug'     => 'module',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
		),

		// 4) PRACTICE SCENARIO — exercises with a "model answer".
		'hcle_scenario'    => array(
			'group'    => 'content',
			'singular' => __( 'Practice Scenario', 'habeas-cle' ),
			'plural'   => __( 'Practice Scenarios', 'habeas-cle' ),
			'icon'     => 'dashicons-clipboard',
			'slug'     => 'practice-scenario',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
		),

		// 5) TEMPLATE — downloadable templates (briefs, forms, etc.).
		'hcle_template'    => array(
			'group'    => 'content',
			'singular' => __( 'Template', 'habeas-cle' ),
			'plural'   => __( 'Templates', 'habeas-cle' ),
			'icon'     => 'dashicons-media-document',
			'slug'     => 'template',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		),

		// 6) SCHEDULE EVENT — live sessions / calendar dates.
		'hcle_event'       => array(
			'group'    => 'content',
			'singular' => __( 'Schedule Event', 'habeas-cle' ),
			'plural'   => __( 'Schedule Events', 'habeas-cle' ),
			'icon'     => 'dashicons-clock',
			'slug'     => 'schedule-event',
			'supports' => array( 'title', 'editor', 'custom-fields' ),
		),

		// 7) CASE UPDATE — case-law updates published by instructors.
		//    Has its own permission group.
		'hcle_case_update' => array(
			'group'    => 'case_update',
			'singular' => __( 'Case Update', 'habeas-cle' ),
			'plural'   => __( 'Case Updates', 'habeas-cle' ),
			'icon'     => 'dashicons-megaphone',
			'slug'     => 'case-update',
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		),
	);
}

/**
 * Generates the standard labels array from singular/plural.
 *
 * @param string $singular Singular label.
 * @param string $plural   Plural label.
 * @return array<string, string>
 */
function hcle_build_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'add_new'            => __( 'Add New', 'habeas-cle' ),
		/* translators: %s: singular post type name. */
		'add_new_item'       => sprintf( __( 'Add New %s', 'habeas-cle' ), $singular ),
		/* translators: %s: singular post type name. */
		'edit_item'          => sprintf( __( 'Edit %s', 'habeas-cle' ), $singular ),
		/* translators: %s: singular post type name. */
		'new_item'           => sprintf( __( 'New %s', 'habeas-cle' ), $singular ),
		/* translators: %s: singular post type name. */
		'view_item'          => sprintf( __( 'View %s', 'habeas-cle' ), $singular ),
		/* translators: %s: plural post type name. */
		'search_items'       => sprintf( __( 'Search %s', 'habeas-cle' ), $plural ),
		/* translators: %s: plural post type name (lowercased). */
		'not_found'          => sprintf( __( 'No %s found', 'habeas-cle' ), strtolower( $plural ) ),
		/* translators: %s: plural post type name (lowercased). */
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'habeas-cle' ), strtolower( $plural ) ),
		'all_items'          => $plural,
	);
}

/**
 * Registers all of Habeas CLE's CPTs.
 *
 * Hooked to `init`. Also called directly on plugin activation (before
 * flush_rewrite_rules) so the URLs work right away.
 */
function hcle_register_post_types() {
	$cap_types   = hcle_capability_types();
	$definitions = hcle_post_type_definitions();

	foreach ( $definitions as $post_type => $def ) {
		$caps = $cap_types[ $def['group'] ];

		register_post_type(
			$post_type,
			array(
				'labels'              => hcle_build_labels( $def['singular'], $def['plural'] ),
				'public'              => false, // Doesn't appear in public archives/search...
				'publicly_queryable'  => true,  // ...but DOES have an individual URL on the frontend.
				                                // Who sees it is decided by access-control.php (the login gate),
				                                // not the absence of a URL.
				'show_ui'             => true,  // Editable in the admin.
				'show_in_nav_menus'   => false,
				'exclude_from_search' => true,
				'show_in_rest'        => true,  // Required for the block editor.
				'has_archive'         => false,
				'hierarchical'        => false, // Program>Week>Module relations go through meta.
				'rewrite'         => array(
					'slug'       => $def['slug'],
					'with_front' => false,
				),
				'menu_icon'       => $def['icon'],
				'show_in_menu'    => 'habeas-cle', // They all hang off the parent menu.
				'supports'        => $def['supports'],

				// Custom permissions: map_meta_cap translates the meta caps
				// (edit_post, etc.) using the `capability_type` array.
				'capability_type' => array( $caps['singular'], $caps['plural'] ),
				'map_meta_cap'    => true,
				'capabilities'    => hcle_build_caps( $caps['singular'], $caps['plural'] ),
			)
		);
	}
}
add_action( 'init', 'hcle_register_post_types' );

/**
 * Creates the "Habeas CLE" parent menu in the admin, under which the CPTs are grouped.
 *
 * Requires the 'view_cle_content' capability to see it (Student/Instructor/Admin).
 */
function hcle_register_admin_menu() {
	add_menu_page(
		__( 'Habeas CLE', 'habeas-cle' ),
		__( 'Habeas CLE', 'habeas-cle' ),
		'view_cle_content',
		'habeas-cle',
		'hcle_render_admin_dashboard',
		'dashicons-shield',
		25
	);
}
add_action( 'admin_menu', 'hcle_register_admin_menu' );

/**
 * Simple parent-menu screen (placeholder for now).
 */
function hcle_render_admin_dashboard() {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Habeas CLE', 'habeas-cle' ) . '</h1>';
	echo '<p>' . esc_html__( 'Continuing Legal Education platform — Immigration Habeas Corpus.', 'habeas-cle' ) . '</p>';
	echo '<p>' . esc_html__( 'Use the submenus to manage the curriculum: Programs, Weeks, Modules, Practice Scenarios, Templates, Schedule Events and Case Updates.', 'habeas-cle' ) . '</p>';
	echo '</div>';
}
