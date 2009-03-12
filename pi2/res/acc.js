
$(document).ready(function(){

	$(".vacationdescription").hide();
	$("h3.vacationtitle").toggle(function(){
		
		if ($(this).hasClass("h3active")) {  
			$(this).removeClass("h3active").next().removeClass("enfolder").hide("fast");
		} else {                  		
			// jQuery("h3.vacationtitle").removeClass("h3active").next().removeClass("enfolder").hide("fast");
			$(this).addClass("h3active").next().addClass("enfolder").show("fast");
		}
		
	},function(){
		
		if ($(this).hasClass("h3active")) {  
			$(this).removeClass("h3active").next().removeClass("enfolder").hide("fast");	
		} else { 
			// jQuery("h3.vacationtitle").removeClass("h3active").next().removeClass("enfolder").hide("fast");
			$(this).addClass("h3active").next().addClass("enfolder").show("fast");
		}
	});
	
	$("#tabs1").tabs({ fx: { opacity: 'toggle' } });

      $('a[rel*=facebox]').facebox({
        loading_image : 'typo3conf/ext/rt_vacationcare/pi2/res/facebox/loading.gif',
        close_image   : 'typo3conf/ext/rt_vacationcare/pi2/res/facebox/closelabel.gif'
      }) 

	
});