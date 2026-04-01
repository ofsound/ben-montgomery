#!/bin/sh
set -eu

THEME_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
WP="$THEME_DIR/bin/wp-local"

ensure_page() {
  title=$1
  slug=$2

  existing_id=$("$WP" post list --post_type=page --name="$slug" --field=ID --format=ids 2>/dev/null || true)

  if [ -n "$existing_id" ]; then
    printf '%s' "$existing_id"
    return 0
  fi

  "$WP" post create \
    --post_type=page \
    --post_status=publish \
    --post_title="$title" \
    --post_name="$slug" \
    --porcelain
}

ensure_post() {
  title=$1
  slug=$2
  content=$3

  existing_id=$("$WP" post list --post_type=post --name="$slug" --field=ID --format=ids 2>/dev/null || true)

  if [ -n "$existing_id" ]; then
    printf '%s\n' "Post already exists: $slug"
    return 0
  fi

  "$WP" post create \
    --post_type=post \
    --post_status=publish \
    --post_title="$title" \
    --post_name="$slug" \
    --post_content="$content" \
    --porcelain >/dev/null

  printf '%s\n' "Created post: $slug"
}

delete_default_post() {
  post_type=$1
  slug=$2

  existing_id=$("$WP" post list --post_type="$post_type" --name="$slug" --field=ID --format=ids 2>/dev/null || true)

  if [ -n "$existing_id" ]; then
    "$WP" post delete "$existing_id" --force >/dev/null
    printf '%s\n' "Deleted default $post_type: $slug"
  fi
}

home_id=$(ensure_page "Home" "home")
about_id=$(ensure_page "About" "about")
contact_id=$(ensure_page "Contact" "contact")
blog_id=$(ensure_page "Blog" "blog")

"$WP" option update show_on_front page >/dev/null
"$WP" option update page_on_front "$home_id" >/dev/null
"$WP" option update page_for_posts "$blog_id" >/dev/null

delete_default_post page "sample-page"
delete_default_post post "hello-world"

ensure_post \
  "Notes on Building Calm Systems" \
  "notes-on-building-calm-systems" \
  "<!-- wp:paragraph --><p>Sample seeded post content for a modern editorial layout. Replace this with real writing once the site voice is locked.</p><!-- /wp:paragraph -->"

ensure_post \
  "Why a Block Theme Makes the Editorial System Easier to Maintain" \
  "why-a-block-theme-makes-the-editorial-system-easier-to-maintain" \
  "<!-- wp:paragraph --><p>This seeded post exists to validate Query Loop layouts, metadata display, and single template rhythm.</p><!-- /wp:paragraph -->"

ensure_post \
  "A Faster FSE Workflow with Local and Playground" \
  "a-faster-fse-workflow-with-local-and-playground" \
  "<!-- wp:paragraph --><p>Use this post to test featured images, excerpts, pagination, and archive states once additional content is added.</p><!-- /wp:paragraph -->"

printf '%s\n' "Seed complete. Home page ID: $home_id, About page ID: $about_id, Contact page ID: $contact_id"
