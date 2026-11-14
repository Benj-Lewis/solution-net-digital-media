</div><!-- #contenu -->

<footer class="snd-footer">
	<div class="snd-wrap">
		<div class="snd-footer-grid">
			<div class="snd-footer-about">
				<?php snd_brand( 'footer' ); ?>
				<p><?php echo esc_html( snd_opt( 'snd_about' ) ); ?></p>
				<?php snd_social_links( 'snd-social snd-social--footer' ); ?>
			</div>
			<div>
				<h2 class="snd-footer-title">Rubriques</h2>
				<?php snd_menu_fallback( array( 'menu_class' => 'snd-footer-cats' ) ); ?>
			</div>
			<div>
				<h2 class="snd-footer-title">Le média</h2>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'snd-footer-links', 'depth' => 1 ) );
				}
				?>
			</div>
			<div>
				<h2 class="snd-footer-title">Contact rédaction</h2>
				<ul class="snd-footer-contact">
					<?php
					$addr  = snd_opt( 'snd_address' );
					$phone = snd_opt( 'snd_phone' );
					$email = snd_opt( 'snd_email' );
					if ( $addr ) {
						echo '<li>' . snd_icon( 'pin', 16 ) . '<span>' . esc_html( $addr ) . '</span></li>'; // phpcs:ignore
					}
					if ( $phone ) {
						echo '<li>' . snd_icon( 'phone', 16 ) . '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></li>'; // phpcs:ignore
					}
					if ( $email ) {
						echo '<li>' . snd_icon( 'mail', 16 ) . '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>'; // phpcs:ignore
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<div class="snd-footer-bottom">
		<div class="snd-wrap snd-footer-bottom-in">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. Tous droits réservés.</span>
			<?php if ( get_privacy_policy_url() ) : ?>
				<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Politique de confidentialité</a>
			<?php endif; ?>
		</div>
	</div>
</footer>
<button class="snd-totop" type="button" aria-label="Revenir en haut" hidden><?php echo snd_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
<?php wp_footer(); ?>
</body>
</html>
