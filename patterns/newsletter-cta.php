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
  <!-- wp:paragraph {"align":"center","className":"bm-section-label"} -->
  <p class="has-text-align-center bm-section-label">Stay in the loop</p>
  <!-- /wp:paragraph -->

  <!-- wp:heading {"textAlign":"center","level":2,"fontSize":"xx-large"} -->
  <h2 class="wp-block-heading has-text-align-center has-xx-large-font-size">Get occasional updates on new releases, fresh writing, and what is taking shape behind the scenes.</h2>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"align":"center","textColor":"muted"} -->
  <p class="has-text-align-center has-muted-color has-text-color">Use this panel for a newsletter signup, release alert list, or any other lightweight call to keep interested listeners and readers close.</p>
  <!-- /wp:paragraph -->

  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
  <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
    <!-- wp:button {"backgroundColor":"accent","textColor":"base"} -->
    <div class="wp-block-button"><a class="wp-block-button__link has-base-color has-accent-background-color has-text-color has-background wp-element-button" href="/contact">Join or inquire</a></div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
