"use strict";

jQuery(document).ready(function($){
    function newsLetterSubscription(){
        let newsletterForm = $( '.bagels-news-letter-widget .nlw-form' );

        if (newsletterForm.length){
            newsletterForm.on( 'submit', function(e){
                e.preventDefault();
                let form = $(this),
                btn = form.find('#nlw-f-b-submit'),
                response_output = form.find('.nlw-f-msg');

                if (btn.length){ btn.addClass( 'loading' ); }
                if (response_output.length){ response_output.hide(); }

                $.ajax({
                    type: 'post',
                    url: myAjax.url,
                    data: { 
                        'action': 'send_newsletter_request',
                        'nonce': myAjax.nonce,
                        'email': form.find( '#nlw-f-i-email' ).val()
                    },
                })
                .success( function( response ) {
                    if (response_output.length){
                        if( response.success ){
                            response_output.addClass('text-success');
                            response_output.html("Thank you for subscribing!");
                            form.trigger( 'reset' );
                        }else{
                            response_output.addClass('text-danger');
                            response_output.html( response.hasOwnProperty('data') ? response.data : "Something went wrong please try again." );
                        }
                    }
                })
                .always( function( response ) {
                    console.log(response);
                    if (btn.length){ btn.removeClass( 'loading' ); }
                    if (response_output.length){ response_output.fadeIn('fast'); }
                });
            });
        }
    }
    
    new newsLetterSubscription();
});