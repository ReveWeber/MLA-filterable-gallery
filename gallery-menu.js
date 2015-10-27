jQuery(document).ready(function($) {
        $( '#album-selector' ).addClass( 'album-with-js' );
        $( '#mla-parent-categories' ).hide();
        $( '#album-button' ).click( function() {
                $( '#mla-parent-categories' ).slideToggle();
        });
        $( '#current-album a' ).attr( 'rel', 'lightbox[album]' );

        $( '#mla-parent-categories a' ).click( function( e ) {
                e.preventDefault();
                if ( ( $( 'body' ).outerWidth() <= 1050 ) 
                    && ( $(this).hasClass( 'parent-link' ) ) ) {
                        $( '#mla-parent-categories a' ).removeClass( 'hover' );
                        $(this).addClass( 'hover' );
                } else {
                    var albumUrl = '?album=' + $(this).attr( 'data-slug' );
                    var albumSource = albumUrl + ' #current-album';
                    $( '#current-album-wrapper' ).load( albumSource, function() {
                            $( '#current-album a' ).attr( 'rel', 'lightbox[album]' );
                    });
                    $( '#mla-parent-categories' ).hide();
                    $( '#mla-parent-categories a' ).removeClass( 'hover' );
                    window.history.pushState( '', '', albumUrl );
                }
        });
    $( document ).on( 'click', function(event) {
				if ( ! $( event.target ).closest( "#album-selector" ).length) {
					$( '#mla-parent-categories a' ).removeClass( 'hover' );
                    $( '#mla-parent-categories' ).slideUp();
				}
		});

});