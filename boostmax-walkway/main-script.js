"use sctict";

(function( $ ) {
    /**
     * Wraps title section proprties elements
     */

    function booxMaxPageActions(){
        let self = this;
        
        self.wrapTSProprties = function(){
            setTimeout(function(){
                let boostMaxPage = $('#collection-64e2e69e8a01a67dc4022a90');
                let targets = boostMaxPage.find('.fe-block-82bbf7a68ae289f2b38f, .fe-block-83fd7acd3b62eb33eaab');
                let propWrapper;
                let phoneTextElem = boostMaxPage.find('.fe-block-b702c4f37915116d95e8');
                if (targets.length) { targets.wrapAll('<div class="ts-prop-wrapper"></div>'); }
                
                propWrapper = boostMaxPage.find('.ts-prop-wrapper');
    
                if (propWrapper.length && phoneTextElem.length) {
                    propWrapper.insertAfter(phoneTextElem);
                }
            }, 1500);
        }
        
        self.wrapCFClients = function(){
            setTimeout(function(){
                let boostMaxPage = $('#collection-64e2e69e8a01a67dc4022a90');
                let targets = boostMaxPage.find('.fe-block-a8bd5e1cb24907bc752c, .fe-block-25419ec70beba9d937ca');
                let formElement = boostMaxPage.find('.fe-64e2e69e8a01a67dc4022a97');
                if (targets.length) {
                    formElement.append($('<div class="do-ls-cf-clients-wrapper"></div>'));
                    $('.do-ls-cf-clients-wrapper').append(targets);
                }
                
            }, 1500);
        }

        self.wrapCFClients();
        self.wrapTSProprties();
    }

    
    new booxMaxPageActions()
})( jQuery );