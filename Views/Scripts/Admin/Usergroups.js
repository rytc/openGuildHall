

function edit(id) {
	$("label."+id).hide();
	$("div#"+id).fadeIn();
	
}

function cancel(id) {
	$("div#"+id).hide();
	$("label."+id).fadeIn();
}

function confirmDelete(url) {
	if(confirm('Are you sure you want to delete that group? This action is irreversable!')) {
		window.location = url;
	}
}
