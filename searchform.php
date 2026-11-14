<?php $snd_sid = wp_unique_id( 'snd-s-' ); ?>
<form class="snd-searchform" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $snd_sid ); ?>">Rechercher</label>
	<input type="search" id="<?php echo esc_attr( $snd_sid ); ?>" name="s" placeholder="Rechercher un article, un sujet…" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit" aria-label="Lancer la recherche"><?php echo snd_icon( 'search', 18 ); // phpcs:ignore ?></button>
</form>
