<?php
/**
 * Title: Blog featured posts
 * Slug: ben-montgomery/blog-featured-posts
 * Categories: ben-montgomery-blog
 * Inserter: true
 * Description: A query loop section for the latest posts.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60)">
  <!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
  <div class="wp-block-group">
    <!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
    <div class="wp-block-group">
      <!-- wp:paragraph {"className":"bm-section-label"} -->
      <p class="bm-section-label">Latest posts</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"grid","columns":3},"layout":{"type":"constrained"}} -->
  <div class="wp-block-query">
    <!-- wp:post-template {"className":"bm-post-grid"} -->
    <!-- wp:template-part {"slug":"post-card","tagName":"div"} /-->
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
