<?php
/**
 * Title: Editorial hero
 * Slug: ben-montgomery/hero
 * Categories: ben-montgomery-pages
 * Inserter: true
 * Description: A split hero section for the homepage or feature pages.
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
  <!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
  <div class="wp-block-columns are-vertically-aligned-top">
    <!-- wp:column {"verticalAlignment":"top","width":"65%"} -->
    <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:65%">
      <!-- wp:paragraph {"className":"bm-section-label"} -->
      <p class="bm-section-label">Independent writing, strategy, and experiments</p>
      <!-- /wp:paragraph -->

      <!-- wp:heading {"level":1,"fontSize":"display"} -->
      <h1 class="wp-block-heading has-display-font-size">Build a site that feels custom without sacrificing editorial speed.</h1>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"fontSize":"large","textColor":"muted"} -->
      <p class="has-muted-color has-text-color has-large-font-size">This starter theme is designed for essays, notes, interviews, and project updates. It keeps the design system in files and keeps the Site Editor from becoming the source of truth.</p>
      <!-- /wp:paragraph -->

      <!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
      <div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
        <!-- wp:button {"backgroundColor":"contrast","textColor":"base"} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-base-color has-contrast-background-color has-text-color has-background wp-element-button" href="/about">About the work</a></div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/contact">Start a conversation</a></div>
        <!-- /wp:button -->
      </div>
      <!-- /wp:buttons -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"verticalAlignment":"top"} -->
    <div class="wp-block-column is-vertically-aligned-top">
      <!-- wp:group {"className":"bm-outline-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
      <div class="wp-block-group bm-outline-surface" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
        <!-- wp:paragraph {"className":"bm-section-label"} -->
        <p class="bm-section-label">Editorial system</p>
        <!-- /wp:paragraph -->

        <!-- wp:list -->
        <ul>
          <li>Block templates and parts for the whole site shell</li>
          <li>Patterns for repeatable sections editors can actually use</li>
          <li>ACF reserved for structured cases, not default layouting</li>
        </ul>
        <!-- /wp:list -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
</div>
<!-- /wp:group -->

