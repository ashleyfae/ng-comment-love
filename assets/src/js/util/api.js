/* global NGLOVE */

export function apiRequest( endpoint, method, body = {} ) {
    const args = {
        method,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': NGLOVE.restNonce
        }
    };

    if ( Object.keys( body ).length ) {
        args.body = JSON.stringify( body );
    }

    return fetch( NGLOVE.restBase + endpoint, args )
        .then( response => {
            if ( ! response.ok ) {
                return Promise.reject( response );
            }

            return response.json();
        } );
}
