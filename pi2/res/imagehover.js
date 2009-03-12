/*
 * Image preview script 
 * powered by jQuery (http://www.jquery.com)
 * 
 * written by Alen Grakalic (http://cssglobe.com)
 * 
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */
 
this.imagePreview = function(){	
	/* CONFIG */
		
		// absolute position for: top left:
		// xOffset = 250;
		// 	yOffset = -450;
		
		// absolute position for: right bottom:
		// xOffset = -40;
		// yOffset = 20;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	$("a.preview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		// screen.width
		// screen.height
		// left column needs ca. 280px
		var columnLeft = 0;
		
		// centerpoints for left/right top/bottom switcher
		var middleX = (screen.width - columnLeft) / 2;
		var middleY = screen.height / 2;
		
		var c = (this.t != "") ? "<br/>" + this.t : "";
		// correction of preview left/right
		if (e.pageX >= middleX) {
			yOffset = -450;
		} else {
			yOffset = 20;
		}
		// correction of preview top/bottom
		if (e.pageY >= middleY) {
			xOffset = 250;
		} else {
			xOffset = -40;
		}
		$("body").append("<p id='preview'><img src='"+ this.rel+"' alt='Image preview' />"+ c +"</p>");
		$("#preview")

			.fadeIn("fast");						
    },
	function(){
		this.title = this.t;	
		$("#preview").remove();
    });	
	$("a.preview").mousemove(function(e){
		$("#preview")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};


// starting the script on page load
$(document).ready(function(){
	imagePreview();
});