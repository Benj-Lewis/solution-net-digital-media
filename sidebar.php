<aside class="snd-side">
	<section>
		<div class="snd-section-title"><span class="snd-h">Dernières infos</span></div>
		<?php
		$exclude = is_singular() ? array( get_queried_object_id() ) : array();
		$side    = new WP_Query( array( 'posts_per_page' => 5, 'post__not_in' => $exclude, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
		if ( $side->have_posts() ) {
			snd_media_list( $side );
		}
		?>
	</section>
	<section>
		<div class="snd-section-title"><span class="snd-h">Rubriques</span></div>
		<ul class="snd-cats">
			<?php
			foreach ( snd_media_sections() as $slug => $name ) {
				$term = get_category_by_slug( $slug );
				if ( $term ) {
					echo '<li><a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
				}
			}
			?>
		</ul>
	</section>
	<?php dynamic_sidebar( 'snd-sidebar' ); ?>
</aside>
