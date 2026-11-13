<?php
get_header();
?>
<main class="snd-wrap snd-main">
	<div class="snd-layout">
		<div>
			<?php
			while ( have_posts() ) :
				the_post();
				$cat = snd_media_primary_cat();
				?>
				<article <?php post_class( 'snd-article' ); ?>>
					<div class="snd-crumbs">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
						<?php if ( $cat ) : ?>
							› <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
						<?php endif; ?>
					</div>
					<?php snd_media_cat_badge(); ?>
					<h1><?php the_title(); ?></h1>
					<div class="snd-meta">
						Publié le <?php echo esc_html( get_the_date( 'j F Y' ) ); ?> à <?php echo esc_html( get_the_time( 'H\hi' ) ); ?>
						· Par <?php the_author(); ?>
					</div>
					<?php if ( snd_media_is_demo() ) : ?>
						<div class="snd-demo-note">Article de démonstration — contenu fictif, à remplacer.</div>
					<?php endif; ?>
					<?php
					if ( has_post_thumbnail() ) {
						snd_media_thumb( 'snd-hero', false );
					}
					?>
					<div class="snd-content"><?php the_content(); ?></div>
					<?php wp_link_pages(); ?>
					<?php the_tags( '<div class="snd-tags">', '', '</div>' ); ?>
					<?php
					$url   = rawurlencode( get_permalink() );
					$title = rawurlencode( get_the_title() );
					?>
					<div class="snd-share">
						<span>Partager :</span>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $url ); ?>" target="_blank" rel="noopener">Facebook</a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $url ); ?>&amp;text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener">X</a>
						<a href="https://wa.me/?text=<?php echo esc_attr( $title . '%20' . $url ); ?>" target="_blank" rel="noopener">WhatsApp</a>
						<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $url ); ?>" target="_blank" rel="noopener">LinkedIn</a>
					</div>
				</article>

				<?php
				if ( $cat ) :
					$rel = new WP_Query( array( 'cat' => $cat->term_id, 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ), 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
					if ( $rel->have_posts() ) :
						?>
						<section class="snd-related">
							<div class="snd-section-title"><h2>À lire aussi</h2></div>
							<div class="snd-cards">
								<?php
								while ( $rel->have_posts() ) {
									$rel->the_post();
									echo '<article class="snd-card">';
									snd_media_thumb( 'snd-card' );
									echo '<div class="snd-card-body"><h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
									snd_media_date();
									echo '</div></article>';
								}
								wp_reset_postdata();
								?>
							</div>
						</section>
						<?php
					endif;
				endif;

				if ( comments_open() || get_comments_number() ) {
					echo '<div class="snd-comments">';
					comments_template();
					echo '</div>';
				}
			endwhile;
			?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
<?php
get_footer();
