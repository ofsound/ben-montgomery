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
		unset( $args );

		$result = Ben_Montgomery_Site_Editor_Sync_Service::export(
			array(
				'templates' => isset( $assoc_args['templates'] ),
				'parts'     => isset( $assoc_args['parts'] ),
				'styles'    => isset( $assoc_args['styles'] ),
			)
		);

		foreach ( $result['messages'] as $message ) {
			WP_CLI::log( $message );
		}

		foreach ( $result['warnings'] as $warning ) {
			WP_CLI::warning( $warning );
		}

		WP_CLI::success(
			sprintf(
				'Site Editor sync complete. Templates: %1$d, Parts: %2$d, Style exports: %3$d.',
				$result['counts']['templates'],
				$result['counts']['parts'],
				$result['counts']['styles']
			)
		);
	}
}

WP_CLI::add_command( 'bm sync-site-editor', Ben_Montgomery_Site_Editor_Sync_Command::class );
