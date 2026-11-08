<?php
/**
 * Title: Página de Inicio
 * Slug: cds/pagina-inicio
 * Categories: cds-paginas
 * Description: Escudo grande del grupo, cuándo y dónde nos reunimos, ¿primera vez?, accesos, sobre nosotros, redes y mapa.
 * Inserter: yes
 *
 * @package cds
 */

$cds_img = static function ( $f ) {
	return esc_url( get_theme_file_uri( 'assets/img/' . $f ) );
};
$cds_url = static function ( $slug ) {
	return esc_url( home_url( '/' . $slug . '/' ) );
};
?>
<!-- wp:columns {"verticalAlignment":"center","className":"cds-portada"} -->
<div class="wp-block-columns are-vertically-aligned-center cds-portada"><!-- wp:column {"verticalAlignment":"center","width":"36%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:36%"><!-- wp:image {"width":"290px","sizeSlug":"full","linkDestination":"none","align":"center","className":"cds-escudo","lightbox":{"enabled":false}} -->
<figure class="wp-block-image aligncenter size-full is-resized cds-escudo"><img src="<?php echo $cds_img( 'escudo.webp' ); ?>" alt="Escudo del Grupo de Oración Divino Niño Jesús: paloma blanca con halo amarillo sobre fondo rojo, con una cruz verde" style="width:290px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Renovación Carismática Católica · Diócesis de Chosica, Zona 3</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Grupo de Oración Divino Niño Jesús</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-por-confirmar","fontSize":"mediana"} -->
<p class="is-style-por-confirmar has-mediana-font-size">Un lugar para encontrarte con Dios y con una comunidad que ora contigo.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Somos un grupo de oración de la Renovación Carismática Católica. Nos reunimos en la Capilla Divino Niño para alabar, escuchar la Palabra y orar unos por otros.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"cds-datos","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group cds-datos"><!-- wp:group {"className":"cds-dato cds-dato--cuando"} -->
<div class="wp-block-group cds-dato cds-dato--cuando"><!-- wp:paragraph {"className":"cds-dato__etiqueta"} -->
<p class="cds-dato__etiqueta">Nos reunimos</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cds-dato__valor is-style-por-confirmar"} -->
<p class="cds-dato__valor is-style-por-confirmar">Viernes · 7:30 p. m.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-dato cds-dato--donde"} -->
<div class="wp-block-group cds-dato cds-dato--donde"><!-- wp:paragraph {"className":"cds-dato__etiqueta"} -->
<p class="cds-dato__etiqueta">Dónde</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cds-dato__valor"} -->
<p class="cds-dato__valor">Capilla Divino Niño</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-dato cds-dato--todos"} -->
<div class="wp-block-group cds-dato cds-dato--todos"><!-- wp:paragraph {"className":"cds-dato__etiqueta"} -->
<p class="cds-dato__etiqueta">Para todos</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cds-dato__valor"} -->
<p class="cds-dato__valor">Sin costo ni inscripción</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#como-llegar">Cómo llegar</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $cds_url( 'comunidad' ); ?>">Ver eventos</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"className":"cds-seccion"} -->
<div class="wp-block-group cds-seccion"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Para quien llega</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">¿Es tu primera vez?</h2>
<!-- /wp:heading -->

<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"cds-foto-redonda","lightbox":{"enabled":false}} -->
<figure class="wp-block-image size-large cds-foto-redonda"><img src="<?php echo $cds_img( 'fotos/inicio-primera-vez.webp' ); ?>" alt="La capilla durante una reunión del grupo: participantes sentados en las bancas frente al altar"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:list {"className":"cds-puntos"} -->
<ul class="wp-block-list cds-puntos"><!-- wp:list-item -->
<li><strong>Sin costo.</strong> No se paga nada para participar, ni en las reuniones ni en los eventos.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Sin inscripción.</strong> No hace falta avisar antes ni reservar cupo. Solo ven.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Alabanza, Palabra y oración.</strong> Cantamos, escuchamos una enseñanza de la Biblia y oramos juntos por las intenciones de cada uno.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Ven como estás.</strong> No necesitas saber cantar ni conocer a nadie. Te recibimos en la puerta.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-seccion cds-accesos"} -->
<div class="wp-block-group cds-seccion cds-accesos"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Qué encuentras aquí</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">Conoce más</h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":"12.5rem"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio--comunidad"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--comunidad"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'comunidad' ); ?>">Comunidad</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Eventos del grupo, fotos del Seminario de Vida en el Espíritu y testimonios.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio--donaciones"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--donaciones"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'donaciones' ); ?>">Donaciones</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La campaña de Navidad: qué se necesita y cuánto falta para cada meta. Solo en especie.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio--misas"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--misas"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'misas' ); ?>">Misas en la capilla</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Horarios de cada día, fechas especiales y cómo guardar la misa en tu calendario.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio--catequesis"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--catequesis"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'servicios' ); ?>">Servicios y catequesis</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Bautizos, confesiones, consejería y las inscripciones de catequesis.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio--formulario"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--formulario"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'contacto' ); ?>">Contacto y ayuda</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Escríbenos, o pide apoyo de forma anónima si tú o una familia cercana lo necesitan.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-seccion"} -->
<div class="wp-block-group cds-seccion"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Quiénes somos</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">Sobre nosotros</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">La Renovación Carismática</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La Renovación Carismática Católica es un movimiento de la Iglesia que invita a vivir una relación personal con Jesús y a abrirse a la acción del Espíritu Santo, en comunión con la parroquia y la diócesis.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Nuestra historia</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Aquí va la historia del grupo: cuándo empezó, quiénes lo iniciaron y cómo ha crecido. Pendiente de recopilar con los servidores del grupo.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"cds-foto-redonda"} -->
<figure class="wp-block-image size-large cds-foto-redonda"><img src="<?php echo $cds_img( 'fotos/servidores-grupo.webp' ); ?>" alt="Servidores del grupo sonríen juntos en una foto selfie frente al mural de la capilla"/><figcaption class="wp-element-caption">Nuestros servidores</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"is-style-por-confirmar"} -->
<p class="is-style-por-confirmar">Los servidores acompañan cada reunión: animan la alabanza, preparan los temas y guían los grupos de compartir. Nombres y cargos por confirmar.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-seccion cds-redes-panel"} -->
<div class="wp-block-group cds-seccion cds-redes-panel"><!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">Síguenos en redes</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ahí avisamos primero los cambios de horario, los retiros y las fotos de cada reunión. Toca una red para abrirla.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_siguenos]
<!-- /wp:shortcode --></div>
<!-- /wp:group -->

<!-- wp:group {"anchor":"como-llegar","className":"cds-seccion"} -->
<div id="como-llegar" class="wp-block-group cds-seccion"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Cómo llegar</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">Te esperamos en la capilla</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"cds-llegar"} -->
<div class="wp-block-group cds-llegar"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%"><!-- wp:quote {"className":"cds-cita"} -->
<blockquote class="wp-block-quote cds-cita"><!-- wp:paragraph -->
<p>Yo soy el camino, la verdad y la vida.</p>
<!-- /wp:paragraph --><cite>Juan 14, 6</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph {"className":"cds-lugar"} -->
<p class="cds-lugar"><strong>Capilla Divino Niño</strong><br>Irlanda, Lima 15494 · Plus Code X35F+QPP</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_como_llegar texto="Abrir en Google Maps"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:shortcode -->
[cds_mapa boton="no"]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
