<?php
/**
 * Archives : rubrique, étiquette, auteur, date, recherche.
 */
get_header();
?>
<main class="snd-wrap snd-main">
	<div class="snd-layout">
		<div>
			<header class="snd-archive-head">
				<div class="snd-section-title">
					<h1 class="snd-h" style="margin:0;background:var(--snd-grad);color:#fff;font-size:17px;text-transform:uppercase;letter-spacing:.06em;padding:9px 16px;border-radius:4px 4px 0 0">
						<?php
						if ( is_search() ) {
							echo 'Recherche : ' . esc_html( get_search_query() );
						} elseif ( is_archive() ) {
							echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
						} else {
							echo 'Tous les articles';
						}
						?>
					</h1>
				</div>
				<?php if ( is_archive() && get_the_archive_description() ) : ?>
					<div><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="snd-cards">
					<?php
					while ( have_posts() ) {
						the_post();
						snd_media_card();
					}
					?>
				</div>
				<nav class="snd-pagination" aria-label="Pagination">
					<?php echo wp_kses_post( paginate_links( array( 'prev_text' => '‹', 'next_text' => '›' ) ) ); ?>
				</nav>
			<?php else : ?>
				<p class="snd-empty">Aucun article trouvé.</p>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
<?php
get_footer();
