<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="snd-skip" href="#contenu">Aller au contenu</a>

<div class="snd-topbar">
	<div class="snd-wrap snd-topbar-in">
		<span class="snd-today"><?php echo esc_html( ucfirst( wp_date( 'l j F Y' ) ) ); ?></span>
		<?php
		if ( snd_opt( 'snd_ticker' ) ) :
			$tick = new WP_Query( array( 'posts_per_page' => 8, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
			if ( $tick->have_posts() ) :
				?>
				<div class="snd-ticker" aria-label="Flash info">
					<span class="snd-ticker-label"><?php echo snd_icon( 'bolt', 13 ); // phpcs:ignore ?>Flash info</span>
					<div class="snd-ticker-track">
						<div class="snd-ticker-run">
							<?php
							for ( $loop = 0; $loop < 2; $loop++ ) {
								foreach ( $tick->posts as $tp ) {
									echo '<a href="' . esc_url( get_permalink( $tp ) ) . '"' . ( $loop ? ' aria-hidden="true" tabindex="-1"' : '' ) . '>' . esc_html( get_the_title( $tp ) ) . '</a>';
								}
							}
							?>
						</div>
					</div>
				</div>
				<?php
			endif;
		endif;
		?>
		<?php snd_social_links( 'snd-social snd-social--top' ); ?>
	</div>
</div>

<header class="snd-masthead">
	<div class="snd-wrap snd-masthead-in">
		<button class="snd-iconbtn snd-burger" type="button" aria-controls="snd-drawer" aria-expanded="false" aria-label="Ouvrir le menu"><?php echo snd_icon( 'menu', 24 ); // phpcs:ignore ?></button>
		<?php snd_brand( 'header' ); ?>
		<?php if ( is_active_sidebar( 'snd-ad-header' ) ) : ?>
			<div class="snd-ad snd-ad--header"><?php dynamic_sidebar( 'snd-ad-header' ); ?></div>
		<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
			<a class="snd-ad snd-ad--header snd-ad--empty" href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">Espace publicitaire 728 × 90<small>Visible par vous seul — à remplir dans Apparence &gt; Widgets</small></a>
		<?php endif; ?>
		<button class="snd-iconbtn snd-search-toggle snd-search-toggle--m" type="button" aria-controls="snd-searchbar" aria-expanded="false" aria-label="Rechercher"><?php echo snd_icon( 'search', 22 ); // phpcs:ignore ?></button>
	</div>
</header>

<nav class="snd-nav" aria-label="Rubriques">
	<div class="snd-wrap snd-nav-in">
		<a class="snd-nav-home<?php echo is_front_page() ? ' is-current' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Accueil"><?php echo snd_icon( 'home', 19 ); // phpcs:ignore ?></a>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'snd-menu',
				'depth'          => 1,
				'fallback_cb'    => 'snd_menu_fallback',
			)
		);
		?>
		<button class="snd-iconbtn snd-search-toggle" type="button" aria-controls="snd-searchbar" aria-expanded="false" aria-label="Rechercher"><?php echo snd_icon( 'search', 19 ); // phpcs:ignore ?></button>
	</div>
	<div class="snd-searchbar" id="snd-searchbar" hidden>
		<div class="snd-wrap"><?php get_search_form(); ?></div>
	</div>
</nav>

<div class="snd-drawer" id="snd-drawer" hidden>
	<div class="snd-drawer-backdrop" data-snd-close></div>
	<div class="snd-drawer-panel" role="dialog" aria-modal="true" aria-label="Menu">
		<div class="snd-drawer-head">
			<?php snd_brand( 'mini' ); ?>
			<button class="snd-iconbtn" type="button" data-snd-close aria-label="Fermer le menu"><?php echo snd_icon( 'close', 24 ); // phpcs:ignore ?></button>
		</div>
		<?php get_search_form(); ?>
		<p class="snd-drawer-label">Rubriques</p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'snd-drawer-menu',
				'depth'          => 1,
				'fallback_cb'    => 'snd_menu_fallback',
			)
		);
		if ( has_nav_menu( 'footer' ) ) {
			echo '<p class="snd-drawer-label">Le média</p>';
			wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'snd-drawer-pages', 'depth' => 1 ) );
		}
		snd_social_links( 'snd-social snd-social--drawer' );
		?>
	</div>
</div>

<div id="contenu" class="snd-site">
