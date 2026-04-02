<?php
declare(strict_types=1);

final class Ben_Montgomery_Music_Page {
	private const MUSIC_PAGE_SLUG = 'music';
	private const RELEASES_FIELD = 'music_releases';

	public static function init(): void {
		add_shortcode( 'bm_music_discography', array( self::class, 'render_shortcode' ) );
	}

	public static function render_shortcode(): string {
		$post = get_post();

		if ( ! $post instanceof WP_Post || self::MUSIC_PAGE_SLUG !== $post->post_name ) {
			return '';
		}

		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$releases = get_field( self::RELEASES_FIELD, $post->ID );

		if ( ! is_array( $releases ) || array() === $releases ) {
			return '<p class="bm-music-empty">' . esc_html__( 'Music releases will appear here soon.', 'ben-montgomery' ) . '</p>';
		}

		ob_start();
		?>
		<div class="bm-music-discography">
			<div class="bm-music-grid">
				<?php foreach ( $releases as $release ) : ?>
					<?php
					$title       = isset( $release['album_title'] ) ? trim( (string) $release['album_title'] ) : '';
					$artist      = isset( $release['artist'] ) ? trim( (string) $release['artist'] ) : '';
					$date        = isset( $release['release_date'] ) ? self::format_release_date( (string) $release['release_date'] ) : '';
					$release_url = isset( $release['release_url'] ) ? esc_url( (string) $release['release_url'] ) : '';
					$image_id    = isset( $release['image'] ) ? (int) $release['image'] : 0;

					if ( '' === $title ) {
						continue;
					}
					?>
					<article class="bm-music-card">
						<?php if ( $image_id > 0 ) : ?>
							<div class="bm-music-card__art">
								<?php if ( '' !== $release_url ) : ?>
									<a class="bm-music-card__art-link" href="<?php echo esc_url( $release_url ); ?>" target="_blank" rel="noreferrer noopener">
										<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'bm-music-card__image' ) ); ?>
									</a>
								<?php else : ?>
									<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'bm-music-card__image' ) ); ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<div class="bm-music-card__body">
							<h2 class="bm-music-card__title">
								<?php if ( '' !== $release_url ) : ?>
									<a href="<?php echo esc_url( $release_url ); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html( $title ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $title ); ?>
								<?php endif; ?>
							</h2>

							<?php if ( '' !== $artist ) : ?>
								<p class="bm-music-card__artist"><?php echo esc_html( $artist ); ?></p>
							<?php endif; ?>

							<?php if ( '' !== $date ) : ?>
								<p class="bm-music-card__date"><?php echo esc_html( $date ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php

		return (string) ob_get_clean();
	}

	private static function format_release_date( string $value ): string {
		if ( '' === $value ) {
			return '';
		}

		$date = DateTimeImmutable::createFromFormat( 'Ymd', $value ) ?: DateTimeImmutable::createFromFormat( 'Y-m-d', $value );

		if ( false === $date ) {
			return $value;
		}

		return wp_date( 'F Y', $date->getTimestamp() );
	}
}
