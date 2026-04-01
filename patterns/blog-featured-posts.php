<?php
/**
 * Title: Blog featured posts
 * Slug: ben-montgomery/blog-featured-posts
 * Categories: ben-montgomery-blog
 * Inserter: true
 * Description: A query loop section for the latest posts.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)">
  <!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
  <div class="wp-block-group">
    <!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
    <div class="wp-block-group">
      <!-- wp:paragraph {"className":"bm-section-label"} -->
      <p class="bm-section-label">Latest posts</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":2,"fontSize":"xx-large"} -->
      <h2 class="wp-block-heading has-xx-large-font-size">A Query Loop-first blog section that stays editable.</h2>
      <!-- /wp:heading -->
    </div>
    <!-- /wp:group -->

    <!-- wp:buttons -->
    <div class="wp-block-buttons">
      <!-- wp:button {"className":"is-style-outline"} -->
      <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/blog">View archive</a></div>
      <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
  </div>
  <!-- /wp:group -->

  <!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"grid","columns":3},"layout":{"type":"constrained"}} -->
  <div class="wp-block-query">
    <!-- wp:post-template {"className":"bm-post-grid"} -->
    <!-- wp:group {"className":"bm-post-card","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"var:preset|spacing|50","left":"0"}}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group bm-post-card" style="padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--50);padding-left:0">
      <!-- wp:post-featured-image {"isLink":true} /-->

      <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"0","left":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
      <div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:0;padding-left:var(--wp--preset--spacing--40)">
        <!-- wp:post-date /-->
        <!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->
        <!-- wp:post-excerpt {"moreText":"Continue reading"} /-->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
    <!-- /wp:post-template -->

    <!-- wp:query-no-results -->
    <!-- wp:paragraph -->
    <p>No posts yet. Run the seed script or publish your first article to validate the layout.</p>
    <!-- /wp:paragraph -->
    <!-- /wp:query-no-results -->
  </div>
  <!-- /wp:query -->
</div>
<!-- /wp:group -->
