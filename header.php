<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#contenu">Aller au contenu</a>

<div class="snd-topbar">
	<div class="snd-wrap">
		<span class="snd-date"><?php echo esc_html( ucfirst( wp_date( 'l j F Y' ) ) ); ?></span>
		<?php $socials = snd_media_socials(); ?>
		<?php if ( $socials ) : ?>
			<ul class="snd-social">
				<?php foreach ( $socials as $label => $url ) : ?>
					<li><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>

<header class="snd-header">
	<div class="snd-wrap">
		<?php snd_media_brand(); ?>
		<?php if ( is_active_sidebar( 'snd-ad-header' ) ) : ?>
			<div class="snd-ad"><?php dynamic_sidebar( 'snd-ad-header' ); ?></div>
		<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
			<div class="snd-ad">Espace publicitaire 728×90</div>
		<?php endif; ?>
	</div>
</header>

<nav class="snd-nav" aria-label="Rubriques">
	<div class="snd-wrap">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'snd-menu',
			'depth'          => 1,
			'fallback_cb'    => 'snd_media_menu_fallback',
		) );
		?>
		<form class="snd-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="snd-s">Rechercher</label>
			<input type="search" id="snd-s" name="s" placeholder="Chercher…" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button type="submit">OK</button>
		</form>
	</div>
</nav>

<div id="contenu">
