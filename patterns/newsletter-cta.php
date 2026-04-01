<?php
/**
 * Title: Newsletter CTA
 * Slug: ben-montgomery/newsletter-cta
 * Categories: ben-montgomery-pages
 * Inserter: true
 * Description: A call-to-action panel for newsletter or signup prompts.
 */
?>
<!-- wp:group {"className":"bm-newsletter-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","right":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|60"},"margin":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group bm-newsletter-panel" style="margin-top:var(--wp--preset--spacing--60);margin-bottom:var(--wp--preset--spacing--70);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--60)">
  <!-- wp:columns {"verticalAlignment":"center"} -->
  <div class="wp-block-columns are-vertically-aligned-center">
    <!-- wp:column {"verticalAlignment":"center","width":"65%"} -->
    <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
      <!-- wp:paragraph {"className":"bm-section-label"} -->
      <p class="bm-section-label">Stay in the loop</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":2,"fontSize":"xx-large"} -->
      <h2 class="wp-block-heading has-xx-large-font-size">Reserve this panel for a newsletter, waitlist, or release announcement.</h2>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"textColor":"muted"} -->
      <p class="has-muted-color has-text-color">If signup logic becomes structured enough to need validation, this is a good candidate for an ACF-backed block later.</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"verticalAlignment":"center"} -->
    <div class="wp-block-column is-vertically-aligned-center">
      <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
      <div class="wp-block-buttons">
        <!-- wp:button {"backgroundColor":"accent","textColor":"base"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" href="/contact">Join or inquire</a></div>
        <!-- /wp:button -->
      </div>
      <!-- /wp:buttons -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->

