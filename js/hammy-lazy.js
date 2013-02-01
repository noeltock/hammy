/**
 * Hammy Plugin for WordPress
 * - Remove hard-coded caption sizes
 * - Fire off Responsive Images
 * - Move figcaption below img
 * - Cast Lazy Loader magic
*/

 jQuery(document).ready(function($){
 
	$('.wp-caption').css('width','');
    $('picture.hammy-responsive').picture({
		container: imageParent
	});
	
	$('picture.hammy-responsive').each(function() {
		var caption = $(this).find('figcaption');
		$(this).append(caption);
  	});
	
	$("img.lazy").lazyload({effect: "fadeIn"});
});