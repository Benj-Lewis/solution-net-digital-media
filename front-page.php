<?php
/**
 * Accueil : mosaïque à la une, puis blocs de rubriques aux mises en page variées.
 */
get_header();

$snd_hero = new WP_Query( array( 'posts_per_page' => 5, 'no_found_rows' => true ) );
snd_shown( wp_list_pluck( $snd_hero->posts, 'ID' ) );
?>
<main>
	<?php if ( $snd_hero->have_posts() ) : ?>
		<section class="snd-wrap snd-hero" aria-label="À la une">
			<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?> — à la une</h1>
			<div class="snd-mosaic">
				<?php
				$i = 0;
				while ( $snd_hero->have_posts() ) {
					$snd_hero->the_post();
					if ( 0 === $i ) {
						snd_overlay( 'snd-xl', 'h2', true, 'snd-ov--hero' );
					} else {
						snd_overlay( 'snd-card', 'h3', $i < 3, 'snd-ov--tile' );
					}
					$i++;
				}
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php else : ?>
		<div class="snd-wrap snd-empty-site"><p>Aucun article publié pour le moment.</p></div>
	<?php endif; ?>

	<div class="snd-wrap snd-layout">
		<div class="snd-main-col">
			<?php
			snd_block_feature( 'actualite', 5 );
			snd_block_grid( 'politique', 3, 3 );
			snd_block_duo( 'securite', 'justice', 4 );
			snd_block_wide( 'economie', 4 );
			?>
		</div>
		<?php get_sidebar(); ?>
	</div>

	<?php snd_block_dark( 'sport', 4 ); ?>

	<div class="snd-wrap snd-layout">
		<div class="snd-main-col">
			<?php
			snd_block_feature( 'societe', 5 );
			snd_block_duo( 'culture', 'femme', 4 );
			snd_block_grid( 'sante', 3, 3 );
			?>
		</div>
		<aside class="snd-side" aria-label="Rubriques">
			<div class="snd-side-in">
				<section class="snd-widget snd-catlist">
					<h2 class="snd-widget-title">Toutes les rubriques</h2>
					<?php snd_menu_fallback( array( 'menu_class' => 'snd-catlist-ul' ) ); ?>
				</section>
			</div>
		</aside>
	</div>

	<div class="snd-wrap">
		<?php snd_block_tiles( 'afrique', 4 ); ?>
	</div>
</main>
<?php
get_footer();
