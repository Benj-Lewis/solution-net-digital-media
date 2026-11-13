<?php
/**
 * Mise en place initiale : rubriques, menu, réglages de lecture et articles de démonstration.
 * S'exécute une seule fois. Tout ce qui est créé ici est marqué pour pouvoir être retiré.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'snd_media_maybe_setup', 20 );
function snd_media_maybe_setup() {
	if ( get_option( 'snd_media_setup_done' ) === SND_MEDIA_VERSION ) {
		return;
	}
	if ( get_transient( 'snd_media_setup_lock' ) ) {
		return;
	}
	set_transient( 'snd_media_setup_lock', 1, 5 * MINUTE_IN_SECONDS );

	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

	// 1. Rubriques.
	$term_ids = array();
	foreach ( snd_media_sections() as $slug => $name ) {
		$term = get_category_by_slug( $slug );
		if ( ! $term ) {
			$res = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$term_ids[ $slug ] = (int) $res['term_id'];
		} else {
			$term_ids[ $slug ] = (int) $term->term_id;
		}
	}

	// 2. Menu principal.
	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) ) {
		$menu_name = 'Menu principal SND';
		$menu      = wp_get_nav_menu_object( $menu_name );
		$menu_id   = $menu ? (int) $menu->term_id : wp_create_nav_menu( $menu_name );
		if ( ! is_wp_error( $menu_id ) ) {
			if ( ! $menu ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'  => 'Accueil',
					'menu-item-url'    => home_url( '/' ),
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				) );
				foreach ( $term_ids as $slug => $term_id ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-object-id' => $term_id,
						'menu-item-object'    => 'category',
						'menu-item-type'      => 'taxonomy',
						'menu-item-status'    => 'publish',
					) );
				}
			}
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// 3. Page d'accueil = derniers articles (les anciens réglages sont gardés pour revenir en arrière).
	if ( false === get_option( 'snd_media_previous_reading' ) ) {
		add_option( 'snd_media_previous_reading', array(
			'show_on_front'  => get_option( 'show_on_front' ),
			'page_on_front'  => get_option( 'page_on_front' ),
			'page_for_posts' => get_option( 'page_for_posts' ),
		), '', false );
	}
	update_option( 'show_on_front', 'posts' );

	// 4. Articles de démonstration.
	if ( ! get_option( 'snd_media_demo_created' ) ) {
		snd_media_create_demo_posts( $term_ids );
		update_option( 'snd_media_demo_created', 1, false );
	}

	update_option( 'snd_media_setup_done', SND_MEDIA_VERSION, false );
	delete_transient( 'snd_media_setup_lock' );
	do_action( 'litespeed_purge_all' );
}

function snd_media_create_demo_posts( $term_ids ) {
	$author = 0;
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	if ( $admins ) {
		$author = (int) $admins[0];
	}
	$body = "<p><strong>Ceci est un article de démonstration.</strong> Il sert uniquement à montrer la mise en page du site et ne rapporte aucun fait réel. Remplacez-le par votre propre texte ou supprimez-le depuis Apparence &gt; Articles de démonstration.</p>\n\n"
		. "<p>Le premier paragraphe d'un article, le chapô, résume l'essentiel en deux ou trois phrases : qui, quoi, où, quand. C'est lui qui apparaît en extrait sur la page d'accueil et dans les pages de rubrique.</p>\n\n"
		. "<h2>Un intertitre pour structurer la lecture</h2>\n\n"
		. "<p>Le corps de l'article développe l'information : le contexte, les déclarations, les chiffres, les réactions. Des paragraphes courts se lisent mieux sur téléphone, où se trouve la majorité des lecteurs.</p>\n\n"
		. "<blockquote><p>Une citation mise en avant donne du rythme à la page et attire l'œil sur une déclaration importante.</p></blockquote>\n\n"
		. "<p>La fin de l'article peut rappeler les prochaines étapes ou renvoyer vers d'autres articles de la même rubrique.</p>";

	$angles = array(
		'Titre d\'exemple pour un article à la une',
		'Deuxième titre d\'exemple, un peu plus long pour tester l\'affichage sur deux lignes',
		'Troisième titre d\'exemple',
		'Quatrième titre d\'exemple pour compléter la liste',
		'Cinquième titre d\'exemple',
	);

	$n = 0;
	$i = 0;
	foreach ( $term_ids as $slug => $term_id ) {
		$term = get_term( $term_id, 'category' );
		foreach ( $angles as $k => $angle ) {
			$stamp   = time() - ( $n * 47 * MINUTE_IN_SECONDS ) - ( $k * 3 * HOUR_IN_SECONDS );
			$post_id = wp_insert_post( array(
				'post_title'    => sprintf( '[Démo] %s — %s', $term->name, $angle ),
				'post_content'  => $body,
				'post_excerpt'  => 'Article de démonstration : ce texte montre où s\'affiche le chapô de vos articles dans la rubrique ' . $term->name . '.',
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'post_author'   => $author,
				'post_category' => array( $term_id ),
				'post_date'     => wp_date( 'Y-m-d H:i:s', $stamp ),
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $stamp ),
			) );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_snd_demo', 1 );
				$i++;
			}
			$n++;
		}
	}
	return $i;
}

/**
 * Apparence > Articles de démonstration : suppression en un clic.
 */
add_action( 'admin_menu', 'snd_media_demo_menu' );
function snd_media_demo_menu() {
	add_theme_page( 'Articles de démonstration', 'Articles de démonstration', 'manage_options', 'snd-media-demo', 'snd_media_demo_page' );
}

function snd_media_demo_ids() {
	return get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'any',
		'meta_key'       => '_snd_demo',
		'meta_value'     => 1,
		'fields'         => 'ids',
		'posts_per_page' => -1,
	) );
}

function snd_media_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$deleted = null;
	if ( isset( $_POST['snd_delete_demo'] ) && check_admin_referer( 'snd_delete_demo' ) ) {
		$deleted = 0;
		foreach ( snd_media_demo_ids() as $id ) {
			if ( wp_delete_post( $id, true ) ) {
				$deleted++;
			}
		}
		do_action( 'litespeed_purge_all' );
	}
	$count = count( snd_media_demo_ids() );
	echo '<div class="wrap"><h1>Articles de démonstration</h1>';
	if ( null !== $deleted ) {
		echo '<div class="notice notice-success"><p>' . esc_html( $deleted ) . ' article(s) de démonstration supprimé(s).</p></div>';
	}
	echo '<p>Le site contient actuellement <strong>' . esc_html( $count ) . '</strong> article(s) de démonstration, tous marqués « [Démo] ». Vos propres articles ne sont pas concernés.</p>';
	if ( $count ) {
		echo '<form method="post">';
		wp_nonce_field( 'snd_delete_demo' );
		echo '<p><button class="button button-primary" name="snd_delete_demo" value="1" onclick="return confirm(\'Supprimer définitivement les articles de démonstration ?\');">Supprimer les articles de démonstration</button></p>';
		echo '</form>';
	}
	echo '</div>';
}
