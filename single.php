<?php
get_header();
?>
<div class="snd-progress" aria-hidden="true"><span></span></div>
<main>
	<?php
	while ( have_posts() ) :
		the_post();
		$snd_cat = snd_primary_cat();
		?>
		<div class="snd-wrap snd-layout snd-layout--single">
			<article <?php post_class( 'snd-article ' . snd_cat_class() ); ?>>
				<header class="snd-article-head">
					<nav class="snd-crumbs" aria-label="Fil d'Ariane">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a>
						<?php if ( $snd_cat ) : ?>
							<span>/</span><a href="<?php echo esc_url( get_category_link( $snd_cat ) ); ?>"><?php echo esc_html( $snd_cat->name ); ?></a>
						<?php endif; ?>
					</nav>
					<?php snd_badge(); ?>
					<h1 class="snd-article-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="snd-standfirst"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<div class="snd-byline">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 44, '', '', array( 'class' => 'snd-avatar' ) ); ?>
						<div class="snd-byline-text">
							<span class="snd-byline-author">Par <strong><?php the_author(); ?></strong></span>
							<span class="snd-byline-date">
								Publié le <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y \à H\hi' ) ); ?></time>
								<?php if ( get_the_modified_time( 'U' ) > get_the_time( 'U' ) + HOUR_IN_SECONDS ) : ?>
									· mis à jour le <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?>
								<?php endif; ?>
								· <?php echo esc_html( snd_reading_time() ); ?> min de lecture
							</span>
						</div>
					</div>
					<?php snd_share( 'snd-share--top' ); ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="snd-article-figure">
						<?php the_post_thumbnail( 'snd-xl', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ) ); ?>
						<?php
						$cap = get_the_post_thumbnail_caption();
						if ( $cap ) {
							echo '<figcaption>' . esc_html( $cap ) . '</figcaption>';
						}
						?>
					</figure>
				<?php endif; ?>

				<?php if ( snd_is_demo() ) : ?>
					<p class="snd-demo-note">Article de démonstration — texte fictif destiné à montrer la mise en page.</p>
				<?php endif; ?>

				<div class="snd-prose">
					<?php the_content(); ?>
				</div>
				<?php wp_link_pages(); ?>

				<footer class="snd-article-foot">
					<?php the_tags( '<div class="snd-tags"><span>Mots-clés</span>', '', '</div>' ); ?>
					<?php snd_share( 'snd-share--bottom' ); ?>
					<div class="snd-authorbox">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'class' => 'snd-avatar' ) ); ?>
						<div>
							<p class="snd-authorbox-name"><?php the_author(); ?></p>
							<p><?php echo esc_html( get_the_author_meta( 'description' ) ? get_the_author_meta( 'description' ) : 'Rédaction de ' . get_bloginfo( 'name' ) . '.' ); ?></p>
						</div>
					</div>
					<?php
					$prev = get_previous_post( true );
					$next = get_next_post( true );
					if ( $prev || $next ) :
						?>
						<nav class="snd-postnav" aria-label="Articles voisins">
							<?php if ( $prev ) : ?>
								<a class="snd-postnav-prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><span>Article précédent</span><?php echo esc_html( get_the_title( $prev ) ); ?></a>
							<?php endif; ?>
							<?php if ( $next ) : ?>
								<a class="snd-postnav-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span>Article suivant</span><?php echo esc_html( get_the_title( $next ) ); ?></a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>
				</footer>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
			<?php get_sidebar(); ?>
		</div>

		<?php
		if ( $snd_cat ) :
			$rel = new WP_Query( array( 'cat' => $snd_cat->term_id, 'posts_per_page' => 4, 'post__not_in' => array( get_the_ID() ), 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
			if ( $rel->have_posts() ) :
				?>
				<section class="snd-related">
					<div class="snd-wrap">
						<?php snd_section_head( 'À lire aussi', get_category_link( $snd_cat ), $snd_cat->slug ); ?>
						<div class="snd-grid snd-grid-4">
							<?php
							while ( $rel->have_posts() ) {
								$rel->the_post();
								snd_card( false, 'snd-card', 'h3' );
							}
							wp_reset_postdata();
							?>
						</div>
					</div>
				</section>
				<?php
			endif;
		endif;
	endwhile;
	?>
</main>
<?php
get_footer();
