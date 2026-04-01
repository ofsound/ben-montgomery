<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exports Site Editor changes back into the active theme files.
 */
final class Ben_Montgomery_Site_Editor_Sync_Service {
	/**
	 * Export Site Editor data into the theme.
	 *
	 * @param array<string, bool> $options Export options.
	 * @return array<string, mixed>
	 */
	public static function export( array $options = array() ): array {
		$export_templates = ! empty( $options['templates'] );
		$export_parts     = ! empty( $options['parts'] );
		$export_styles    = ! empty( $options['styles'] );

		if ( ! $export_templates && ! $export_parts && ! $export_styles ) {
			$export_templates = true;
			$export_parts     = true;
			$export_styles    = true;
		}

		$result = array(
			'counts'   => array(
				'templates' => 0,
				'parts'     => 0,
				'styles'    => 0,
			),
			'exported' => array(),
			'messages' => array(),
			'warnings' => array(),
		);

		if ( $export_templates ) {
			$template_result               = self::export_block_entities( 'wp_template', 'templates' );
			$result['counts']['templates'] = $template_result['count'];
			$result['exported']            = array_merge( $result['exported'], $template_result['exported'] );
			$result['messages']            = array_merge( $result['messages'], $template_result['messages'] );
			$result['warnings']            = array_merge( $result['warnings'], $template_result['warnings'] );
		}

		if ( $export_parts ) {
			$parts_result               = self::export_block_entities( 'wp_template_part', 'parts' );
			$result['counts']['parts'] = $parts_result['count'];
			$result['exported']         = array_merge( $result['exported'], $parts_result['exported'] );
			$result['messages']         = array_merge( $result['messages'], $parts_result['messages'] );
			$result['warnings']         = array_merge( $result['warnings'], $parts_result['warnings'] );
		}

		if ( $export_styles ) {
			$styles_result               = self::export_styles();
			$result['counts']['styles'] = $styles_result['count'];
			$result['exported']          = array_merge( $result['exported'], $styles_result['exported'] );
			$result['messages']          = array_merge( $result['messages'], $styles_result['messages'] );
			$result['warnings']          = array_merge( $result['warnings'], $styles_result['warnings'] );
		}

		return $result;
	}

	/**
	 * Export customized block templates or parts.
	 *
	 * @param string $post_type Source entity type.
	 * @param string $directory Target directory.
	 * @return array<string, mixed>
	 */
	private static function export_block_entities( string $post_type, string $directory ): array {
		$written   = 0;
		$exported  = array();
		$messages  = array();
		$warnings  = array();
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
				$warnings[] = sprintf( 'Unable to write %s.', $relative_path );
				continue;
			}

			++$written;
			$exported[] = $relative_path;
			$messages[] = sprintf( 'Exported %s.', $relative_path );
		}

		if ( 0 === $written ) {
			$messages[] = sprintf( 'No customized %s records found to export.', $post_type );
		}

		return array(
			'count'    => $written,
			'exported' => $exported,
			'messages' => $messages,
			'warnings' => $warnings,
		);
	}

	/**
	 * Export customized global styles.
	 *
	 * @return array<string, mixed>
	 */
	private static function export_styles(): array {
		$exported   = array();
		$messages   = array();
		$warnings   = array();
		$styles_dir = trailingslashit( get_stylesheet_directory() ) . 'styles';
		$post_id    = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		wp_mkdir_p( $styles_dir );

		if ( ! $post_id ) {
			$messages[] = 'No Site Editor global styles post exists yet.';
			return array(
				'count'    => 0,
				'exported' => $exported,
				'messages' => $messages,
				'warnings' => $warnings,
			);
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post || '' === trim( (string) $post->post_content ) ) {
			$messages[] = 'No customized Site Editor global styles found to export.';
			return array(
				'count'    => 0,
				'exported' => $exported,
				'messages' => $messages,
				'warnings' => $warnings,
			);
		}

		$content = (string) $post->post_content;
		$decoded = json_decode( $content, true );

		if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
			$content = (string) wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . PHP_EOL;
		} else {
			$content = $content . PHP_EOL;
		}

		if ( false === file_put_contents( $styles_dir . '/site-editor-export.json', $content ) ) {
			$warnings[] = 'Unable to write styles/site-editor-export.json.';
			return array(
				'count'    => 0,
				'exported' => $exported,
				'messages' => $messages,
				'warnings' => $warnings,
			);
		}

		$exported[] = 'styles/site-editor-export.json';
		$messages[] = 'Exported styles/site-editor-export.json.';

		$custom_css = trim( wp_get_global_stylesheet( array( 'custom-css' ) ) );

		if ( '' !== $custom_css ) {
			if ( false === file_put_contents( $styles_dir . '/site-editor-custom.css', $custom_css . PHP_EOL ) ) {
				$warnings[] = 'Unable to write styles/site-editor-custom.css.';
			} else {
				$exported[] = 'styles/site-editor-custom.css';
				$messages[] = 'Exported styles/site-editor-custom.css.';
			}
		}

		$warnings[] = 'Promote exported style decisions into theme.json or permanent CSS before committing.';

		return array(
			'count'    => 1,
			'exported' => $exported,
			'messages' => $messages,
			'warnings' => $warnings,
		);
	}
}
