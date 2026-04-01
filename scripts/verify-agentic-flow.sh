#!/bin/zsh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
WP="$THEME_DIR/bin/wp-local"
MCP_USER=1

printf '%s\n' 'Verifying active theme and MCP abilities...'
"$WP" eval '
$required_public = array(
	"ben-montgomery/get-content",
	"ben-montgomery/update-content",
	"ben-montgomery/sync-site-editor",
	"ben-montgomery/site-map-resource",
);
$required_internal = array(
	"ben-montgomery/list-content",
	"ben-montgomery/list-theme-artifacts",
	"ben-montgomery/get-theme-artifact",
	"ben-montgomery/theme-structure-resource",
	"ben-montgomery/update-page-prompt",
	"ben-montgomery/theme-edit-prompt",
);

if ( "ben-montgomery" !== wp_get_theme()->get_stylesheet() ) {
	fwrite( STDERR, "Active theme is not ben-montgomery.\n" );
	exit( 1 );
}

$patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
$matches = array_filter(
	$patterns,
	static function ( $name ) {
		return 0 === strpos( $name, "ben-montgomery/" );
	},
	ARRAY_FILTER_USE_KEY
);
if ( count( $matches ) < 3 ) {
	fwrite( STDERR, "Expected Ben Montgomery patterns to be registered.\n" );
	exit( 1 );
}

foreach ( $required_public as $name ) {
	$ability = wp_get_ability( $name );
	if ( ! $ability ) {
		fwrite( STDERR, "Missing ability: {$name}\n" );
		exit( 1 );
	}
	$meta = $ability->get_meta();
	if ( empty( $meta["mcp"]["public"] ) ) {
		fwrite( STDERR, "Ability is not MCP public: {$name}\n" );
		exit( 1 );
	}
}

foreach ( $required_internal as $name ) {
	$ability = wp_get_ability( $name );
	if ( ! $ability ) {
		fwrite( STDERR, "Missing ability: {$name}\n" );
		exit( 1 );
	}
	$meta = $ability->get_meta();
	if ( ! empty( $meta["mcp"]["public"] ) ) {
		fwrite( STDERR, "Ability should not be MCP public: {$name}\n" );
		exit( 1 );
	}
}

$posts = get_posts(
	array(
		"post_type"   => "post",
		"post_status" => array( "publish", "draft", "future", "pending", "private" ),
		"numberposts" => 1,
		"orderby"     => "modified",
		"order"       => "DESC",
	)
);
if ( empty( $posts ) ) {
	fwrite( STDERR, "Expected at least one post for content verification.\n" );
	exit( 1 );
}

$post = $posts[0];
$content_item = Ben_Montgomery_Mcp_Abilities::execute_get_content( array( "id" => $post->ID ) );
if ( is_wp_error( $content_item ) || empty( $content_item["item"]["content"] ) ) {
	fwrite( STDERR, "Get content failed.\n" );
	exit( 1 );
}

$updated_item = Ben_Montgomery_Mcp_Abilities::execute_update_content(
	array(
		"id"    => $post->ID,
		"title" => $post->post_title,
	)
);
if ( is_wp_error( $updated_item ) || empty( $updated_item["updated"] ) ) {
	fwrite( STDERR, "Update content failed.\n" );
	exit( 1 );
}

$sync_result = Ben_Montgomery_Mcp_Abilities::execute_sync_site_editor( array( "templates" => true ) );
if ( is_wp_error( $sync_result ) || empty( $sync_result["synced"] ) ) {
	fwrite( STDERR, "Site Editor sync failed.\n" );
	exit( 1 );
}

$resource = Ben_Montgomery_Mcp_Abilities::execute_site_map_resource();
if ( empty( $resource[0]["text"] ) ) {
	fwrite( STDERR, "Resource payload invalid.\n" );
	exit( 1 );
}

echo "wordpress-ok";
'

printf '%s\n' 'Verifying MCP server inventory...'
"$WP" mcp-adapter list --user="$MCP_USER" | grep -Eq 'mcp-adapter-default-server.+[[:space:]]+3[[:space:]]+1[[:space:]]+0$'

printf '%s\n' 'Agentic WordPress verification passed.'
