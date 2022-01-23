/* global NGLOVE */

import {apiRequest} from "./util/api";

document.addEventListener( 'DOMContentLoaded', () => {
	const siteUrl = getSiteUrl();

	if ( siteUrl ) {
		getPosts( siteUrl );
	}

	const fetchPostsEl = document.getElementById( 'comment-love-get-posts' );
	if ( fetchPostsEl ) {
		fetchPostsEl.addEventListener( 'click', e => {
			e.preventDefault();

			getPosts( getSiteUrl() );
		} )
	}
} );

function getSiteUrl() {
	let siteUrl = NGLOVE.website_url;
	const websiteElement = document.getElementById( 'url' );
	if ( siteUrl === '' && websiteElement ) {
		siteUrl = websiteElement.value;
	}

	return siteUrl;
}

function getPosts( url ) {
	const messagesWrap = document.getElementById( 'comment-love-messages' );
	const postsWrap = document.getElementById( 'comment-love-latest-posts' );

	messagesWrap.innerHTML = '';

	if ( ! isValidUrl( url ) ) {
		addErrorMessage( NGLOVE.message_no_url );
		return;
	}

	postsWrap.innerHTML = '<p>' + NGLOVE.loadingPosts + '</p>';

	apiRequest( '/posts', 'POST', {url} )
		.then( response => {
			console.log( 'Posts', response );
			if ( ! response.posts.length ) {
				throw new Error();
			}

			postsWrap.innerHTML = buildPostList( response.posts );

			addPostSelectListener();
		} )
		.catch( error => {
			console.log( 'Caught error', error );
			addErrorMessage( NGLOVE.noPostsFound );
			postsWrap.innerHTML = '';
		} )
}

function buildPostList( posts ) {
	return `<ul>
<li>
	<input type="radio" id="no-love" name="cl_post_title" value="" data-url="">
	<label for="no-love">${NGLOVE.noLove}</label>
</li>
${posts.map( buildPost ).join( "\n" )}
</ul>`;
}

/**
 *
 * @param {object} post
 */
function buildPost( post ) {
	const selectedHtml = post.isSelected ? 'checked' : '';

	return `
<li>
	<input type="radio" id="${post.identifier}" name="cl_post_title" value="${post.title}" data-url="${post.url}" ${selectedHtml}>
	<label for="${post.identifier}">${post.title}</label>
</li>
`;
}

function addPostSelectListener() {
	const postTitleInputs = document.getElementsByName( 'cl_post_title' );
	if ( ! postTitleInputs ) {
		return;
	}

	postTitleInputs.forEach( input => {
		if ( input.checked ) {
			setSelectedUrl( input );
		}

		input.addEventListener( 'change', e => {
			if ( input.checked ) {
				setSelectedUrl( input );
			}
		} );
	} );
}

function setSelectedUrl( radio ) {
	const postUrlEl = document.getElementById( 'cl_post_url' );
	if ( ! postUrlEl ) {
		return;
	}

	postUrlEl.value = radio.getAttribute( 'data-url' );
}

function addErrorMessage( message ) {
	const messagesWrap = document.getElementById( 'comment-love-messages' );
	if ( messagesWrap ) {
		messagesWrap.innerHTML = '<p class="comment-love-error">' + message + '</p>';
	} else {
		console.log( 'NG Comment Love Error', message );
	}
}

function isValidUrl( url ) {
	if ( typeof url == 'undefined' ) {
		return false;
	}

	// There's nothing there!
	if ( ! url.length > 1 ) {
		return false;
	}

	// Is the http:// missing?
	if ( url.toLowerCase().substring( 0, 7 ) !== 'http://' && url.toLowerCase().substring( 0, 8 ) !== 'https://' ) {
		return false;
	}

	// Otherwise we're good to go.
	return true;
}
