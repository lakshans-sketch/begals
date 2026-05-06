<?php
/**
 * Displays footer widgets
 * Included in footer.php
 */
?>

<div class="footer-widget-box bagels-relative" id="footer-widget-area">
    <div class="footer-widgets h-p-ul-m-0">
        <div class="container">
            <div class="footer-widgets-l-1 bagels-flex bagels-justify-space-between bagels-flex-wrap">
                <div class="footer-widgets-l-2">
                    <?php if (!empty(BTS::$options['contact_details']['phone_number_1'])): ?>
                            <div class="contact-group bagels-flex bagels-vertical-align-baseline">
                                <h4 class="cg-heading">
                                    <span class="cg-h-icon"><i class="fas fa-phone"></i></span> 
                                </h4>
                                <p class="cg-text">
                                    <?php printf('<a href="tel:%1$s" aria-label="' . __("Call us at" . BTS::$options['contact_details']['phone_number_1'], 'bagels') . '">%1$s</a>', esc_html(BTS::$options['contact_details']['phone_number_1'])); ?>
                                </p>
                            </div>
                    <?php endif; ?>

                    <?php if (!empty(BTS::$options['contact_details']['email_address'])): ?>
                            <div class="contact-group bagels-flex bagels-vertical-align-baseline">
                                <h4 class="cg-heading">
                                    <span class="cg-h-icon"><i class="fas fa-envelope"></i></span> 
                                </h4>
                                <p class="cg-text"><?php printf('<a href="mailto:%1$s" aria-label="' . __("Mail to us at " . BTS::$options['contact_details']['email_address'], 'bagels') . '">%1$s</a>', esc_html(BTS::$options['contact_details']['email_address'])); ?></p>
                            </div>
                    <?php endif; ?>

                    <?php if (!empty(BTS::$options['contact_details']['address'])): ?>
                            <div class="contact-group bagels-flex bagels-vertical-align-baseline">
                                <h4 class="cg-heading">
                                    <span class="cg-h-icon"><i class="fas fa-map-marker-alt"></i></span> 
                                </h4>
                                <p class="cg-text"><?php echo BTS::$options['contact_details']['address']; ?></p>
                            </div>
                    <?php endif; ?>
                </div>

                <div class="footer-column">
                    <h5>Shop</h5>
                    <ul>
                        <li><a href="#">Coffee Tables</a></li>
                        <li><a href="#">TV Stands</a></li>
                        <li><a href="#">Beds & Wardrobes</a></li>
                        <li><a href="#">Dining Sets</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Our Blog</a></li>
                        <li><a href="#">Sustainability</a></li>
                    </ul>
                </div>
                
                <div class="footer-widgets-l-2">
                    <?php
                    echo do_shortcode('[newsletter_widget]');
                    echo do_shortcode('[social_icons_widget]');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>