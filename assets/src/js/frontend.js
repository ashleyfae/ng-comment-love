/* global NGLOVE */

import {apiRequest} from "./util/api";

/**
 * Set up initial listeners on DOM load.
 */
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

/**
 * Retrieves the entered site URL.
 *
 * @returns {string}
 */
function getSiteUrl() {
	let siteUrl = NGLOVE.website_url;
	const websiteElement = document.getElementById( 'url' );
	if ( siteUrl === '' && websiteElement ) {
		siteUrl = websiteElement.value;
	}

	return siteUrl;
}

/**
 * Fetches blog posts from the specified URL.
 *
 * @param {string} url
 */
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

/**
 * Builds the HTML list of blog posts.
 *
 * @param {object[]} posts
 * @returns {string}
 */
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
 * Builds the HTML for a single post.
 *
 * @param {object} post
 */
function buildPost( post ) {
	const selectedHtml = post.isSelected ? 'checked' : '';

	return `<li>
	<input type="radio" id="${post.identifier}" name="cl_post_title" value="${post.title}" data-url="${post.url}" ${selectedHtml}>
	<label for="${post.identifier}">${post.title}</label>
</li>
`;
}

/**
 * When the selected radio button changes, update the hidden field to
 * set the selected URL.
 */
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

/**
 * Saves the selected URL in a hidden field.
 *
 * @param {HTMLElement} radio
 */
function setSelectedUrl( radio ) {
	const postUrlEl = document.getElementById( 'cl_post_url' );
	if ( ! postUrlEl ) {
		return;
	}

	postUrlEl.value = radio.getAttribute( 'data-url' );
}

/**
 * Adds an error message to the DOM.
 *
 * @param {string} message
 */
function addErrorMessage( message ) {
	const messagesWrap = document.getElementById( 'comment-love-messages' );
	if ( messagesWrap ) {
		messagesWrap.innerHTML = '<p class="comment-love-error">' + message + '</p>';
	} else {
		console.log( 'NG Comment Love Error', message );
	}
}

/**
 * Determines if the supplied URL looks valid.
 *
 * @param {string} url
 * @returns {boolean}
 */
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
