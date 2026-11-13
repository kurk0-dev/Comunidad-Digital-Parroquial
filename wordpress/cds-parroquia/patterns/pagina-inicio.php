<?php
/**
 * Title: Página de Inicio
 * Slug: cds/pagina-inicio
 * Categories: cds-paginas
 * Description: Escudo grande del grupo, cuándo y dónde nos reunimos, objetivos, accesos, misión y visión, redes y mapa.
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
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:36%"><!-- wp:image {"lightbox":{"enabled":false},"width":"290px","sizeSlug":"full","linkDestination":"none","align":"center","className":"cds-escudo"} -->
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

<!-- wp:paragraph {"fontSize":"mediana"} -->
<p class="has-mediana-font-size">Un lugar para encontrarte con Dios y con una comunidad que ora y alaba contigo.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Somos un grupo de personas que se reúnen en torno a Cristo, movidos y dirigidos por el Espíritu Santo para compartir la fe y la vivencia cristiana, alabando a Dios Padre, transformando sus vidas y edificándose mutuamente a imagen de Jesucristo, según su Evangelio.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"cds-datos","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group cds-datos"><!-- wp:group {"className":"cds-dato cds-dato\u002d\u002dcuando"} -->
<div class="wp-block-group cds-dato cds-dato--cuando"><!-- wp:paragraph {"className":"cds-dato__etiqueta"} -->
<p class="cds-dato__etiqueta">Nos reunimos</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cds-dato__valor"} -->
<p class="cds-dato__valor">Miércoles · 7:30 a 10:00 p. m.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-dato cds-dato\u002d\u002ddonde"} -->
<div class="wp-block-group cds-dato cds-dato--donde"><!-- wp:paragraph {"className":"cds-dato__etiqueta"} -->
<p class="cds-dato__etiqueta">Dónde</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"cds-dato__valor"} -->
<p class="cds-dato__valor">Capilla Divino Niño</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-dato cds-dato\u002d\u002dtodos"} -->
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
<h2 class="wp-block-heading is-style-con-filete">Objetivos</h2>
<!-- /wp:heading -->

<!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%"><!-- wp:image {"lightbox":{"enabled":false},"sizeSlug":"large","linkDestination":"none","className":"cds-foto-redonda"} -->
<figure class="wp-block-image size-large cds-foto-redonda"><img src="<?php echo $cds_img( 'fotos/inicio-primera-vez.webp' ); ?>" alt="La capilla durante una reunión del grupo: participantes sentados en las bancas frente al altar"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:list {"className":"cds-puntos"} -->
<ul class="wp-block-list cds-puntos"><!-- wp:list-item -->
<li><strong>Crecer en intimidad con el Señor.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Crecer en el amor de los unos con los otros.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Compartir a Jesús con otros.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Trabajar como Iglesia, participar de la acción pastoral de la Iglesia.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Dar y recibir apoyo.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Suscitar nuevos dirigentes o líderes cristianos.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Profundizar nuestra identidad católica.</strong></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Colaborar activamente con la comunidad parroquial.</strong></li>
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
<div class="wp-block-group"><!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio\u002d\u002dcomunidad"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--comunidad"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'comunidad' ); ?>">Comunidad</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Eventos del grupo, fotos del Seminario de Vida en el Espíritu y testimonios.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio\u002d\u002ddonaciones"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--donaciones"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'donaciones' ); ?>">Donaciones</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La campaña de Navidad: qué se necesita y cuánto falta para cada meta. Solo en especie.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio\u002d\u002dmisas"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--misas"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'misas' ); ?>">Misas en la capilla</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Horarios de misa en la parroquia y en la capilla, y la fiesta del Divino Niño.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio\u002d\u002dcatequesis"} -->
<div class="wp-block-group is-style-tarjeta cds-servicio cds-servicio--catequesis"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'servicios' ); ?>">Servicios y catequesis</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Bautizos, confesiones, consejería y las inscripciones de catequesis.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta cds-servicio cds-servicio\u002d\u002dformulario"} -->
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
<h3 class="wp-block-heading">Misión</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nuestro grupo de oración pertenece a la Renovación Carismática Católica que es una corriente de gracia suscitada por el Espíritu Santo reconocida por la Iglesia, cuya misión es vivir la experiencia del bautismo en el Espíritu que nos lleva a un encuentro personal con Jesús, así como vivir la cultura del Pentecostés al servicio permanente de todo el pueblo y de la sociedad, para vivir en conversión y santidad, acogiéndonos a la poderosa intercesión de María Santísima.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Visión</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Como parte de la Renovación Carismática del Perú, nuestro grupo de oración busca ser reconocido como una comunidad de discípulos misioneros, formados íntegramente y conformada por familias (niños, jóvenes, adultos, adultos mayores), que vive y promueve la cultura del Pentecostés.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"cds-foto-redonda"} -->
<figure class="wp-block-image size-large cds-foto-redonda"><img src="<?php echo $cds_img( 'fotos/servidores-grupo.webp' ); ?>" alt="Servidores del grupo sonríen juntos en una foto selfie frente al mural de la capilla"/><figcaption class="wp-element-caption">Nuestros servidores</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Conformado por un responsable, un equipo de servidores (responsables de los distintos ministerios del grupo) y los miembros de la asamblea.</p>
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

<!-- wp:group {"className":"cds-seccion","anchor":"como-llegar"} -->
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
