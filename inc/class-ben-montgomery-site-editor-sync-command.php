<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Syncs Site Editor templates, template parts, and style exports back into the theme.
 */
final class Ben_Montgomery_Site_Editor_Sync_Command {
	/**
	 * Exports customized Site Editor entities back into the theme repository.
	 *
	 * ## OPTIONS
	 *
	 * [--templates]
	 * : Export customized templates into the theme's templates directory.
	 *
	 * [--parts]
	 * : Export customized template parts into the theme's parts directory.
	 *
	 * [--styles]
	 * : Export customized global styles JSON and custom CSS into the theme's styles directory.
	 *
	 * ## EXAMPLES
	 *
	 *     wp bm sync-site-editor
	 *     wp bm sync-site-editor --templates --parts
	 *
	 * @param array<string>          $args       Positional arguments.
	 * @param array<string, string>  $assoc_args Flag arguments.
	 */
	public function __invoke( array $args, array $assoc_args ): void {
		$export_templates = isset( $assoc_args['templates'] );
		$export_parts     = isset( $assoc_args['parts'] );
		$export_styles    = isset( $assoc_args['styles'] );

		if ( ! $export_templates && ! $export_parts && ! $export_styles ) {
			$export_templates = true;
			$export_parts     = true;
			$export_styles    = true;
		}

		$counts = array(
			'templates' => 0,
			'parts'     => 0,
			'styles'    => 0,
		);

		if ( $export_templates ) {
			$counts['templates'] = $this->export_block_entities( 'wp_template', 'templates' );
		}

		if ( $export_parts ) {
			$counts['parts'] = $this->export_block_entities( 'wp_template_part', 'parts' );
		}

		if ( $export_styles ) {
			$counts['styles'] = $this->export_styles();
		}

		WP_CLI::success(
			sprintf(
				'Site Editor sync complete. Templates: %1$d, Parts: %2$d, Style exports: %3$d.',
				$counts['templates'],
				$counts['parts'],
				$counts['styles']
			)
		);
	}

	private function export_block_entities( string $post_type, string $directory ): int {
		$written   = 0;
		$theme_dir = trailingslashit( get_stylesheet_directory() ) . $directory;
		$entities  = get_block_templates( array(), $post_type );

		wp_mkdir_p( $theme_dir );

		foreach ( $entities as $entity ) {
			if ( 'custom' !== $entity->source ) {
				continue;
			}

			$relative_path = $directory . '/' . $entity->slug . '.html';
			$file_path     = trailingslashit( get_stylesheet_directory() ) . $relative_path;
			$content       = rtrim( (string) $entity->content ) . PHP_EOL;

			if ( false === file_put_contents( $file_path, $content ) ) {
				WP_CLI::warning( sprintf( 'Unable to write %s.', $relative_path ) );
				continue;
			}

			++$written;
			WP_CLI::log( sprintf( 'Exported %s.', $relative_path ) );
		}

		if ( 0 === $written ) {
			WP_CLI::log( sprintf( 'No customized %s records found to export.', $post_type ) );
		}

		return $written;
	}

	private function export_styles(): int {
		$styles_dir = trailingslashit( get_stylesheet_directory() ) . 'styles';
		$post_id    = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		wp_mkdir_p( $styles_dir );

		if ( ! $post_id ) {
			WP_CLI::log( 'No Site Editor global styles post exists yet.' );
			return 0;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post || '' === trim( (string) $post->post_content ) ) {
			WP_CLI::log( 'No customized Site Editor global styles found to export.' );
			return 0;
		}

		$content = (string) $post->post_content;
		$decoded = json_decode( $content, true );

		if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
			$content = (string) wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
		} else {
			$content = $content . PHP_EOL;
		}

		if ( false === file_put_contents( $styles_dir . '/site-editor-export.json', $content ) ) {
			WP_CLI::warning( 'Unable to write styles/site-editor-export.json.' );
			return 0;
		}

		$custom_css = trim( wp_get_global_stylesheet( array( 'custom-css' ) ) );

		if ( '' !== $custom_css ) {
			file_put_contents( $styles_dir . '/site-editor-custom.css', $custom_css . PHP_EOL );
			WP_CLI::log( 'Exported styles/site-editor-custom.css.' );
		}

		WP_CLI::log( 'Exported styles/site-editor-export.json.' );
		WP_CLI::warning( 'Promote exported style decisions into theme.json or permanent CSS before committing.' );

		return 1;
	}
}

WP_CLI::add_command( 'bm sync-site-editor', Ben_Montgomery_Site_Editor_Sync_Command::class );

