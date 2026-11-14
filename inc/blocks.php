<?php
/**
 * Blocs de la page d'accueil. Chaque bloc a une mise en page différente,
 * comme sur les sites d'information professionnels.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function snd_cat_query( $slug, $count ) {
	$term = get_category_by_slug( $slug );
	if ( ! $term ) {
		return array( null, null );
	}
	$q = new WP_Query(
		array(
			'cat'                 => $term->term_id,
			'posts_per_page'      => $count,
			'post__not_in'        => snd_shown(),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	snd_shown( wp_list_pluck( $q->posts, 'ID' ) );
	return array( $term, $q );
}

/**
 * Grand article à gauche + liste à droite.
 */
function snd_block_feature( $slug, $count = 5 ) {
	list( $term, $q ) = snd_cat_query( $slug, $count );
	if ( ! $term || ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-sec snd-feature">';
	snd_section_head( $term->name, get_category_link( $term ), $slug );
	echo '<div class="snd-feature-grid">';
	$q->the_post();
	snd_card( true, 'snd-xl', 'h3' );
	echo '<div class="snd-stack">';
	while ( $q->have_posts() ) {
		$q->the_post();
		snd_list_item();
	}
	echo '</div></div></section>';
	wp_reset_postdata();
}

/**
 * Grille de cartes.
 */
function snd_block_grid( $slug, $count = 3, $cols = 3 ) {
	list( $term, $q ) = snd_cat_query( $slug, $count );
	if ( ! $term || ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-sec">';
	snd_section_head( $term->name, get_category_link( $term ), $slug );
	echo '<div class="snd-grid snd-grid-' . (int) $cols . '">';
	while ( $q->have_posts() ) {
		$q->the_post();
		snd_card( false );
	}
	echo '</div></section>';
	wp_reset_postdata();
}

/**
 * Deux rubriques côte à côte : un article illustré puis une liste.
 */
function snd_block_duo( $slug_a, $slug_b, $count = 4 ) {
	echo '<div class="snd-duo">';
	foreach ( array( $slug_a, $slug_b ) as $slug ) {
		list( $term, $q ) = snd_cat_query( $slug, $count );
		if ( ! $term || ! $q->have_posts() ) {
			continue;
		}
		echo '<section class="snd-sec">';
		snd_section_head( $term->name, get_category_link( $term ), $slug );
		$q->the_post();
		snd_card( true );
		echo '<div class="snd-stack snd-stack--plain">';
		while ( $q->have_posts() ) {
			$q->the_post();
			snd_list_item( false );
		}
		echo '</div></section>';
		wp_reset_postdata();
	}
	echo '</div>';
}

/**
 * Article horizontal (image à gauche) + rangée de cartes.
 */
function snd_block_wide( $slug, $count = 4 ) {
	list( $term, $q ) = snd_cat_query( $slug, $count );
	if ( ! $term || ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-sec snd-wide">';
	snd_section_head( $term->name, get_category_link( $term ), $slug );
	$q->the_post();
	echo '<article class="snd-hcard ' . esc_attr( snd_cat_class() ) . '">';
	snd_img( 'snd-xl' );
	echo '<div class="snd-hcard-body">';
	snd_badge();
	snd_title( 'h3' );
	echo '<p class="snd-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';
	snd_meta( null, true );
	echo '</div></article>';
	if ( $q->have_posts() ) {
		echo '<div class="snd-grid snd-grid-3">';
		while ( $q->have_posts() ) {
			$q->the_post();
			snd_card( false, 'snd-card', 'h4' );
		}
		echo '</div>';
	}
	echo '</section>';
	wp_reset_postdata();
}

/**
 * Bande sombre pleine largeur (sport, vidéo…).
 */
function snd_block_dark( $slug, $count = 4 ) {
	list( $term, $q ) = snd_cat_query( $slug, $count );
	if ( ! $term || ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-dark snd-c-' . esc_attr( $slug ) . '"><div class="snd-wrap">';
	snd_section_head( $term->name, get_category_link( $term ), $slug );
	echo '<div class="snd-dark-grid">';
	$q->the_post();
	snd_overlay( 'snd-xl', 'h3', false, 'snd-ov--lg' );
	echo '<div class="snd-dark-side">';
	while ( $q->have_posts() ) {
		$q->the_post();
		echo '<article class="snd-dli ' . esc_attr( snd_cat_class() ) . '">';
		snd_img( 'snd-thumb' );
		echo '<div>';
		snd_title( 'h4' );
		snd_meta();
		echo '</div></article>';
	}
	echo '</div></div></div></section>';
	wp_reset_postdata();
}

/**
 * Rangée de tuiles à texte sur image.
 */
function snd_block_tiles( $slug, $count = 4 ) {
	list( $term, $q ) = snd_cat_query( $slug, $count );
	if ( ! $term || ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-sec">';
	snd_section_head( $term->name, get_category_link( $term ), $slug );
	echo '<div class="snd-tiles">';
	while ( $q->have_posts() ) {
		$q->the_post();
		snd_overlay( 'snd-card', 'h4' );
	}
	echo '</div></section>';
	wp_reset_postdata();
}

/**
 * Les plus lus (compteur de lectures), avec repli sur les plus récents.
 */
function snd_most_read( $count = 5 ) {
	$q = new WP_Query(
		array(
			'posts_per_page'      => $count,
			'meta_key'            => '_snd_views',
			'orderby'             => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'date_query'          => array( array( 'after' => '60 days ago' ) ),
		)
	);
	if ( ! $q->have_posts() ) {
		$q = new WP_Query( array( 'posts_per_page' => $count, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
	}
	if ( ! $q->have_posts() ) {
		return;
	}
	echo '<section class="snd-widget snd-popular">';
	echo '<h2 class="snd-widget-title">Les plus lus</h2><ol>';
	while ( $q->have_posts() ) {
		$q->the_post();
		echo '<li class="' . esc_attr( snd_cat_class() ) . '"><div>';
		snd_badge( null, false );
		snd_title( 'h3' );
		echo '</div></li>';
	}
	echo '</ol></section>';
	wp_reset_postdata();
}

function snd_follow_box() {
	$socials = snd_media_socials();
	echo '<section class="snd-widget snd-follow">';
	echo '<h2 class="snd-widget-title">Suivez-nous</h2>';
	if ( $socials ) {
		snd_social_links( 'snd-follow-list' );
	} elseif ( current_user_can( 'edit_theme_options' ) ) {
		echo '<p class="snd-admin-hint"><a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=snd_media' ) ) . '">Ajoutez vos liens Facebook, Instagram, TikTok, WhatsApp…</a> (visible par vous seul)</p>';
	}
	$email = snd_opt( 'snd_email' );
	if ( $email ) {
		echo '<a class="snd-btn snd-btn--block" href="mailto:' . esc_attr( $email ) . '">' . snd_icon( 'mail', 16 ) . ' Envoyer une info à la rédaction</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</section>';
}
