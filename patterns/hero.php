<?php
/**
 * Title: Editorial hero
 * Slug: ben-montgomery/hero
 * Categories: ben-montgomery-pages
 * Inserter: true
 * Description: A split hero section for the homepage or feature pages.
 */
?>
<!-- wp:group {"align":"wide","className":"bm-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide bm-hero" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--70)">
  <!-- wp:group {"className":"bm-hero__content","layout":{"type":"constrained"}} -->
  <div class="wp-block-group bm-hero__content">
    <!-- wp:paragraph {"align":"center","className":"bm-section-label"} -->
    <p class="has-text-align-center bm-section-label">Songs, essays, experiments, and the work behind them</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
    <h1 class="wp-block-heading has-text-align-center has-xx-large-font-size">A home for new music, field notes, and ideas still in motion.</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","fontSize":"large","textColor":"muted"} -->
    <p class="has-text-align-center has-muted-color has-text-color has-large-font-size">Ben Montgomery shares recordings, writing, and creative systems in public, with a site built to stay sharp, personal, and easy to keep current.</p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
    <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
      <!-- wp:button {"backgroundColor":"contrast","textColor":"base"} -->
      <div class="wp-block-button"><a class="wp-block-button__link has-base-color has-contrast-background-color has-text-color has-background wp-element-button" href="/music">Explore music</a></div>
      <!-- /wp:button -->

      <!-- wp:button {"className":"is-style-outline"} -->
      <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/blog">Read the journal</a></div>
      <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
  </div>
  <!-- /wp:group -->
</div>
<!-- /wp:group -->
