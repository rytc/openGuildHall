
$(document).ready(function() {
	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active");
	$(".tab_content:first").show();
	var currentTab = "#"+$(".tab_content:first").attr("id");

	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		
		var newTab = $(this).find("a").attr("href");

		$(currentTab).fadeOut('fast', function() {
	        $(newTab).fadeIn('fast');
	    });
    
    	currentTab = newTab;
		$(currentTab).find('div.errors').html('');

    	return false;
	});

	$("input").blur(function() {
		$(currentTab).find('div.errors').fadeOut('fast');
	});

	$("form").submit(function() {
		var data = $(this).serialize();
		$(currentTab).find('div.errors').fadeOut('fast');
		$.ajax({
			url: SITE_PATH+'user/controls',
			type: 'POST',
			data: data,
			cache: false,
			success: function(html) {
				$(currentTab).find('div.errors').show();
				if(html == 'SUCCESS') {
					$(currentTab).find('div.errors').css('color', '#0A0');
					$(currentTab).find('div.errors').html('Settings successfully changed');
				} else {
					$(currentTab).find('div.errors').css('color', '#A00');
					$(currentTab).find('div.errors').html(html);
				}
			}
			
		});

    	return false;

	});

	$("#country").change(function() {
		$("img.cntyicon").attr('src', SITE_PATH+"Application/Views/countries/" + $("select#country option:selected").val());
	});

	$("#avatarinput").blur(function() {
		$(currentTab).find('div.errors').fadeOut('fast');
		$(".avatar").attr("src", $("#avatarinput").val());
	});
});

