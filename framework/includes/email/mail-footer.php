<?php
/**
 * Email template footer
 */

$site_url = get_site_url();
?>

										</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- END MAIN CONTENT AREA -->
                    </table>

                    <!-- START FOOTER -->
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block powered-by">
                                    <?php if( !empty( BTS::$options['contact_details']['address'] ) ): ?>
                                        <span class="apple-link"><?php echo nl2br( BTS::$options['contact_details']['address'] ); ?></span>
                                    <?php endif; ?>

                                    <?php printf( '<a href="%1$s" target="_blank">%1$s</a>', esc_url( $site_url ) ); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END FOOTER -->

                    <!-- END CENTERED WHITE CONTAINER -->
                    </div>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </body>
</html>