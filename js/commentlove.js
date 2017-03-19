jQuery.noConflict();

jQuery(document).ready(function ($) {

	var siteURL;

	if (NGLOVE.website_url != '') {
		siteURL = NGLOVE.website_url;
	} else {
		siteURL = $('#url').val()
	}

	var getPostsButton = $('#ng-cl-get-posts');
	var clMessageField = $('#cl_messages');
	var clLatestPosts = $('#cl_latest_posts');

	var NG_CommentLove = {

		init: function () {

			if (siteURL) {
				NG_CommentLove.getPosts();
			}

			getPostsButton.click(function (e) {
				e.preventDefault();

				NG_CommentLove.getPosts();
			});

		},

		/**
		 * Checks to make sure the URL is valid.
		 */
		checkURL: function () {

			if (!siteURL) {
				siteURL = $('#url').val();
			}

			if (typeof siteURL == 'undefined') {
				return false;
			}

			// There's nothing there!
			if (!siteURL.length > 1) {
				return false;
			}

			// Is the http:// missing?
			if (siteURL.toLowerCase().substring(0, 7) != 'http://' && siteURL.toLowerCase().substring(0, 8) != 'https://') {
				return false;
			}

			// Otherwise we're good to go.
			return true;

		},

		/**
		 * Get Posts
		 */
		getPosts: function () {

			clMessageField.empty();
			NG_CommentLove.addSpinner();

			// URL is invalid - bail.
			if (NG_CommentLove.checkURL() == false) {
				clMessageField.append('<p class="comment-love-error">' + NGLOVE.message_no_url + '</p>');
				NG_CommentLove.removeSpinner();

				return false;
			}

			var data = {
				action: 'ng_get_latest_blog_post',
				url: siteURL,
				nonce: NGLOVE.nonce
			};

			$.ajax({
				type: 'POST',
				data: data,
				url: NGLOVE.ajaxurl,
				xhrFields: {
					withCredentials: true
				},
				success: function (response) {

					// Remove the spinner.
					NG_CommentLove.removeSpinner();

					// Failed.
					if (response.success != true) {
						clMessageField.empty().append(response.data);

						return;
					}

					var clPostsTemplate = wp.template('ng-commentlove');
					var clTemplateData = {posts: response.data};

					clLatestPosts.empty().append(clPostsTemplate(clTemplateData));

					NG_CommentLove.populateHiddenField();

				}
			});

		},

		/**
		 * Add Spinner
		 */
		addSpinner: function () {
			getPostsButton.attr('disabled', true);
			getPostsButton.after('<i id="ng-love-spinner" class="fa fa-spinner fa-spin"></i>');
		},

		/**
		 * Remove Spinner
		 */
		removeSpinner: function () {
			getPostsButton.attr('disabled', false);
			$('#ng-love-spinner').remove();
		},

		/**
		 * Populates the hidden field based on which post is selected.
		 */
		populateHiddenField: function () {

			$('input[name="cl_post_title"]').change(function () {
				var url = $(this).data('url');
				$('#cl_post_url').val(url);
			});

		}

	};

	NG_CommentLove.init();

});