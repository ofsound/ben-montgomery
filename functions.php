<?php
declare(strict_types=1);

if ( ! defined( 'BEN_MONTGOMERY_THEME_DIR' ) ) {
	define( 'BEN_MONTGOMERY_THEME_DIR', __DIR__ );
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_editor_style( 'style.css' );
	}
);

add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'ben-montgomery-pages',
			array(
				'label'       => __( 'Ben Montgomery Pages', 'ben-montgomery' ),
				'description' => __( 'Reusable page-building sections for the Ben Montgomery site.', 'ben-montgomery' ),
			)
		);

		register_block_pattern_category(
			'ben-montgomery-blog',
			array(
				'label'       => __( 'Ben Montgomery Blog', 'ben-montgomery' ),
				'description' => __( 'Reusable blog and archive sections for the Ben Montgomery site.', 'ben-montgomery' ),
			)
		);
	}
);

add_filter(
	'acf/settings/save_json',
	static function ( string $path ): string {
		return trailingslashit( BEN_MONTGOMERY_THEME_DIR ) . 'acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	static function ( array $paths ): array {
		$paths[] = trailingslashit( BEN_MONTGOMERY_THEME_DIR ) . 'acf-json';

		return array_values( array_unique( $paths ) );
	}
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once BEN_MONTGOMERY_THEME_DIR . '/inc/class-ben-montgomery-site-editor-sync-service.php';
	require_once BEN_MONTGOMERY_THEME_DIR . '/inc/class-ben-montgomery-site-editor-sync-command.php';
}
