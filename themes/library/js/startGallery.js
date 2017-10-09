function startGallery() {
		var myGallery = new gallery($('myGallery'), {
			timed: true,
			delay: 4000,
			showArrows: false,
			showCarousel: true,
			embedLinks: false
		});
	}
window.addEvent('domready',startGallery);



