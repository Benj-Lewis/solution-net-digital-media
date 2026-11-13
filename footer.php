</div><!-- #contenu -->

<footer class="snd-footer">
	<div class="snd-footer-top"></div>
	<div class="snd-wrap">
		<div class="snd-footer-grid">
			<div>
				<?php snd_media_brand(); ?>
				<?php
				$about = get_theme_mod( 'snd_about' );
				if ( ! $about ) {
					$about = get_bloginfo( 'description' );
				}
				if ( $about ) {
					echo '<p>' . esc_html( $about ) . '</p>';
				}
				?>
			</div>
			<div>
				<h2>Rubriques</h2>
				<ul class="snd-footer-cats">
					<?php
					foreach ( snd_media_sections() as $slug => $name ) {
						$term = get_category_by_slug( $slug );
						if ( $term ) {
							echo '<li><a href="' . esc_url( get_category_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
						}
					}
					?>
				</ul>
			</div>
			<div>
				<h2>Contact</h2>
				<ul class="snd-footer-contact">
					<?php
					$address = get_theme_mod( 'snd_address' );
					$phone   = get_theme_mod( 'snd_phone' );
					$email   = get_theme_mod( 'snd_email' );
					if ( $address ) {
						echo '<li>' . esc_html( $address ) . '</li>';
					}
					if ( $phone ) {
						echo '<li><a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></li>';
					}
					if ( $email ) {
						echo '<li><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
					}
					foreach ( snd_media_socials() as $label => $url ) {
						echo '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a></li>';
					}
					if ( ! $address && ! $phone && ! $email && ! snd_media_socials() && current_user_can( 'edit_theme_options' ) ) {
						echo '<li><a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=snd_media' ) ) . '">Ajouter vos coordonnées et réseaux sociaux</a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<div class="snd-copy">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Tous droits réservés.</span>
			<?php if ( get_privacy_policy_url() ) : ?>
				<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Politique de confidentialité</a>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
