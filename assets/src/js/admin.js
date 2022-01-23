/* global NGLOVE */

import {apiRequest} from "./util/api";

document.addEventListener( 'DOMContentLoaded', () => {
    const removeLoveButton = document.getElementById( 'comment-love-remove-love' );
    const container = document.getElementById( 'ng-comment-love-wrap' );
    if ( ! removeLoveButton || ! container ) {
        return;
    }

    removeLoveButton.addEventListener( 'click', ( e ) => {
        e.preventDefault();

        const commentId = removeLoveButton.getAttribute( 'data-comment-id' );
        if ( ! commentId ) {
            console.log( 'No comment ID.' );
            return;
        }

        removeLoveButton.disabled = true;
        removeLoveButton.classList.add( 'updating-message' );

        apiRequest( '/comments/' + commentId + '/love', 'DELETE' )
            .then( response => {
                container.innerHTML = response.message ? '<p>' + response.message + '</p>' : '';
            } )
            .catch( error => {
                console.log( 'Error', error );
                removeLoveButton.disabled = false;
                removeLoveButton.classList.remove( 'updating-message' );

                const errorWrap = document.getElementById('ng-comment-love-errors');
                if (errorWrap) {
                    errorWrap.innerHTML = '<div class="notice notice-error inline"><p>' + NGLOVE.removeError + '</p></div>';
                }
            } );
    } );
} );
