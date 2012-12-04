/**
 * Hammy Plugin for WordPress
 * - Remove hard-coded caption sizes
 * - Fire off Responsive Images
*/

 jQuery(document).ready(function($){
 
	$('.wp-caption').css('width','');
    $('picture.hammy-responsive').picture({
		container: imageParent
	});
	
});