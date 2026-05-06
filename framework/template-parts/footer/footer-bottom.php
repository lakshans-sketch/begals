<?php
/**
 * Displays footer bottom area.
 * Included in footer.php
 */
?>

<!-- Footer Bottom -->
<?php if ( !empty( BTS::$options['footer']['copyright_text'] ) ): ?>
	<div id="footer-bottom">
	    <div class="container">
			<div class="fb-1">
				<?php if ( !empty( BTS::$options[ 'footer' ] ) && !empty( BTS::$options[ 'footer' ][ 'copyright_text' ] ) ): ?>
					<div class="copyright-note"><?php echo html_entity_decode( stripslashes( nl2br( BTS::$options[ 'footer' ][ 'copyright_text' ] ) ) ); ?></div>
				<?php endif; ?>

				<div class="fb-site-creater">
					<div class="fb-sc-text"><?php _e( "Solution by", 'bagels' ); ?></div>

					<a href="https://domedia.lk?ref=<?php echo esc_url( home_url() ); ?>" target="_blank" title="Domedia.lk" class="fb-sc-link" aria-label="<?php _e( "Site creator", 'bagels' ); ?>">
						<?php $dm_path = '/framework/assets/images/domedia-logo-fav.jpg'; ?>

						<?php if ( file_exists( THEME_DIRECTORY . $dm_path ) ): ?>
							<div class="fb-sc-l-img">
								<img src="<?php echo THEME_DIRECTORY_URI . $dm_path; ?>" alt="<?php _e( "Domedia logo", 'bagels' ); ?>">
							</div>
						<?php endif; ?>

						<div class="fb-sc-l-name">DoMedia</div>
					</a>
				</div>
			</div>
	    </div>
	</div>
<?php endif; ?>
<!-- End Footer Bottom -->