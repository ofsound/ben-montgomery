<?php
/**
 * Plugin Name: WordPress MCP Adapter Loader
 * Description: Boots the official WordPress MCP adapter for local Codex access.
 * Version: 0.1.0
 * Author: Ben Montgomery
 */

declare(strict_types=1);

use WP\MCP\Core\McpAdapter;

if ( ! defined( 'BEN_MONTGOMERY_THEME_ROOT' ) ) {
	define( 'BEN_MONTGOMERY_THEME_ROOT', dirname( __DIR__, 2 ) );
}

$autoload = __DIR__ . '/vendor/autoload.php';

if (! file_exists($autoload)) {
	return;
}

require_once $autoload;
require_once BEN_MONTGOMERY_THEME_ROOT . '/inc/class-ben-montgomery-site-editor-sync-service.php';
require_once __DIR__ . '/includes/class-ben-montgomery-mcp-abilities.php';

add_action(
	'wp_abilities_api_init',
	static function (): void {
		Ben_Montgomery_Mcp_Abilities::register();
	},
	20
);

add_action(
	'init',
	static function (): void {
		Ben_Montgomery_Mcp_Abilities::register();
	},
	20
);

add_filter(
	'mcp_adapter_default_server_config',
	static function ( array $config ): array {
		$tools = array();

		foreach ( wp_get_abilities() as $ability ) {
			$meta = $ability->get_meta();

			if ( ! ( $meta['mcp']['public'] ?? false ) ) {
				continue;
			}

			if ( ( $meta['mcp']['type'] ?? 'tool' ) !== 'tool' ) {
				continue;
			}

			$tools[] = $ability->get_name();
		}

		$config['tools'] = array_values( array_unique( $tools ) );

		return $config;
	}
);

if (class_exists(McpAdapter::class)) {
	McpAdapter::instance();
}
