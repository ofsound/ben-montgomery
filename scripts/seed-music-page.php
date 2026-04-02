<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( ! function_exists( 'update_field' ) ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::error( 'ACF Pro must be active before seeding the Music page.' );
	}

	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

const BM_MUSIC_RELEASES_FIELD_KEY = 'field_bm_music_releases';
const BM_MUSIC_IMAGE_FIELD_KEY = 'field_bm_music_image';
const BM_MUSIC_ALBUM_TITLE_FIELD_KEY = 'field_bm_music_album_title';
const BM_MUSIC_ARTIST_FIELD_KEY = 'field_bm_music_artist';
const BM_MUSIC_RELEASE_DATE_FIELD_KEY = 'field_bm_music_release_date';
const BM_MUSIC_RELEASE_URL_FIELD_KEY = 'field_bm_music_release_url';
const BM_MUSIC_ATTACHMENT_SOURCE_META_KEY = '_bm_music_source_url';

$releases = array(
	array(
		'album_title'  => 'Memory Man',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20240201',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/memory-man',
		'image_url'    => 'https://f4.bcbits.com/img/a0946824323_2.jpg',
	),
	array(
		'album_title'  => 'In Directions',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20230101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/in-directions',
		'image_url'    => 'https://f4.bcbits.com/img/a3425227012_2.jpg',
	),
	array(
		'album_title'  => 'Nice Mask',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20220701',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/nice-mask',
		'image_url'    => 'https://f4.bcbits.com/img/a2826025045_2.jpg',
	),
	array(
		'album_title'  => "Why'd You Leave New Mexico?",
		'artist'       => 'Ben Montgomery',
		'release_date' => '20210301',
		'release_url'  => 'https://benmontgomery.bandcamp.com/track/whyd-you-leave-new-mexico',
		'image_url'    => 'https://f4.bcbits.com/img/a0383848204_2.jpg',
	),
	array(
		'album_title'  => 'JJ Shuffle',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20210201',
		'release_url'  => 'https://benmontgomery.bandcamp.com/track/jj-shuffle',
		'image_url'    => 'https://f4.bcbits.com/img/a1126874913_2.jpg',
	),
	array(
		'album_title'  => 'Playlist',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20201201',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/playlist',
		'image_url'    => 'https://f4.bcbits.com/img/a1743745369_2.jpg',
	),
	array(
		'album_title'  => 'This Is It',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20201101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/this-is-it',
		'image_url'    => 'https://f4.bcbits.com/img/a0586206815_2.jpg',
	),
	array(
		'album_title'  => 'Night or Day',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20190801',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/night-or-day',
		'image_url'    => 'https://f4.bcbits.com/img/a4116229053_2.jpg',
	),
	array(
		'album_title'  => 'Free As a Hotel',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20190101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/free-as-a-hotel',
		'image_url'    => 'https://f4.bcbits.com/img/a2303248392_2.jpg',
	),
	array(
		'album_title'  => 'Soft Piano',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20181101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/soft-piano',
		'image_url'    => 'https://f4.bcbits.com/img/a2879155198_2.jpg',
	),
	array(
		'album_title'  => 'Run Out, Run In / Become',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20180601',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/run-out-run-in-become',
		'image_url'    => 'https://f4.bcbits.com/img/a3228484085_2.jpg',
	),
	array(
		'album_title'  => 'Wilderness / Return',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20180101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/wilderness-return',
		'image_url'    => 'https://f4.bcbits.com/img/a2459027643_2.jpg',
	),
	array(
		'album_title'  => 'Consider This: Volume III',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20171101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/consider-this-volume-iii',
		'image_url'    => 'https://f4.bcbits.com/img/a1280736361_2.jpg',
	),
	array(
		'album_title'  => 'Consider This: Volume II',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20161101',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/consider-this-volume-ii',
		'image_url'    => 'https://f4.bcbits.com/img/a1175489551_2.jpg',
	),
	array(
		'album_title'  => 'Consider This: Volume I',
		'artist'       => 'Ben Montgomery',
		'release_date' => '20161001',
		'release_url'  => 'https://benmontgomery.bandcamp.com/album/consider-this-volume-i',
		'image_url'    => 'https://f4.bcbits.com/img/a1199664956_2.jpg',
	),
);

$page_id = bm_music_ensure_page();
$rows    = array();

foreach ( $releases as $release ) {
	$image_id = bm_music_import_image( $release['image_url'], $release['album_title'] );

	$rows[] = array(
		BM_MUSIC_IMAGE_FIELD_KEY        => $image_id,
		BM_MUSIC_ALBUM_TITLE_FIELD_KEY  => $release['album_title'],
		BM_MUSIC_ARTIST_FIELD_KEY       => $release['artist'],
		BM_MUSIC_RELEASE_DATE_FIELD_KEY => $release['release_date'],
		BM_MUSIC_RELEASE_URL_FIELD_KEY  => $release['release_url'],
	);
}

update_field( BM_MUSIC_RELEASES_FIELD_KEY, $rows, $page_id );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( sprintf( 'Music page ready. Page ID %d with %d releases.', $page_id, count( $rows ) ) );
}

function bm_music_ensure_page(): int {
	$page = get_page_by_path( 'music', OBJECT, 'page' );

	$page_args = array(
		'post_type'    => 'page',
		'post_title'   => 'Music',
		'post_name'    => 'music',
		'post_status'  => 'publish',
		'post_content' => '',
	);

	if ( $page instanceof WP_Post ) {
		$page_args['ID'] = $page->ID;
		wp_update_post( $page_args );

		return (int) $page->ID;
	}

	return (int) wp_insert_post( $page_args );
}

function bm_music_import_image( string $image_url, string $title ): int {
	$existing_ids = get_posts(
		array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'meta_key'               => BM_MUSIC_ATTACHMENT_SOURCE_META_KEY,
			'meta_value'             => $image_url,
			'no_found_rows'          => true,
			'suppress_filters'       => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! empty( $existing_ids ) ) {
		return (int) $existing_ids[0];
	}

	$temp_file = download_url( $image_url );

	if ( is_wp_error( $temp_file ) ) {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( sprintf( 'Failed to download cover art for %s: %s', $title, $temp_file->get_error_message() ) );
		}

		return 0;
	}

	$file_array = array(
		'name'     => sanitize_title( $title ) . '.jpg',
		'tmp_name' => $temp_file,
	);

	$attachment_id = media_handle_sideload( $file_array, 0, $title );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $temp_file );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::error( sprintf( 'Failed to import cover art for %s: %s', $title, $attachment_id->get_error_message() ) );
		}

		return 0;
	}

	update_post_meta( $attachment_id, BM_MUSIC_ATTACHMENT_SOURCE_META_KEY, $image_url );

	return (int) $attachment_id;
}
