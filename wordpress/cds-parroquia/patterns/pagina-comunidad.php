<?php
/**
 * Title: Página de Comunidad
 * Slug: cds/pagina-comunidad
 * Categories: cds-paginas
 * Description: Próximos eventos, eventos pasados con sus fotos (viernes y carrusel) y testimonios.
 * Inserter: yes
 *
 * @package cds
 */

$cds_img = static function ( $f ) {
	return esc_url( get_theme_file_uri( 'assets/img/' . $f ) );
};

// Una foto por viernes. Son de EJEMPLO: no se sabe qué foto corresponde a cada viernes.
$cds_viernes = array(
	array( 'asamblea-mural.webp', 'Asamblea del seminario en el templo, con el mural del Espíritu Santo detrás del altar' ),
	array( 'compartir-1.webp', 'Grupo pequeño conversando con las hojas del tema en la mano' ),
	array( 'alabanza.webp', 'Participantes de rodillas en oración mientras un servidor toca la guitarra' ),
	array( 'compartir-3.webp', 'Participantes comparten en círculo bajo el cartel del tema El pecado y sus consecuencias' ),
	array( 'asamblea-coronas.webp', 'Asamblea de participantes con coronas de papel amarillo' ),
	array( 'oracion.webp', 'Personas arrodilladas en oración frente al altar iluminado con velas' ),
	array( 'servidoras.webp', 'Cuatro servidoras sonríen frente al altar con las bolsas del seminario' ),
);

// Galería completa (fotos con permiso; sin menores de edad).
$cds_fotos = array(
	array( 'asamblea-mural.webp', 'Asamblea del seminario en el templo, con el mural del Espíritu Santo detrás del altar', 'Asamblea' ),
	array( 'compartir-2.webp', 'Cuatro participantes conversan sentados en círculo durante el compartir', 'Grupos de compartir' ),
	array( 'alabanza.webp', 'Participantes de rodillas en oración mientras un servidor toca la guitarra', 'Alabanza y oración' ),
	array( 'compartir-1.webp', 'Grupo pequeño conversando con las hojas del tema en la mano', 'Grupos de compartir' ),
	array( 'asamblea-coronas.webp', 'Asamblea de participantes con coronas de papel amarillo', 'Asamblea' ),
	array( 'oracion.webp', 'Personas arrodilladas en oración frente al altar iluminado con velas', 'Oración' ),
	array( 'compartir-3.webp', 'Participantes comparten en círculo bajo el cartel del tema del día', 'Grupos de compartir' ),
	array( 'servidoras.webp', 'Cuatro servidoras sonríen frente al altar con las bolsas del seminario', 'Servidores' ),
	array( 'servidores-2.webp', 'Tres servidores posan frente al altar con las bolsas del seminario', 'Servidores' ),
	array( 'asamblea.webp', 'Vista amplia del templo lleno durante el seminario', 'Asamblea' ),
);
?>
<!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Agenda</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">Comunidad</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Todos nuestros eventos son sin costo, sin cupos y sin inscripción previa.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Próximos eventos</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Fecha por confirmar</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Próximo evento de la parroquia</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Aquí aparecerán los retiros, vigilias, almuerzos solidarios y encuentros de la comunidad. Para agregar uno, duplica esta tarjeta y cambia el texto.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading {"className":"cds-seccion"} -->
<h2 class="wp-block-heading cds-seccion">Lo que ya vivimos</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"150px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:150px"><!-- wp:image {"width":"130px","sizeSlug":"full","linkDestination":"none","align":"center","lightbox":{"enabled":false}} -->
<figure class="wp-block-image aligncenter size-full is-resized"><img src="<?php echo $cds_img( 'paloma-llamas.webp' ); ?>" alt="" style="width:130px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">7 viernes por la noche · Grupo de Oración Divino Niño Jesús (RCC)</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Seminario de Vida en el Espíritu</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Siete viernes para conocer el amor de Dios y abrir el corazón a la acción del Espíritu Santo. Cada tema se vivió primero en asamblea y luego en pequeños grupos de compartir.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:heading {"level":4,"className":"is-style-por-confirmar"} -->
<h4 class="wp-block-heading is-style-por-confirmar">Revive cada viernes</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"pequena"} -->
<p class="has-pequena-font-size">Toca un viernes para ver su foto en grande.</p>
<!-- /wp:paragraph -->

<!-- wp:gallery {"columns":7,"linkTo":"none","sizeSlug":"medium","className":"is-style-viernes"} -->
<figure class="wp-block-gallery has-nested-images columns-7 is-cropped is-style-viernes"><?php foreach ( $cds_viernes as $cds_i => $cds_v ) : ?><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="<?php echo $cds_img( 'seminario/' . $cds_v[0] ); ?>" alt="<?php echo esc_attr( $cds_v[1] ); ?>"/><figcaption class="wp-element-caption">Viernes <?php echo (int) $cds_i + 1; ?></figcaption></figure>
<!-- /wp:image -->

<?php endforeach; ?></figure>
<!-- /wp:gallery -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Galería</h4>
<!-- /wp:heading -->

<!-- wp:gallery {"linkTo":"none","sizeSlug":"large","className":"is-style-carrusel"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-cropped is-style-carrusel"><?php foreach ( $cds_fotos as $cds_f ) : ?><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo $cds_img( 'seminario/' . $cds_f[0] ); ?>" alt="<?php echo esc_attr( $cds_f[1] ); ?>"/><figcaption class="wp-element-caption"><?php echo esc_html( $cds_f[2] ); ?></figcaption></figure>
<!-- /wp:image -->

<?php endforeach; ?></figure>
<!-- /wp:gallery --></div>
<!-- /wp:group -->

<!-- wp:heading {"className":"cds-seccion"} -->
<h2 class="wp-block-heading cds-seccion">Lo que cuenta la gente</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Testimonios publicados con el consentimiento de cada persona.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote {"className":"is-style-por-confirmar"} -->
<blockquote class="wp-block-quote is-style-por-confirmar"><!-- wp:paragraph -->
<p>Cuando mi esposo se quedó sin trabajo, la parroquia nos acompañó. No fue solo la ayuda: fue saber que no estábamos solos.</p>
<!-- /wp:paragraph --><cite>Miembro de la comunidad</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote {"className":"is-style-por-confirmar"} -->
<blockquote class="wp-block-quote is-style-por-confirmar"><!-- wp:paragraph -->
<p>Llegué al seminario sin conocer a nadie y salí con una comunidad que ora por mí cada semana.</p>
<!-- /wp:paragraph --><cite>Participante del seminario</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote {"className":"is-style-por-confirmar"} -->
<blockquote class="wp-block-quote is-style-por-confirmar"><!-- wp:paragraph -->
<p>Empecé como voluntaria por tres meses y llevo cuatro años. Aquí uno recibe más de lo que da.</p>
<!-- /wp:paragraph --><cite>Voluntaria de la parroquia</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
