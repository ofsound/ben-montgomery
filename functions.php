<?php
declare(strict_types=1);

if ( ! defined( 'BEN_MONTGOMERY_THEME_DIR' ) ) {
	define( 'BEN_MONTGOMERY_THEME_DIR', __DIR__ );
}

if ( ! defined( 'BEN_MONTGOMERY_THEME_URI' ) ) {
	define( 'BEN_MONTGOMERY_THEME_URI', get_stylesheet_directory_uri() );
}

require_once BEN_MONTGOMERY_THEME_DIR . '/inc/class-ben-montgomery-music-page.php';

function ben_montgomery_get_font_stylesheet_url(): string {
	return 'https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400..800&family=JetBrains+Mono:wght@400;500;600&family=Sora:wght@500;600;700&display=swap';
}

/**
 * Emits the stored or system-resolved theme preference before CSS paints.
 */
function ben_montgomery_print_theme_bootstrap(): void {
	?>
	<script>
		(function () {
			var storageKey = 'bm-theme-preference';
			var root = document.documentElement;
			var preferredTheme = null;

			try {
				preferredTheme = window.localStorage.getItem(storageKey);
			} catch (error) {
				preferredTheme = null;
			}

			if (preferredTheme !== 'light' && preferredTheme !== 'dark') {
				preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
			}

			root.dataset.theme = preferredTheme;
			root.style.colorScheme = preferredTheme;
		}());
	</script>
	<?php
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_editor_style(
			array(
				ben_montgomery_get_font_stylesheet_url(),
				'style.css',
			)
		);
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

		add_shortcode(
			'bm_current_year',
			static function (): string {
				return esc_html( (string) wp_date( 'Y' ) );
			}
		);
	}
);

add_action( 'wp_head', 'ben_montgomery_print_theme_bootstrap', 0 );

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$script_path = BEN_MONTGOMERY_THEME_DIR . '/assets/js/theme-toggle.js';
		$style_path  = BEN_MONTGOMERY_THEME_DIR . '/style.css';

		wp_enqueue_style(
			'ben-montgomery-theme',
			get_stylesheet_uri(),
			array(),
			file_exists( $style_path ) ? (string) filemtime( $style_path ) : null
		);

		wp_enqueue_style(
			'ben-montgomery-fonts',
			ben_montgomery_get_font_stylesheet_url(),
			array(),
			null
		);

		wp_enqueue_script(
			'ben-montgomery-theme-toggle',
			BEN_MONTGOMERY_THEME_URI . '/assets/js/theme-toggle.js',
			array(),
			file_exists( $script_path ) ? (string) filemtime( $script_path ) : null,
			true
		);
	}
);

add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_enqueue_style(
			'ben-montgomery-editor-fonts',
			ben_montgomery_get_font_stylesheet_url(),
			array(),
			null
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

Ben_Montgomery_Music_Page::init();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once BEN_MONTGOMERY_THEME_DIR . '/inc/class-ben-montgomery-site-editor-sync-service.php';
	require_once BEN_MONTGOMERY_THEME_DIR . '/inc/class-ben-montgomery-site-editor-sync-command.php';
}



add_filter( 'ai1wm_exclude_themes_from_export', function ( $exclude_filters ) {
    $exclude_filters[] = BEN_MONTGOMERY_THEME_DIR . '/node_modules';
    return $exclude_filters;
} );