"use strict";

jQuery(document).ready(function($){
    function accessibilityWidgetActions(){
        let self = this;
        self.DOMbody = $('html');
        self.widget = $('.bagels-accessibility-widget');
        self.fontSize = 110;
        self.menuItemSelected = 'selected';
        self.resizeClassesCommonText = "bagels-abtw-resize-font-";
        self.fzResizedClass = "bagels-abtw-font-resized";
        self.readaleFontClass = 'bagels-abtw-readable-font';
        self.linksUnderlineClass = 'bagels-abtw-links-underline';
        self.lightBgClass = 'bagels-abtw-light-background';
        self.greyScaleClass = 'bagels-abtw-greyscale';
        
        if ( self.widget.length ) {            
            /* Toggles visibility of the accessibility menu */
            self.toggleMenuVisibility = function(){
                let visibilityToggler = self.widget.find('.abtw-tt-link');
                
                visibilityToggler.on('click', function(e){
                    e.preventDefault();
                    self.widget.toggleClass('open');
                });
            };
            
            /* Handles all click events related to the accessibility menu */
            self.clickEventHandler = function(){
                self.menuItems = self.widget.find('.abtw-t-i-s-link');

                if ( self.menuItems.length ) {
                    self.menuItems.on('click', function(e){
                        e.preventDefault();

                        switch ( $(this).data('bagels-abtw-action') ) {                                
                            case 'bagels-abtw-increase-font-size':
                                self.increaseFontSize();
                            break;

                            case 'bagels-abtw-decrease-font-size':
                                self.decreaseFontSize();
                            break;
                            
                            case 'bagels-abtw-greyscale':
                                self.greyscaleDom( $(this) );
                            break;
                            
                            case 'bagels-abtw-light-background':
                                self.giveDomLightBg( $(this) );
                            break;
                            
                            case 'bagels-abtw-links-underlined':
                                self.underlineLinks( $(this) );
                            break;
                            
                            case 'bagels-abtw-readable-font':
                                self.giveReadableFontFamily( $(this) );
                            break;
                            
                            case 'bagels-abtw-reset':
                                self.unsetAccessibilitySettings();
                            break;
                        
                            default:
                            break;
                        }
                    });
                }
            };
            
            /* Increases document font size */
            self.increaseFontSize = function(){
                self.fontSize += 10;
                if ( !self.DOMbody.hasClass() ) { self.DOMbody.addClass(self.fzResizedClass); }
                
                if (self.fontSize <= 200) {
                    self.removeAllResizeClasses();
                    let resizeClass = self.resizeClassesCommonText + self.fontSize;
                    self.DOMbody.addClass(resizeClass);
                }else{
                    self.fontSize = 200;
                }
            };
            
            /* Decreases document font size */
            self.decreaseFontSize = function(){
                self.removeAllResizeClasses();
                self.fontSize -= 10;
                
                if (self.fontSize >= 120) {
                    let resizeClass = self.resizeClassesCommonText + self.fontSize;
                    self.DOMbody.addClass(resizeClass);
                }else{
                    self.DOMbody.removeClass(self.fzResizedClass);
                    self.fontSize = 110;
                }
            };
            
            /* Remove all font resize related classes from the body */
            self.removeAllResizeClasses = function(){
                for (let index = 120; index <= 200; index+=10) {
                    let resizeClass = self.resizeClassesCommonText + index;
                    self.DOMbody.removeClass(resizeClass);
                }
            };
            
            /* Greyscale DOM */
            self.greyscaleDom = function(clickedItem){
                clickedItem.toggleClass(self.menuItemSelected);
                self.DOMbody.toggleClass(self.greyScaleClass);
            }
            
            /* Gives light background to DOM elements */
            self.giveDomLightBg = function(clickedItem){
                clickedItem.toggleClass(self.menuItemSelected);
                self.DOMbody.toggleClass(self.lightBgClass);
            }
            
            /* Underlines all anchor tags */
            self.underlineLinks = function(clickedItem){
                clickedItem.toggleClass(self.menuItemSelected);
                self.DOMbody.toggleClass(self.linksUnderlineClass);
            }
            
            /* Gives DOM text a readable font family */
            self.giveReadableFontFamily = function(clickedItem){
                clickedItem.toggleClass(self.menuItemSelected);
                self.DOMbody.toggleClass(self.readaleFontClass);
            }
            
            /* Removes all added accessibility settings */
            self.unsetAccessibilitySettings = function(){
                self.fontSize = 110;
                self.removeAllResizeClasses();
                self.DOMbody.removeClass(self.greyScaleClass + ' ' + self.lightBgClass + ' ' + self.linksUnderlineClass + ' ' + self.readaleFontClass + ' ' + self.fzResizedClass);
                self.widget.find('.abtw-t-i-s-link').removeClass(self.menuItemSelected);
            }

            self.toggleMenuVisibility();
            self.clickEventHandler();
        }
    }

    new accessibilityWidgetActions();
});