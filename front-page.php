<?php
/**
 * Page d'accueil : carrousel « À la une », derniers articles, puis un bloc par rubrique.
 */
get_header();

$hero = new WP_Query( array( 'posts_per_page' => 5, 'ignore_sticky_posts' => false, 'no_found_rows' => true ) );
$hero_ids = wp_list_pluck( $hero->posts, 'ID' );
?>
<main class="snd-wrap">

	<?php if ( $hero->have_posts() ) : ?>
	<section class="snd-hero" aria-label="À la une">
		<div class="snd-hero-grid">
			<div class="snd-slider" data-snd-slider>
				<?php
				$i = 0;
				while ( $hero->have_posts() ) :
					$hero->the_post();
					?>
					<article class="snd-slide<?php echo 0 === $i ? ' is-active' : ''; ?>">
						<?php snd_media_thumb( 'snd-hero' ); ?>
						<div class="snd-slide-body">
							<?php snd_media_cat_badge(); ?>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php snd_media_date(); ?>
						</div>
					</article>
					<?php
					$i++;
				endwhile;
				wp_reset_postdata();
				?>
				<?php if ( $i > 1 ) : ?>
					<div class="snd-dots" aria-hidden="true">
						<?php for ( $d = 0; $d < $i; $d++ ) : ?>
							<button type="button" class="<?php echo 0 === $d ? 'is-active' : ''; ?>" data-snd-dot="<?php echo esc_attr( $d ); ?>" tabindex="-1"></button>
						<?php endfor; ?>
					</div>
					<div class="snd-slider-nav">
						<button type="button" data-snd-prev aria-label="Article précédent">‹</button>
						<button type="button" data-snd-next aria-label="Article suivant">›</button>
					</div>
				<?php endif; ?>
			</div>

			<aside class="snd-latest">
				<div class="snd-section-title"><h2>Dernières infos</h2></div>
				<?php
				$latest = new WP_Query( array( 'posts_per_page' => 6, 'post__not_in' => $hero_ids, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
				if ( $latest->have_posts() ) :
					echo '<ol>';
					while ( $latest->have_posts() ) :
						$latest->the_post();
						?>
						<li><div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php snd_media_date(); ?></div></li>
						<?php
					endwhile;
					echo '</ol>';
					wp_reset_postdata();
				endif;
				?>
			</aside>
		</div>
	</section>
	<?php else : ?>
		<p class="snd-empty" style="margin-top:30px">Aucun article publié pour le moment.</p>
	<?php endif; ?>

	<div class="snd-blocks">
		<?php
		$n = 0;
		foreach ( snd_media_sections() as $slug => $name ) :
			$term = get_category_by_slug( $slug );
			if ( ! $term ) {
				continue;
			}
			$wide = ( 0 === $n % 3 ); // un bloc large, puis deux blocs côte à côte.
			$q    = new WP_Query( array( 'cat' => $term->term_id, 'posts_per_page' => $wide ? 5 : 4, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
			if ( ! $q->have_posts() && ! current_user_can( 'edit_posts' ) ) {
				continue;
			}
			$n++;
			?>
			<section class="snd-block<?php echo $wide ? ' snd-block--wide' : ''; ?>">
				<div class="snd-section-title">
					<h2><a href="<?php echo esc_url( get_category_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h2>
					<a class="snd-more" href="<?php echo esc_url( get_category_link( $term ) ); ?>">Tout voir ›</a>
				</div>
				<?php if ( $q->have_posts() ) : ?>
					<div class="snd-block-body">
						<?php $q->the_post(); ?>
						<article class="snd-lead">
							<?php snd_media_thumb( 'snd-card' ); ?>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php snd_media_date(); ?>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</article>
						<?php
						if ( $q->have_posts() ) {
							snd_media_list( $q );
						}
						wp_reset_postdata();
						?>
					</div>
				<?php else : ?>
					<p class="snd-empty">Aucun article dans cette rubrique pour le moment.</p>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</div>

</main>
<?php
get_footer();
