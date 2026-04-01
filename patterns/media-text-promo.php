<?php
/**
 * Title: Media and text promo
 * Slug: ben-montgomery/media-text-promo
 * Categories: ben-montgomery-pages
 * Inserter: true
 * Description: A promo section using the Media & Text block for editorial highlights.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
  <!-- wp:media-text {"mediaPosition":"right","verticalAlignment":"center","className":"bm-outline-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
  <div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-center bm-outline-surface" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);grid-template-columns:auto 50%">
    <figure class="wp-block-media-text__media"><img alt="" src="/wp-content/themes/ben-montgomery/assets/media/editorial-placeholder.svg"/></figure>
    <div class="wp-block-media-text__content">
      <!-- wp:paragraph {"className":"bm-section-label"} -->
      <p class="bm-section-label">Featured story</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
      <h3 class="wp-block-heading has-xx-large-font-size">Pair an image-led feature with a tight call to action.</h3>
      <!-- /wp:heading -->

      <!-- wp:paragraph -->
      <p>This pattern is useful for promoted essays, releases, or editorial campaigns that need more weight than a simple text card.</p>
      <!-- /wp:paragraph -->

      <!-- wp:buttons -->
      <div class="wp-block-buttons">
        <!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/">Read the feature</a></div>
        <!-- /wp:button -->
      </div>
      <!-- /wp:buttons -->
    </div>
  </div>
  <!-- /wp:media-text -->
</div>
<!-- /wp:group -->
