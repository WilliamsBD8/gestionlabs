var boton = $('#login');
boton.on('click', e => {
	boton.prop('disabled', true);
	boton.removeClass('gradient-45deg-purple-deep-orange');
	boton.addClass('blue-grey darken-3');
	var form = $('.login-form');
	var url = form.attr('action');
	var data = form.serializeArray();
	e.preventDefault();
	fetch(url, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded', Authentication: 'secret'},
		body: form.serialize()
	})
	.then(response => {
	    if (!response.ok) throw Error(response.status);
	    return response.json();
	 })
	.then( result => {
		if(result.errors){
			Swal.fire({
				position: 'top-end',
			  	icon: 'error',
			  	text: result.errors,
			});
			boton.addClass('gradient-45deg-purple-deep-orange');
			boton.removeClass('blue-grey darken-3');
			boton.prop('disabled', false);
		}
		else if(result.login)
			location.href = result.login;
	}).catch( error => {
		console.log(error);
		Swal.fire({
			position: 'top-end',
		  	icon: 'warning',
		  	text: 'Problemas de conectividad',
		});
		boton.addClass('gradient-45deg-purple-deep-orange');
		boton.removeClass('blue-grey darken-3');
		boton.prop('disabled', false);
	});
});