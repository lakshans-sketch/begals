/**
 * Theme Gallery Script file.
 * Contains project gallery regards to this theme.
 */

"use strict";

(function( $ ) {
    
/* ---------------------------------
    Load Projects By Ajax
   --------------------------------- */
    function loadProjectsByAjax(){
        var cachedProjects = []; // Cached projects array
        var $content = $('.gallery-container');
        var $loader = $('.ajax-loader');
        
        $('.gallery-filter li').click(function(){
            var filterId = $(this).attr("data-filter");
            var filterCount = $(this).attr("data-count");

            $(this).siblings().removeClass('current');
            $(this).addClass('current');
            $content.html('');
            
            var searchResult = $.grep(cachedProjects, function(e){ 
                return e.id === filterId; 
            });
            
            if (searchResult.length === 0) {
                jQuery.ajax({
                    type : 'post',
                    url : ajax_object.ajaxurl,
                    data : {
                        action : 'loadProjectsByAjax',
                        projectId : filterId,
                        filterCount : filterCount
                    },
                    beforeSend: function() {
                        $loader.show();
                    },
                    success : function( response ) {
                        $loader.hide();
                        $content.html(response);

                        cachedProjects.push({
                            id: filterId, 
                            projects:  response.replace(/\s*\n\s*/g,"")
                        });                   
                    }
                });
            }else{
                // Using cached data
                $content.html(searchResult[0].projects);
            }
        });
    }    
    

    // Initialize the functions
    loadProjectsByAjax();
})( jQuery );