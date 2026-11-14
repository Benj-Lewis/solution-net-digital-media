<?php
/**
 * Contenu de démonstration : titres de type « dossier pratique » (aucune information
 * présentée comme un fait d'actualité) et photos d'illustration génériques.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function snd_demo_titles() {
	return array(
		'actualite' => array(
			'Ce qu\'il faut retenir de la semaine en cinq points',
			'Transport urbain : comment mieux se déplacer aux heures de pointe',
			'Coupures d\'électricité : les bons réflexes pour protéger vos appareils',
			'Le guide des démarches administratives les plus demandées',
			'Saison des pluies : comprendre les prévisions météo',
		),
		'politique' => array(
			'Comment fonctionne le travail des commissions parlementaires',
			'Décentralisation : cinq questions pour comprendre le rôle des provinces',
			'Lire un budget public : le guide pour les citoyens',
			'Élections : les étapes d\'un scrutin, de la candidature aux résultats',
			'Motion de censure : ce que cela change, expliqué simplement',
		),
		'securite'  => array(
			'Sécurité routière : les gestes qui sauvent sur la route',
			'Arnaques en ligne : comment repérer un faux message',
			'Protéger son téléphone : six réglages à activer dès aujourd\'hui',
			'En cas d\'urgence : les numéros utiles et les bons réflexes',
			'Quartiers : comment s\'organisent les comités de vigilance',
		),
		'economie'  => array(
			'Inflation : cinq repères pour comprendre la hausse des prix',
			'Mobile money : ce qu\'il faut savoir avant d\'envoyer de l\'argent',
			'Créer son entreprise : les étapes clés pour se lancer',
			'Marchés : comment se forme le prix des produits vivriers',
			'Épargne : les solutions accessibles pour mettre de l\'argent de côté',
		),
		'sport'     => array(
			'Football : comment une équipe prépare un grand rendez-vous',
			'Basket-ball : les règles essentielles pour suivre une rencontre',
			'Courir en ville : les conseils pour bien débuter',
			'Entraîneur, arbitre, préparateur : les métiers du sport',
			'Comprendre un classement de championnat en trois minutes',
		),
		'societe'   => array(
			'Vie de quartier : quand l\'entraide s\'organise',
			'Jeunesse : les pistes pour décrocher un premier emploi',
			'École : bien accompagner son enfant dans ses devoirs',
			'Logement : les questions à poser avant de signer un bail',
			'Famille : droits et devoirs expliqués simplement',
		),
		'culture'   => array(
			'Rumba : histoire d\'un rythme devenu patrimoine',
			'Les instruments traditionnels et leur place dans les cérémonies',
			'Créer un club de lecture dans son quartier',
			'Photographe de presse, un métier entre art et témoignage',
			'Mode : quand le pagne inspire les créateurs',
		),
		'femme'     => array(
			'Entrepreneuriat féminin : les clés pour développer son activité',
			'Leadership : ces parcours qui ouvrent la voie',
			'Santé maternelle : les consultations à ne pas manquer',
			'Concilier vie professionnelle et vie de famille',
			'Financement : les pistes pour les porteuses de projet',
		),
		'justice'   => array(
			'Comment se déroule un procès, étape par étape',
			'Avocat, juge, greffier : qui fait quoi au tribunal',
			'Litige foncier : les documents à conserver',
			'Droit du travail : ce que doit contenir un contrat',
			'Porter plainte : la marche à suivre',
		),
		'sante'     => array(
			'Paludisme : les gestes de prévention au quotidien',
			'Eau potable : comment la rendre sûre à la maison',
			'Vaccination : le calendrier expliqué aux parents',
			'Hypertension : surveiller sa tension sans s\'inquiéter',
			'Bien manger avec un petit budget',
		),
		'afrique'   => array(
			'Le fleuve Congo, artère vitale de l\'Afrique centrale',
			'Libre-échange continental : ce que cela change pour les entreprises',
			'Les grandes villes africaines face à la croissance urbaine',
			'Énergie solaire : un potentiel encore sous-exploité',
			'Paiement mobile : le numérique transforme les services sur le continent',
		),
	);
}

function snd_demo_body( $cat_name ) {
	return '<p><strong>Article de démonstration.</strong> Ce texte sert à montrer la mise en page du site ; il ne rapporte aucun fait réel. Remplacez-le par votre propre article, ou supprimez tous les articles de démonstration en un clic depuis <em>Apparence &gt; Articles de démonstration</em>.</p>' . "\n\n"
		. '<p>Le premier paragraphe, le chapô, résume l\'essentiel en deux ou trois phrases : qui, quoi, où, quand. Il apparaît sous le titre et dans les listes de la rubrique ' . esc_html( $cat_name ) . '.</p>' . "\n\n"
		. '<h2>Un intertitre pour structurer la lecture</h2>' . "\n\n"
		. '<p>Le corps de l\'article développe l\'information : le contexte, les déclarations, les chiffres et les réactions. Des paragraphes courts se lisent mieux sur téléphone, où se trouve la majorité des lecteurs.</p>' . "\n\n"
		. '<blockquote><p>Une citation mise en avant donne du rythme à la page et attire l\'œil sur une déclaration importante.</p></blockquote>' . "\n\n"
		. '<p>Les intertitres, les listes et les citations aident aussi le référencement : Google comprend mieux un texte bien structuré.</p>' . "\n\n"
		. '<ul><li>Un point clé à retenir</li><li>Un deuxième point important</li><li>Une information pratique pour le lecteur</li></ul>' . "\n\n"
		. '<p>La fin de l\'article peut rappeler les prochaines étapes ou renvoyer vers d\'autres articles de la même rubrique.</p>';
}

/**
 * Photos d'illustration (générées pour la démonstration). Liens temporaires :
 * le serveur les télécharge dans la médiathèque à l'installation.
 */
function snd_demo_images() {
	$file = get_template_directory() . '/inc/demo-images.txt';
	$out  = array();
	if ( ! is_readable( $file ) ) {
		return $out;
	}
	foreach ( file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
		$parts = explode( '|', trim( $line ), 2 );
		if ( 2 === count( $parts ) ) {
			$out[] = array( 'cat' => $parts[0], 'url' => $parts[1] );
		}
	}
	return $out;
}
