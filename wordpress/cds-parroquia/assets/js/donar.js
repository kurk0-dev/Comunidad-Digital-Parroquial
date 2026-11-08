/**
 * Formulario de donación [cds_donar].
 *
 * - Al marcar un producto aparece su pregunta "¿Cuántos…?"; al desmarcarlo se
 *   oculta, se borra el número y deja de enviarse (campo desactivado).
 * - Mensajes de error en español y sencillos.
 * - Envía al formulario de Google (misma hoja de respuestas). Sin JavaScript el
 *   formulario se envía igual, directo a Google, con todas las cantidades a la vista.
 *
 * Solo textContent: nada de innerHTML.
 */
( function () {
	'use strict';

	var form = document.getElementById( 'donar' );
	if ( ! form || ! window.fetch || ! window.URLSearchParams || ! window.FormData ) {
		return;
	}
	var estado = form.querySelector( '.cds-donar__estado' );
	var boton = form.querySelector( '.cds-donar__enviar' );
	var otro = document.getElementById( 'cds-donar-otro' );
	var casillas = Array.prototype.slice.call( form.querySelectorAll( '[data-cantidad]' ) );

	function mostrar( texto, esError ) {
		estado.textContent = texto;
		estado.classList.toggle( 'es-error', !! esError );
	}

	function actualizar( casilla, enfocar ) {
		var caja = document.getElementById( casilla.getAttribute( 'data-cantidad' ) );
		if ( ! caja ) {
			return;
		}
		var numero = caja.querySelector( 'input' );
		var marcado = casilla.checked;
		caja.hidden = ! marcado;
		numero.disabled = ! marcado;
		numero.required = marcado;
		if ( ! marcado ) {
			numero.value = '';
			numero.setCustomValidity( '' );
		} else if ( enfocar ) {
			numero.focus();
		}
	}

	casillas.forEach( function ( casilla ) {
		actualizar( casilla, false );
		casilla.addEventListener( 'change', function () {
			actualizar( casilla, true );
			if ( casilla.checked ) {
				mostrar( '', false );
			}
		} );
	} );

	// Mensajes propios: el del navegador ("Introduce un número válido") confunde.
	form.addEventListener( 'invalid', function ( e ) {
		var campo = e.target;
		var mensaje = campo.getAttribute( 'data-mensaje' ) || '';
		if ( campo.validity.valueMissing ) {
			if ( 'checkbox' === campo.type ) {
				mensaje = 'Marque esta casilla para poder enviar.';
			} else if ( 'radio' === campo.type ) {
				mensaje = 'Elija una opción.';
			} else if ( 'number' === campo.type ) {
				mensaje = 'Escriba cuánto va a traer. Por ejemplo: 5';
			} else {
				mensaje = 'Este dato es obligatorio.';
			}
		} else if ( campo.validity.rangeUnderflow ) {
			mensaje = 'Escriba un número mayor que 0.';
		} else if ( campo.validity.rangeOverflow ) {
			mensaje = 'Ese número es muy grande. Revíselo, por favor.';
		}
		if ( mensaje ) {
			campo.setCustomValidity( mensaje );
		}
	}, true );

	function limpiar( e ) {
		var campo = e.target;
		if ( 'radio' === campo.type ) {
			Array.prototype.forEach.call( form.querySelectorAll( 'input[type="radio"]' ), function ( r ) {
				if ( r.name === campo.name ) {
					r.setCustomValidity( '' );
				}
			} );
		} else if ( campo.setCustomValidity ) {
			campo.setCustomValidity( '' );
		}
	}
	form.addEventListener( 'input', limpiar );
	form.addEventListener( 'change', limpiar );

	function gracias() {
		var caja = document.createElement( 'div' );
		caja.className = 'cds-donar__gracias';
		caja.setAttribute( 'tabindex', '-1' );

		var titulo = document.createElement( 'p' );
		titulo.className = 'cds-donar__gracias-titulo';
		titulo.textContent = '¡Gracias! Enviamos su donación al grupo.';

		var texto = document.createElement( 'p' );
		texto.textContent = 'Le escribiremos a su celular para ponernos de acuerdo en la entrega. Que Dios le bendiga.';

		var otra = document.createElement( 'button' );
		otra.type = 'button';
		otra.className = 'wp-element-button is-style-outline';
		otra.textContent = 'Registrar otra donación';
		otra.addEventListener( 'click', function () {
			form.reset();
			casillas.forEach( function ( c ) {
				actualizar( c, false );
			} );
			boton.disabled = false;
			mostrar( '', false );
			caja.parentNode.removeChild( caja );
			form.hidden = false;
			form.scrollIntoView( { block: 'start' } );
		} );

		caja.appendChild( titulo );
		caja.appendChild( texto );
		caja.appendChild( otra );
		form.hidden = true;
		form.parentNode.insertBefore( caja, form.nextSibling );
		caja.focus();
	}

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();
		mostrar( '', false );
		if ( ! form.reportValidity() ) {
			return;
		}
		var algo = casillas.some( function ( c ) {
			return c.checked;
		} ) || '' !== otro.value.trim();
		if ( ! algo ) {
			mostrar( 'Marque al menos una cosa que va a traer, o escríbala en "¿Trae otra cosa?".', true );
			casillas[ 0 ].focus();
			return;
		}

		// Los campos desactivados (cantidades de lo no marcado) no entran en FormData.
		var datos = new URLSearchParams( new FormData( form ) );
		boton.disabled = true;
		mostrar( 'Enviando…', false );

		// "no-cors": Google no deja leer su respuesta, pero sí la recibe.
		window.fetch( form.action, { method: 'POST', mode: 'no-cors', credentials: 'omit', body: datos } )
			.then( gracias )
			.catch( function () {
				boton.disabled = false;
				mostrar( 'No se pudo enviar. Revise su conexión a internet e intente de nuevo.', true );
			} );
	} );
}() );
