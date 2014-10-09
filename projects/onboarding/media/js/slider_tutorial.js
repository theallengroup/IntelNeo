$(document).ready(function(){
	$("#close").click(function(){
		$('#tutorial_container').hide("swing"); //oculto mediante id
	});

	if( typeof(DragSlideshow) != 'undefined' ) {
		var overlay = document.getElementById( 'overlay' ),
			overlayClose = overlay.querySelector( 'button' ),
			toggleBtnn = function() {
				if( slideshow.isFullscreen ) {
					classie.add( switchBtnn, 'view-maxi' );
				}
				else {
					classie.remove( switchBtnn, 'view-maxi' );
				}
			},
			toggleCtrls = function() {
				if( !slideshow.isContent ) {
					classie.add( header, 'hide' );
				}
			},
			toggleCompleteCtrls = function() {
				if( !slideshow.isContent ) {
					classie.remove( header, 'hide' );
				}
			},
			slideshow = new DragSlideshow( document.getElementById( 'slideshow' ), { 
				// toggle between fullscreen and minimized slideshow
				onToggle : toggleBtnn,
				// toggle the main image and the content view
				onToggleContent : toggleCtrls,
				// toggle the main image and the content view (triggered after the animation ends)
				onToggleContentComplete : toggleCompleteCtrls
			}),
			toggleSlideshow = function() {
				slideshow.toggle();
				toggleBtnn();
			},
			closeOverlay = function() {
				classie.add( overlay, 'hide' );
			};

		// zahmad: currently there is no switchBtnn defined
		// toggle between fullscreen and small slideshow
		// if( switchBtnn ) {
		// 	switchBtnn.addEventListener( 'click', toggleSlideshow );
		// }

		// close overlay
		if( overlayClose ) {
			overlayClose.addEventListener( 'click', closeOverlay );
		}
	}
});
