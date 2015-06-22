<?php
/**
 * The template for displaying summary of content
 * Used for both category, search and any pages require list of contents summary. 
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since KiJong
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() ) : ?>
		<div class="entry-thumbnail">
			<?php the_post_thumbnail(); ?>
		</div>
		<?php endif; ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<h2 class="entry-title"><?php the_title(); ?></h2>
		</a>
		
		<div class="entry-meta">
			<?php twentythirteen_entry_meta(); ?>
			<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
</article><!-- #post -->