<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site-specific MCP abilities for content and theme workflows.
 */
final class Ben_Montgomery_Mcp_Abilities {
	private const ABILITY_CATEGORY = 'ben-montgomery';
	private const RESOURCE_SCHEME  = 'wordpress://ben-montgomery';

	/**
	 * Register site abilities.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( ! function_exists( 'wp_register_ability' ) || ! function_exists( 'wp_has_ability' ) ) {
			return;
		}

		self::register_category();

		self::register_list_content_ability();
		self::register_get_content_ability();
		self::register_update_content_ability();
		self::register_list_theme_artifacts_ability();
		self::register_get_theme_artifact_ability();
		self::register_sync_site_editor_ability();
		self::register_site_map_resource();
		self::register_theme_structure_resource();
		self::register_update_page_prompt();
		self::register_theme_edit_prompt();
	}

	/**
	 * Shared read permission check.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return bool|\WP_Error
	 */
	public static function can_read( $input = null ) {
		unset( $input );

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'authentication_required', 'User must be authenticated.' );
		}

		if ( current_user_can( 'edit_posts' ) ) {
			return true;
		}

		return new WP_Error( 'insufficient_capability', 'User must be able to edit posts.' );
	}

	/**
	 * Shared write permission check.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return bool|\WP_Error
	 */
	public static function can_write( $input = null ) {
		unset( $input );

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'authentication_required', 'User must be authenticated.' );
		}

		if ( current_user_can( 'edit_theme_options' ) || current_user_can( 'edit_pages' ) ) {
			return true;
		}

		return new WP_Error( 'insufficient_capability', 'User must be able to edit pages or theme options.' );
	}

	/**
	 * List editable content items.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function execute_list_content( $input = null ) {
		$input      = is_array( $input ) ? $input : array();
		$post_types = $input['post_types'] ?? array( 'page', 'post', 'wp_navigation', 'attachment' );
		$statuses   = $input['statuses'] ?? array( 'publish', 'draft', 'future', 'pending', 'private', 'inherit' );
		$limit      = isset( $input['limit'] ) ? max( 1, min( 100, (int) $input['limit'] ) ) : 25;

		if ( is_string( $post_types ) ) {
			$post_types = array( $post_types );
		}

		if ( is_string( $statuses ) ) {
			$statuses = array( $statuses );
		}

		$post_types = array_values( array_filter( array_map( 'sanitize_key', (array) $post_types ) ) );
		$statuses   = array_values( array_filter( array_map( 'sanitize_key', (array) $statuses ) ) );

		if ( empty( $post_types ) ) {
			$post_types = array( 'page', 'post', 'wp_navigation', 'attachment' );
		}

		if ( empty( $statuses ) ) {
			$statuses = array( 'publish', 'draft', 'future', 'pending', 'private', 'inherit' );
		}

		$query = new WP_Query(
			array(
				'post_type'              => $post_types,
				'post_status'            => $statuses,
				'posts_per_page'         => $limit,
				's'                      => isset( $input['search'] ) ? sanitize_text_field( (string) $input['search'] ) : '',
				'orderby'                => 'modified',
				'order'                  => 'DESC',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		$items = array_map(
			static function ( WP_Post $post ): array {
				return self::format_content_summary( $post );
			},
			$query->posts
		);

		return array(
			'items'  => $items,
			'total'  => count( $items ),
			'filter' => array(
				'post_types' => $post_types,
				'statuses'   => $statuses,
				'search'     => isset( $input['search'] ) ? sanitize_text_field( (string) $input['search'] ) : '',
				'limit'      => $limit,
			),
		);
	}

	/**
	 * Get a content item by ID or slug.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function execute_get_content( $input = null ) {
		$input = is_array( $input ) ? $input : array();
		$post  = self::find_post_from_input( $input );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		return array(
			'item' => self::format_content_detail( $post ),
		);
	}

	/**
	 * Update an existing content item.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function execute_update_content( $input = null ) {
		$input = is_array( $input ) ? $input : array();
		$post  = self::find_post_from_input(
			array(
				'id'        => $input['id'] ?? null,
				'slug'      => $input['lookup_slug'] ?? '',
				'post_type' => $input['post_type'] ?? 'any',
			)
		);

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return new WP_Error( 'insufficient_capability', 'User cannot edit the requested post.' );
		}

		$updates = array(
			'ID' => $post->ID,
		);

		if ( array_key_exists( 'title', $input ) ) {
			$updates['post_title'] = (string) $input['title'];
		}

		if ( array_key_exists( 'content', $input ) ) {
			$updates['post_content'] = (string) $input['content'];
		}

		if ( array_key_exists( 'excerpt', $input ) ) {
			$updates['post_excerpt'] = (string) $input['excerpt'];
		}

		if ( array_key_exists( 'status', $input ) ) {
			$updates['post_status'] = sanitize_key( (string) $input['status'] );
		}

		if ( array_key_exists( 'slug', $input ) ) {
			$updates['post_name'] = sanitize_title( (string) $input['slug'] );
		}

		if ( 1 === count( $updates ) ) {
			return new WP_Error( 'no_updates_requested', 'Provide at least one field to update.' );
		}

		$updated_id = wp_update_post( wp_slash( $updates ), true );

		if ( is_wp_error( $updated_id ) ) {
			return $updated_id;
		}

		$updated_post = get_post( $updated_id );

		if ( ! $updated_post instanceof WP_Post ) {
			return new WP_Error( 'post_not_found', 'Updated post could not be loaded.' );
		}

		return array(
			'updated' => true,
			'item'    => self::format_content_detail( $updated_post ),
		);
	}

	/**
	 * List theme templates, parts, patterns, and style exports.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>
	 */
	public static function execute_list_theme_artifacts( $input = null ): array {
		$input = is_array( $input ) ? $input : array();
		$type  = isset( $input['type'] ) ? sanitize_key( (string) $input['type'] ) : '';
		$items = self::get_theme_artifact_catalog();

		if ( '' !== $type ) {
			$items = array_values(
				array_filter(
					$items,
					static function ( array $item ) use ( $type ): bool {
						return $item['type'] === $type;
					}
				)
			);
		}

		$response_items = array_map(
			static function ( array $item ): array {
				unset( $item['absolute_path'] );
				return $item;
			},
			$items
		);

		return array(
			'items'  => $response_items,
			'total'  => count( $response_items ),
			'theme'  => wp_get_theme()->get_stylesheet(),
			'filter' => array(
				'type' => $type,
			),
		);
	}

	/**
	 * Read a theme artifact from disk.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function execute_get_theme_artifact( $input = null ) {
		$input = is_array( $input ) ? $input : array();
		$item  = self::resolve_theme_artifact( $input );

		if ( is_wp_error( $item ) ) {
			return $item;
		}

		$content = file_get_contents( $item['absolute_path'] );

		if ( false === $content ) {
			return new WP_Error( 'artifact_read_failed', 'Unable to read theme artifact from disk.' );
		}

		unset( $item['absolute_path'] );
		$item['content'] = $content;

		return array(
			'artifact' => $item,
		);
	}

	/**
	 * Sync Site Editor data into files.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>|WP_Error
	 */
	public static function execute_sync_site_editor( $input = null ) {
		$input  = is_array( $input ) ? $input : array();
		$result = Ben_Montgomery_Site_Editor_Sync_Service::export(
			array(
				'templates' => ! empty( $input['templates'] ),
				'parts'     => ! empty( $input['parts'] ),
				'styles'    => ! empty( $input['styles'] ),
			)
		);

		return array(
			'synced'   => true,
			'counts'   => $result['counts'],
			'exported' => $result['exported'],
			'messages' => $result['messages'],
			'warnings' => $result['warnings'],
		);
	}

	/**
	 * Return a site map resource payload.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<int, array<string, string>>
	 */
	public static function execute_site_map_resource( $input = null ): array {
		unset( $input );

		$data = array(
			'theme'            => wp_get_theme()->get_stylesheet(),
			'pages'            => self::execute_list_content( array( 'post_types' => array( 'page' ), 'limit' => 50 ) )['items'],
			'posts'            => self::execute_list_content( array( 'post_types' => array( 'post' ), 'limit' => 50 ) )['items'],
			'navigation_items' => self::execute_list_content( array( 'post_types' => array( 'wp_navigation' ), 'limit' => 50 ) )['items'],
		);

		return array(
			array(
				'uri'      => self::RESOURCE_SCHEME . '/site-map',
				'mimeType' => 'application/json',
				'text'     => (string) wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ),
			),
		);
	}

	/**
	 * Return a theme structure resource payload.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<int, array<string, string>>
	 */
	public static function execute_theme_structure_resource( $input = null ): array {
		unset( $input );

		$data = array(
			'theme'     => wp_get_theme()->get_stylesheet(),
			'artifacts' => self::execute_list_theme_artifacts()['items'],
		);

		return array(
			array(
				'uri'      => self::RESOURCE_SCHEME . '/theme-structure',
				'mimeType' => 'application/json',
				'text'     => (string) wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ),
			),
		);
	}

	/**
	 * Prompt for page editing workflow.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>
	 */
	public static function execute_update_page_prompt( $input = null ): array {
		$input      = is_array( $input ) ? $input : array();
		$page_slug  = sanitize_title( (string) ( $input['page_slug'] ?? '' ) );
		$goal       = sanitize_text_field( (string) ( $input['goal'] ?? 'Update page content while preserving block-theme structure.' ) );
		$page_label = '' !== $page_slug ? $page_slug : 'the target page';

		return array(
			'description' => 'Workflow guidance for updating a WordPress page with Codex.',
			'messages'    => array(
				array(
					'role'    => 'user',
					'content' => array(
						'type' => 'text',
						'text' => sprintf(
							"Goal: %s\n\nUse `ben-montgomery-list-content` to find %s, then inspect it with `ben-montgomery-get-content`. If the page depends on specific theme patterns or templates, inspect them with `ben-montgomery-list-theme-artifacts` and `ben-montgomery-get-theme-artifact` before editing. Apply content updates with `ben-montgomery-update-content`, preserving valid block markup and existing reusable structure.",
							$goal,
							$page_label
						),
					),
				),
			),
		);
	}

	/**
	 * Prompt for theme editing workflow.
	 *
	 * @param array<string, mixed>|null $input Ability input.
	 * @return array<string, mixed>
	 */
	public static function execute_theme_edit_prompt( $input = null ): array {
		$input = is_array( $input ) ? $input : array();
		$goal  = sanitize_text_field( (string) ( $input['goal'] ?? 'Update the active block theme safely.' ) );

		return array(
			'description' => 'Workflow guidance for editing theme files and Site Editor state.',
			'messages'    => array(
				array(
					'role'    => 'user',
					'content' => array(
						'type' => 'text',
						'text' => sprintf(
							"Goal: %s\n\nStart with `ben-montgomery-list-theme-artifacts` to identify the relevant template, part, pattern, or styles export, then read the exact file with `ben-montgomery-get-theme-artifact`. Prefer theme.json, templates, parts, and patterns over PHP when possible. If Site Editor changes were made in wp-admin, finish by running `ben-montgomery-sync-site-editor` so database-only template and style changes are exported back into the repo.",
							$goal
						),
					),
				),
			),
		);
	}

	/**
	 * Register the list content ability.
	 *
	 * @return void
	 */
	private static function register_list_content_ability(): void {
		self::register_ability(
			'ben-montgomery/list-content',
			array(
				'label'               => 'List Content',
				'description'         => 'List posts, pages, navigation items, or media from WordPress for agentic editing workflows.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_types' => array(
							'type'        => 'array',
							'description' => 'Post types to query, such as page, post, wp_navigation, or attachment.',
							'items'       => array( 'type' => 'string' ),
						),
						'statuses'   => array(
							'type'        => 'array',
							'description' => 'Post statuses to include.',
							'items'       => array( 'type' => 'string' ),
						),
						'search'     => array(
							'type'        => 'string',
							'description' => 'Optional search term.',
						),
						'limit'      => array(
							'type'        => 'integer',
							'description' => 'Maximum number of items to return.',
						),
					),
				),
				'output_schema'       => self::get_list_content_output_schema(),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_list_content' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'tool', false ),
			)
		);
	}

	/**
	 * Register the get content ability.
	 *
	 * @return void
	 */
	private static function register_get_content_ability(): void {
		self::register_ability(
			'ben-montgomery/get-content',
			array(
				'label'               => 'Get Content',
				'description'         => 'Read a specific post, page, navigation item, or attachment in detail, including raw block content.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'id'        => array(
							'type'        => 'integer',
							'description' => 'Post ID to load.',
						),
						'slug'      => array(
							'type'        => 'string',
							'description' => 'Post slug to load when ID is not provided.',
						),
						'post_type' => array(
							'type'        => 'string',
							'description' => 'Optional post type hint used with slug lookups.',
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'item' => self::get_content_item_schema(),
					),
					'required'   => array( 'item' ),
				),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_get_content' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'tool', true ),
			)
		);
	}

	/**
	 * Register the update content ability.
	 *
	 * @return void
	 */
	private static function register_update_content_ability(): void {
		self::register_ability(
			'ben-montgomery/update-content',
			array(
				'label'               => 'Update Content',
				'description'         => 'Update an existing post or page title, slug, excerpt, status, or raw block content.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'id'          => array(
							'type'        => 'integer',
							'description' => 'Post ID to update.',
						),
						'lookup_slug' => array(
							'type'        => 'string',
							'description' => 'Existing post slug used for lookup when ID is not provided.',
						),
						'post_type'   => array(
							'type'        => 'string',
							'description' => 'Optional post type hint used with slug lookups.',
						),
						'title'       => array(
							'type'        => 'string',
							'description' => 'Updated post title.',
						),
						'content'     => array(
							'type'        => 'string',
							'description' => 'Updated raw post content, including block markup.',
						),
						'excerpt'     => array(
							'type'        => 'string',
							'description' => 'Updated excerpt.',
						),
						'status'      => array(
							'type'        => 'string',
							'description' => 'Updated post status.',
						),
						'slug'        => array(
							'type'        => 'string',
							'description' => 'Updated post slug.',
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'updated' => array( 'type' => 'boolean' ),
						'item'    => self::get_content_item_schema(),
					),
					'required'   => array( 'updated', 'item' ),
				),
				'permission_callback' => array( self::class, 'can_write' ),
				'execute_callback'    => array( self::class, 'execute_update_content' ),
				'meta'                => self::get_mcp_meta( false, true, false, 'tool', true ),
			)
		);
	}

	/**
	 * Register the list theme artifacts ability.
	 *
	 * @return void
	 */
	private static function register_list_theme_artifacts_ability(): void {
		self::register_ability(
			'ben-montgomery/list-theme-artifacts',
			array(
				'label'               => 'List Theme Artifacts',
				'description'         => 'List templates, template parts, patterns, and style exports in the active Ben Montgomery theme.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'type' => array(
							'type'        => 'string',
							'description' => 'Optional artifact type filter: template, part, pattern, or style.',
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'items'  => array(
							'type'  => 'array',
							'items' => self::get_theme_artifact_schema(),
						),
						'total'  => array( 'type' => 'integer' ),
						'theme'  => array( 'type' => 'string' ),
						'filter' => array( 'type' => 'object' ),
					),
					'required'   => array( 'items', 'total', 'theme' ),
				),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_list_theme_artifacts' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'tool', false ),
			)
		);
	}

	/**
	 * Register the get theme artifact ability.
	 *
	 * @return void
	 */
	private static function register_get_theme_artifact_ability(): void {
		self::register_ability(
			'ben-montgomery/get-theme-artifact',
			array(
				'label'               => 'Get Theme Artifact',
				'description'         => 'Read the contents of a specific theme template, part, pattern, or style export file.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'type'          => array(
							'type'        => 'string',
							'description' => 'Artifact type: template, part, pattern, or style.',
						),
						'name'          => array(
							'type'        => 'string',
							'description' => 'Artifact name without extension.',
						),
						'relative_path' => array(
							'type'        => 'string',
							'description' => 'Relative path to the artifact from the theme root.',
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'artifact' => array(
							'type'       => 'object',
							'properties' => array(
								'type'          => array( 'type' => 'string' ),
								'name'          => array( 'type' => 'string' ),
								'relative_path' => array( 'type' => 'string' ),
								'mime_type'     => array( 'type' => 'string' ),
								'content'       => array( 'type' => 'string' ),
							),
							'required'   => array( 'type', 'name', 'relative_path', 'mime_type', 'content' ),
						),
					),
					'required'   => array( 'artifact' ),
				),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_get_theme_artifact' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'tool', false ),
			)
		);
	}

	/**
	 * Register the Site Editor sync ability.
	 *
	 * @return void
	 */
	private static function register_sync_site_editor_ability(): void {
		self::register_ability(
			'ben-montgomery/sync-site-editor',
			array(
				'label'               => 'Sync Site Editor',
				'description'         => 'Export customized templates, parts, and global styles from the Site Editor database into theme files.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'templates' => array(
							'type'        => 'boolean',
							'description' => 'Export customized templates.',
						),
						'parts'     => array(
							'type'        => 'boolean',
							'description' => 'Export customized template parts.',
						),
						'styles'    => array(
							'type'        => 'boolean',
							'description' => 'Export customized global styles.',
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'synced'   => array( 'type' => 'boolean' ),
						'counts'   => array( 'type' => 'object' ),
						'exported' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'messages' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'warnings' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
					),
					'required'   => array( 'synced', 'counts', 'exported', 'messages', 'warnings' ),
				),
				'permission_callback' => array( self::class, 'can_write' ),
				'execute_callback'    => array( self::class, 'execute_sync_site_editor' ),
				'meta'                => self::get_mcp_meta( false, true, false, 'tool', true ),
			)
		);
	}

	/**
	 * Register the site map resource.
	 *
	 * @return void
	 */
	private static function register_site_map_resource(): void {
		self::register_ability(
			'ben-montgomery/site-map-resource',
			array(
				'label'               => 'Site Map',
				'description'         => 'A live JSON snapshot of pages, posts, and navigation items for the active site.',
				'category'            => self::ABILITY_CATEGORY,
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_site_map_resource' ),
				'meta'                => array_merge(
					self::get_mcp_meta( true, false, true, 'resource', true ),
					array(
						'uri' => self::RESOURCE_SCHEME . '/site-map',
					)
				),
			)
		);
	}

	/**
	 * Register the theme structure resource.
	 *
	 * @return void
	 */
	private static function register_theme_structure_resource(): void {
		self::register_ability(
			'ben-montgomery/theme-structure-resource',
			array(
				'label'               => 'Theme Structure',
				'description'         => 'A live JSON index of the active theme templates, parts, patterns, and styles.',
				'category'            => self::ABILITY_CATEGORY,
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_theme_structure_resource' ),
				'meta'                => array_merge(
					self::get_mcp_meta( true, false, true, 'resource', false ),
					array(
						'uri' => self::RESOURCE_SCHEME . '/theme-structure',
					)
				),
			)
		);
	}

	/**
	 * Register the update page prompt.
	 *
	 * @return void
	 */
	private static function register_update_page_prompt(): void {
		self::register_ability(
			'ben-montgomery/update-page-prompt',
			array(
				'label'               => 'Update Page Workflow',
				'description'         => 'Structured guidance for updating a WordPress page with the Ben Montgomery MCP abilities.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'page_slug' => array(
							'type'        => 'string',
							'description' => 'Slug of the page you plan to update.',
						),
						'goal'      => array(
							'type'        => 'string',
							'description' => 'The user-visible goal of the update.',
						),
					),
				),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_update_page_prompt' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'prompt', false ),
			)
		);
	}

	/**
	 * Register the theme edit prompt.
	 *
	 * @return void
	 */
	private static function register_theme_edit_prompt(): void {
		self::register_ability(
			'ben-montgomery/theme-edit-prompt',
			array(
				'label'               => 'Theme Edit Workflow',
				'description'         => 'Structured guidance for editing block theme files and syncing Site Editor changes.',
				'category'            => self::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'goal' => array(
							'type'        => 'string',
							'description' => 'The theme change you want to make.',
						),
					),
				),
				'permission_callback' => array( self::class, 'can_read' ),
				'execute_callback'    => array( self::class, 'execute_theme_edit_prompt' ),
				'meta'                => self::get_mcp_meta( true, false, true, 'prompt', false ),
			)
		);
	}

	/**
	 * Build shared MCP metadata.
	 *
	 * @param bool   $readonly Whether the ability is read-only.
	 * @param bool   $destructive Whether the ability is destructive.
	 * @param bool   $idempotent Whether the ability is idempotent.
	 * @param string $type MCP component type.
	 * @param bool   $public Whether the ability should be exposed to MCP clients.
	 * @return array<string, mixed>
	 */
	private static function get_mcp_meta( bool $readonly, bool $destructive, bool $idempotent, string $type, bool $public ): array {
		return array(
			'mcp'         => array(
				'public' => $public,
				'type'   => $type,
			),
			'annotations' => array(
				'readonly'    => $readonly,
				'destructive' => $destructive,
				'idempotent'  => $idempotent,
			),
		);
	}

	/**
	 * Register the site category through the current Abilities API state.
	 *
	 * @return void
	 */
	private static function register_category(): void {
		$registry = WP_Ability_Categories_Registry::get_instance();

		if ( ! $registry || $registry->is_registered( self::ABILITY_CATEGORY ) ) {
			return;
		}

		$args = array(
			'label'       => 'Ben Montgomery',
			'description' => 'Site-specific content and theme workflows for Codex.',
		);

		if ( doing_action( 'wp_abilities_api_init' ) && function_exists( 'wp_register_ability_category' ) ) {
			wp_register_ability_category( self::ABILITY_CATEGORY, $args );
			return;
		}

		$registry->register( self::ABILITY_CATEGORY, $args );
	}

	/**
	 * Register an ability through the current Abilities API state.
	 *
	 * @param string               $name Ability name.
	 * @param array<string, mixed> $args Ability arguments.
	 * @return void
	 */
	private static function register_ability( string $name, array $args ): void {
		$registry = WP_Abilities_Registry::get_instance();

		if ( ! $registry || $registry->is_registered( $name ) ) {
			return;
		}

		if ( doing_action( 'wp_abilities_api_init' ) ) {
			wp_register_ability( $name, $args );
			return;
		}

		$registry->register( $name, $args );
	}

	/**
	 * Format a content summary item.
	 *
	 * @param WP_Post $post Source post.
	 * @return array<string, mixed>
	 */
	private static function format_content_summary( WP_Post $post ): array {
		return array(
			'id'            => $post->ID,
			'post_type'     => $post->post_type,
			'post_status'   => $post->post_status,
			'slug'          => $post->post_name,
			'title'         => get_the_title( $post ),
			'modified_gmt'  => $post->post_modified_gmt,
			'link'          => get_permalink( $post ),
			'template_slug' => (string) get_page_template_slug( $post ),
		);
	}

	/**
	 * Format a detailed content item.
	 *
	 * @param WP_Post $post Source post.
	 * @return array<string, mixed>
	 */
	private static function format_content_detail( WP_Post $post ): array {
		$summary               = self::format_content_summary( $post );
		$summary['excerpt']    = $post->post_excerpt;
		$summary['content']    = $post->post_content;
		$summary['rendered']   = apply_filters( 'the_content', $post->post_content );
		$summary['parent_id']  = (int) $post->post_parent;
		$summary['menu_order'] = (int) $post->menu_order;

		return $summary;
	}

	/**
	 * Locate a post from ability input.
	 *
	 * @param array<string, mixed> $input Ability input.
	 * @return WP_Post|WP_Error
	 */
	private static function find_post_from_input( array $input ) {
		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );
			if ( $post instanceof WP_Post ) {
				return $post;
			}
		}

		$slug = sanitize_title( (string) ( $input['slug'] ?? '' ) );
		if ( '' === $slug ) {
			return new WP_Error( 'missing_post_identifier', 'Provide a post ID or slug.' );
		}

		$post_type = isset( $input['post_type'] ) ? sanitize_key( (string) $input['post_type'] ) : 'any';
		$post      = get_page_by_path( $slug, OBJECT, $post_type );

		if ( $post instanceof WP_Post ) {
			return $post;
		}

		return new WP_Error( 'post_not_found', 'The requested content item was not found.' );
	}

	/**
	 * Return the artifact catalog.
	 *
	 * @return array<int, array<string, string>>
	 */
	private static function get_theme_artifact_catalog(): array {
		$groups = array(
			'template' => array( 'directory' => 'templates', 'pattern' => '*.html' ),
			'part'     => array( 'directory' => 'parts', 'pattern' => '*.html' ),
			'pattern'  => array( 'directory' => 'patterns', 'pattern' => '*.php' ),
			'style'    => array( 'directory' => 'styles', 'pattern' => '*.{json,css}' ),
		);

		$items = array();

		foreach ( $groups as $type => $config ) {
			$directory = trailingslashit( get_stylesheet_directory() ) . $config['directory'];
			$files     = glob( $directory . '/' . $config['pattern'], GLOB_BRACE );

			if ( false === $files ) {
				continue;
			}

			sort( $files );

			foreach ( $files as $file ) {
				$relative_path = ltrim( str_replace( trailingslashit( get_stylesheet_directory() ), '', $file ), '/' );
				$items[]       = array(
					'type'          => $type,
					'name'          => pathinfo( $file, PATHINFO_FILENAME ),
					'relative_path' => $relative_path,
					'mime_type'     => self::detect_mime_type( $relative_path ),
					'absolute_path' => $file,
				);
			}
		}

		return $items;
	}

	/**
	 * Resolve a theme artifact by path or by type/name.
	 *
	 * @param array<string, mixed> $input Ability input.
	 * @return array<string, string>|WP_Error
	 */
	private static function resolve_theme_artifact( array $input ) {
		$catalog = self::get_theme_artifact_catalog();

		if ( ! empty( $input['relative_path'] ) ) {
			$relative_path = ltrim( str_replace( '\\', '/', (string) $input['relative_path'] ), '/' );

			foreach ( $catalog as $item ) {
				if ( $item['relative_path'] === $relative_path ) {
					return $item;
				}
			}
		}

		$type = sanitize_key( (string) ( $input['type'] ?? '' ) );
		$name = sanitize_title( (string) ( $input['name'] ?? '' ) );

		if ( '' === $type || '' === $name ) {
			return new WP_Error( 'missing_artifact_identifier', 'Provide relative_path or both type and name.' );
		}

		foreach ( $catalog as $item ) {
			if ( $item['type'] === $type && $item['name'] === $name ) {
				return $item;
			}
		}

		return new WP_Error( 'artifact_not_found', 'The requested theme artifact was not found.' );
	}

	/**
	 * Detect a simple MIME type for the artifact path.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return string
	 */
	private static function detect_mime_type( string $relative_path ): string {
		$extension = strtolower( pathinfo( $relative_path, PATHINFO_EXTENSION ) );

		switch ( $extension ) {
			case 'html':
				return 'text/html';
			case 'json':
				return 'application/json';
			case 'css':
				return 'text/css';
			case 'php':
				return 'application/x-httpd-php';
			default:
				return 'text/plain';
		}
	}

	/**
	 * Shared schema for list responses.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_list_content_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'items'  => array(
					'type'  => 'array',
					'items' => self::get_content_summary_schema(),
				),
				'total'  => array( 'type' => 'integer' ),
				'filter' => array( 'type' => 'object' ),
			),
			'required'   => array( 'items', 'total', 'filter' ),
		);
	}

	/**
	 * Content summary schema.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_content_summary_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'            => array( 'type' => 'integer' ),
				'post_type'     => array( 'type' => 'string' ),
				'post_status'   => array( 'type' => 'string' ),
				'slug'          => array( 'type' => 'string' ),
				'title'         => array( 'type' => 'string' ),
				'modified_gmt'  => array( 'type' => 'string' ),
				'link'          => array( 'type' => 'string' ),
				'template_slug' => array( 'type' => 'string' ),
			),
			'required'   => array( 'id', 'post_type', 'post_status', 'slug', 'title', 'modified_gmt', 'link', 'template_slug' ),
		);
	}

	/**
	 * Detailed content item schema.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_content_item_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'            => array( 'type' => 'integer' ),
				'post_type'     => array( 'type' => 'string' ),
				'post_status'   => array( 'type' => 'string' ),
				'slug'          => array( 'type' => 'string' ),
				'title'         => array( 'type' => 'string' ),
				'modified_gmt'  => array( 'type' => 'string' ),
				'link'          => array( 'type' => 'string' ),
				'template_slug' => array( 'type' => 'string' ),
				'excerpt'       => array( 'type' => 'string' ),
				'content'       => array( 'type' => 'string' ),
				'rendered'      => array( 'type' => 'string' ),
				'parent_id'     => array( 'type' => 'integer' ),
				'menu_order'    => array( 'type' => 'integer' ),
			),
			'required'   => array( 'id', 'post_type', 'post_status', 'slug', 'title', 'modified_gmt', 'link', 'template_slug', 'excerpt', 'content', 'rendered', 'parent_id', 'menu_order' ),
		);
	}

	/**
	 * Theme artifact schema.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_theme_artifact_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'type'          => array( 'type' => 'string' ),
				'name'          => array( 'type' => 'string' ),
				'relative_path' => array( 'type' => 'string' ),
				'mime_type'     => array( 'type' => 'string' ),
			),
			'required'   => array( 'type', 'name', 'relative_path', 'mime_type' ),
		);
	}
}
