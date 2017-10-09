$("#TestButton").on("click",function(){

	$.CrystalNotification({
		title: "This is my title",   // Title
		image: "static/img/Colorfull/Messages-colorfull.png", // Image
		content: "Write some content...", // Content
		sound: true, // If you want to disabled or enabled the sound
		panelbutton: true, // If you want to show or not the button line that shows the Notification Center
		closebutton: true, // If you want to hide the Close button
		timeout: 4000, // Timeout to autoclose and execute a callback if is set
		addtopanel: true, // if you don't want to display it on the notification center
	});

});
