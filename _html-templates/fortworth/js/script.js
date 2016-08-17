$(document).ready(function(){
	
	/* masonry layout */
    var $container = $('.container-property');
    $container.imagesLoaded( function(){
        $container.masonry();
    });
	
	$("#partners").owlCarousel();
   
});