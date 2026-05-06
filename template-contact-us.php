<?php
/* Template Name: Contact Us */

get_header(); 

//Page Header Banner
echo page_header_banner();

if( BTS::$options['page_settings']['breadcumbs'] ): ?>
    <div class="breadcumb-container">
        <div class="container">
            <?php theme_breadcrumbs(); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Page Container -->
<div class="page-container pg-temp-contact">
	<div class="container">
		<div class="row pg-tc-row">
			<div class="col-md-6 col-lg-5">
				<div class="ptc-form">
					<?php echo do_shortcode( '[contact-form-7 id="207" title="Contact form 1"]' ); ?>
				</div>
			</div>

			<div class="col-md-6 col-lg-5">
				<div class="contact-info-bar">
					<?php if( !empty( BTS::$options['contact_details']['phone_number_1'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading">
								<span class="cg-h-icon"><i class="fas fa-phone"></i></span> 
								<?php _e( 'Hotline', 'bagels' ); ?>
							</h4>
							<p class="cg-text">
								<?php printf( '<a href="tel:%1$s" aria-label="' . __( "Call us at" . BTS::$options['contact_details']['phone_number_1'], 'bagels' ) . '">%1$s</a>', esc_html( BTS::$options['contact_details']['phone_number_1'] ) ); ?>
							</p>
						</div>
					<?php endif; ?>

					<?php if( !empty( BTS::$options['contact_details']['phone_number_2'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading">
								<span class="cg-h-icon"><i class="fas fa-phone"></i></span>
								<?php _e( 'Phone', 'bagels' ); ?>
							</h4>
							<p class="cg-text"><?php printf( '<a href="tel:%1$s" aria-label="' . __( "Call us at" . BTS::$options['contact_details']['phone_number_2'], 'bagels' ) . '">%1$s</a>', esc_html( BTS::$options['contact_details']['phone_number_2'] ) ); ?></p>
						</div>
					<?php endif; ?>

					<?php if( !empty( BTS::$options['contact_details']['email_address'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading">
								<span class="cg-h-icon"><i class="fas fa-envelope"></i></span> 
								<?php _e( 'Email', 'bagels' ); ?>
							</h4>
							<p class="cg-text"><?php printf( '<a href="mailto:%1$s" aria-label="' . __( "Mail to us at " . BTS::$options['contact_details']['email_address'], 'bagels' ) . '">%1$s</a>', esc_html( BTS::$options['contact_details']['email_address'] ) ); ?></p>
						</div>
					<?php endif; ?>

					<?php if( !empty( BTS::$options['contact_details']['address'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading">
								<span class="cg-h-icon"><i class="fas fa-map-marker-alt"></i></span> 
								<?php _e( 'Address', 'bagels' ); ?>
							</h4>
							<p class="cg-text"><?php echo BTS::$options['contact_details']['address']; ?></p>
						</div>
					<?php endif; ?>

					<?php if( !empty( BTS::$options['contact_details']['opening_hours'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading">
								<span class="cg-h-icon"><i class="fas fa-clock"></i></span> 
								<?php _e( 'Open hours', 'bagels' ); ?>
							</h4>
							<p class="cg-text"><?php echo esc_html( BTS::$options['contact_details']['opening_hours'] ); ?></p>
						</div>
					<?php endif; ?>

					<?php if( !empty( BTS::$options['social_icons'] ) ): ?>
						<div class="contact-group">
							<h4 class="cg-heading"><?php _e( 'Follow us on', 'bagels' ); ?></h4>
							<?php echo do_shortcode('[social_icons_widget]') ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<?php if( !empty( BTS::$options[ 'contact_details' ][ 'location' ] ) ): ?>
		<div class="pg-tc-map"><?php echo BTS::$options[ 'contact_details' ][ 'location' ]; ?></div>
	<?php endif; ?>
</div>
<!-- End Page Container -->

<?php get_footer();