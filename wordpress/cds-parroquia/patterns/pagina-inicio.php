<?php
/**
 * Title: Página de Inicio
 * Slug: cds/pagina-inicio
 * Categories: cds-paginas
 * Description: Escudo grande, misión, accesos a las demás páginas, sobre nosotros con mapa, equipo pastoral y grupos.
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
<figure class="wp-block-image aligncenter size-full is-resized cds-escudo"><img src="<?php echo $cds_img( 'escudo-parroquia.webp' ); ?>" alt="Escudo de la Parroquia San Francisco Javier" style="width:290px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Diócesis de Chosica · Vicaría II · Ate, Lima</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Parroquia San Francisco Javier</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-por-confirmar","fontSize":"mediana"} -->
<p class="is-style-por-confirmar has-mediana-font-size">Una comunidad que se acompaña, se escucha y se sostiene.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Somos una parroquia de puertas abiertas en Ate Vitarte. Aquí encuentras nuestros horarios de misa, los servicios parroquiales y las formas concretas de ayudar o de pedir ayuda.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $cds_url( 'misas' ); ?>">Ver horarios de misa</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $cds_url( 'donaciones' ); ?>">Cómo ayudar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:shortcode -->
[cds_redes]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"className":"cds-seccion cds-accesos"} -->
<div class="wp-block-group cds-seccion cds-accesos"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Qué encuentras aquí</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"is-style-con-filete"} -->
<h2 class="wp-block-heading is-style-con-filete">La parroquia en línea</h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":"15rem"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'misas' ); ?>">Misas y horarios</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Horarios de cada día, fechas especiales y cómo guardar la misa dominical en tu calendario.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'servicios' ); ?>">Servicios y catequesis</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Bautizos, confesiones, consejería y las inscripciones abiertas de catequesis.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'donaciones' ); ?>">Donaciones</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Qué necesita la parroquia ahora mismo y cuánto falta para cada meta. Solo en especie.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo $cds_url( 'comunidad' ); ?>">Comunidad</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Eventos, fotos de lo que ya vivimos y testimonios de quienes forman la parroquia.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3} -->
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
<div class="wp-block-group is-style-tarjeta"><!-- wp:paragraph {"className":"is-style-por-confirmar"} -->
<p class="is-style-por-confirmar">La parroquia acompaña a las familias de Ate desde hace décadas. Aquí va la historia real de la parroquia: cuándo se fundó, quiénes la levantaron y qué comunidades la sostienen hoy.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Ate, Lima</strong></p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_como_llegar texto="Abrir en Google Maps"]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:shortcode -->
[cds_mapa]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Equipo pastoral</h3>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:paragraph -->
<p><strong>P. Nombre Apellido</strong><br>Párroco</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:paragraph -->
<p><strong>Nombre Apellido</strong><br>Coordinación de pastoral</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:paragraph -->
<p><strong>Nombre Apellido</strong><br>Secretaría parroquial</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Grupos y comunidades</h3>
<!-- /wp:heading -->

<!-- wp:group {"className":"is-style-tarjeta","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:image {"width":"84px","sizeSlug":"full","linkDestination":"none","lightbox":{"enabled":false}} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo $cds_img( 'escudo-rcc.webp' ); ?>" alt="Escudo del Grupo de Oración Divino Niño Jesús" style="width:84px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p><strong>Grupo de Oración Divino Niño Jesús</strong> — Renovación Carismática Católica, Diócesis de Chosica, Zona 3. Se reúne en la Capilla Divino Niño. <a href="<?php echo $cds_url( 'comunidad' ); ?>">Mira su último evento</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
