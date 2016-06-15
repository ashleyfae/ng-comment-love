<?php
/**
 * Underscore JS Template
 *
 * Displays a list of all the posts.
 *
 * @package   ng-commentlove
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

?>
<script id="tmpl-ng-commentlove" type="text/html">
	
	<ul>

		<li>
			<input type="radio" id="no-love" name="cl_post_title" value="" data-url="">
			<label for="no-love"><?php _e( 'None', 'ng-commentlove' ); ?></label>
		</li>

		<# _.each( data.posts, function( post ) { #>

			<li>
				<input type="radio" id="{{ post.ID }}" name="cl_post_title" value="{{ post.title }}" data-url="{{ post.link }}" <# if (post.selected) { #> checked<# } #>>
				<label for="{{ post.ID }}">{{{ post.title }}}</label>
			</li>

		<# }); #>
		
	</ul>

</script>
