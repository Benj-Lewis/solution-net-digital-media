<?php
/**
 * Archives : rubrique, étiquette, auteur, date, recherche.
 */
get_header();

$snd_term  = is_category() ? get_queried_object() : null;
$snd_class = $snd_term ? 'snd-c-' . sanitize_html_class( $snd_term->slug ) : '';
?>
<main>
	<header class="snd-archive-head <?php echo esc_attr( $snd_class ); ?>">
		<div class="snd-wrap">
			<nav class="snd-crumbs" aria-label="Fil d'Ariane"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a><span>/</span><?php echo is_search() ? 'Recherche' : ( is_archive() ? 'Rubrique' : 'Articles' ); ?></nav>
			<h1>
				<?php
				if ( is_search() ) {
					echo 'Résultats pour « ' . esc_html( get_search_query() ) . ' »';
				} elseif ( is_archive() ) {
					echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
				} else {
					echo 'Tous les articles';
				}
				?>
			</h1>
			<?php if ( is_archive() && get_the_archive_description() ) : ?>
				<div class="snd-archive-desc"><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
			<?php endif; ?>
			<?php
			global $wp_query;
			if ( $wp_query->found_posts ) {
				echo '<p class="snd-archive-count">' . esc_html( sprintf( _n( '%s article', '%s articles', $wp_query->found_posts, 'snd-media' ), number_format_i18n( $wp_query->found_posts ) ) ) . '</p>';
			}
			?>
		</div>
	</header>

	<div class="snd-wrap snd-layout">
		<div class="snd-main-col">
			<?php if ( have_posts() ) : ?>
				<?php
				$i = 0;
				if ( ! is_paged() ) {
					the_post();
					echo '<article class="snd-hcard snd-hcard--lead ' . esc_attr( snd_cat_class() ) . '">';
					snd_img( 'snd-xl', null, true, true );
					echo '<div class="snd-hcard-body">';
					snd_badge();
					snd_title( 'h2' );
					echo '<p class="snd-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';
					snd_meta( null, true );
					echo '</div></article>';
				}
				?>
				<div class="snd-river">
					<?php
					while ( have_posts() ) {
						the_post();
						echo '<article class="snd-rcard ' . esc_attr( snd_cat_class() ) . '">';
						snd_img( 'snd-card' );
						echo '<div class="snd-rcard-body">';
						snd_badge();
						snd_title( 'h2' );
						echo '<p class="snd-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';
						snd_meta( null, true );
						echo '</div></article>';
					}
					?>
				</div>
				<nav class="snd-pagination" aria-label="Pagination">
					<?php echo wp_kses_post( paginate_links( array( 'prev_text' => '‹ Précédent', 'next_text' => 'Suivant ›', 'mid_size' => 1 ) ) ); ?>
				</nav>
			<?php else : ?>
				<div class="snd-noresult">
					<p>Aucun article ne correspond<?php echo is_search() ? ' à votre recherche' : ''; ?>.</p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
<?php
get_footer();
