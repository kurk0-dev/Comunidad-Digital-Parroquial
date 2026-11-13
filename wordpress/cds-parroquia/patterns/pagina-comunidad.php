<?php
/**
 * Title: Página de Comunidad
 * Slug: cds/pagina-comunidad
 * Categories: cds-paginas
 * Description: Próximos eventos (fiesta del Divino Niño y Santo Rosario), últimas publicaciones de Instagram, eventos pasados con sus fotos (viernes y carrusel), testimonios y redes.
 * Inserter: yes
 *
 * @package cds
 */

$cds_img = static function ( $f ) {
	return esc_url( get_theme_file_uri( 'assets/img/' . $f ) );
};

// Una foto por viernes. No se sabe qué foto corresponde a cada viernes: son las que eligió el grupo.
// Fotos con permiso; los rostros de posibles menores van difuminados.
$cds_viernes = array(
	array( 'fotos/viernes-1.webp', 'Una servidora habla desde el ambón mientras la asamblea escucha sentada en las bancas' ),
	array( 'fotos/viernes-2.webp', 'Asamblea con coronas de papel amarillo escuchando a la servidora en el ambón' ),
	array( 'fotos/viernes-3.webp', 'Vista amplia de la capilla durante el seminario, con participantes en las bancas y el altar al fondo' ),
	array( 'fotos/viernes-4.webp', 'Participantes sentados en las bancas frente al altar decorado del seminario' ),
	array( 'fotos/viernes-5.webp', 'Músicos con guitarra acompañan el canto mientras los participantes se reúnen en la capilla' ),
	array( 'seminario/oracion.webp', 'Personas arrodilladas en oración frente al altar iluminado con velas' ),
	array( 'fotos/viernes-7.webp', 'Un servidor guía la oración desde el ambón ante la asamblea reunida' ),
);

// Galería: grupos de compartir del seminario.
$cds_fotos = array(
	array( 'fotos/seminario-01.webp', 'Cinco participantes conversan sentados entre las bancas de la capilla', 'Grupos de compartir' ),
	array( 'fotos/seminario-02.webp', 'Grupo de compartir en sillas en círculo, con las hojas del tema en la mano', 'Grupos de compartir' ),
	array( 'fotos/seminario-03.webp', 'Cuatro participantes con sus credenciales conversan sobre el tema del día', 'Grupos de compartir' ),
	array( 'fotos/seminario-04.webp', 'Grupo pequeño sentado en círculo junto a la ventana de vidrio', 'Grupos de compartir' ),
	array( 'fotos/seminario-05.webp', 'Participantes leen juntos las hojas del tema sentados frente al altar', 'Grupos de compartir' ),
	array( 'fotos/seminario-06.webp', 'Tres participantes sonríen sentados en las sillas de la capilla', 'Grupos de compartir' ),
	array( 'fotos/seminario-07.webp', 'Grupo de compartir sentado sobre la alfombra roja del presbiterio', 'Grupos de compartir' ),
	array( 'fotos/seminario-08.webp', 'Participantes en círculo con sus credenciales al cuello, junto a la pared con cuadros', 'Grupos de compartir' ),
	array( 'fotos/seminario-09.webp', 'Grupo conversando animado entre las bancas, con las hojas del tema', 'Grupos de compartir' ),
	array( 'fotos/seminario-10.webp', 'Vista desde arriba de un grupo grande sentado en círculo en la capilla', 'Grupos de compartir' ),
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
<p class="cds-sobretitulo">Del viernes 6 al domingo 15 de noviembre · Capilla Divino Niño</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Fiesta del Divino Niño Jesús 2026</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Celebramos 36 años de la llegada de la imagen del Divino Niño Jesús a la urbanización Los Portales de Javier Prado. Cada día hay misa en su honor a las 6:00 p. m. y novena a las 7:00 p. m.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"cds-puntos"} -->
<ul class="wp-block-list cds-puntos"><!-- wp:list-item -->
<li><strong>Viernes 13:</strong> misa a las 6:00 p. m. y novena a las 7:00 p. m. La novena la anima nuestro Grupo de Oración.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Sábado 14:</strong> misa a las 6:30 p. m. y novena a las 7:30 p. m.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Domingo 15:</strong> misa solemne a las 10:00 a. m. y, a las 11:30 a. m., procesión del Divino Niño por las calles de la urbanización.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Todos los viernes · 5:00 p. m.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Santo Rosario al Divino Niño</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>La Cuadrilla del Divino Niño Jesús invita a todos los devotos a rezar juntos el Santo Rosario. ¡Te esperamos con mucha fe y alegría!</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"cds-seccion"} -->
<div class="wp-block-group cds-seccion"><!-- wp:shortcode -->
[cds_instagram limite="9"]
<!-- /wp:shortcode --></div>
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
<p class="cds-sobretitulo">7 viernes por la noche · Capilla Divino Niño</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Seminario de Vida en el Espíritu</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Siete viernes para conocer el amor de Dios y abrir el corazón a la acción del Espíritu Santo. Cada tema se vivió primero en asamblea y luego en pequeños grupos de compartir.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Revive cada viernes</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"pequena"} -->
<p class="has-pequena-font-size">Toca un viernes para ver su foto en grande.</p>
<!-- /wp:paragraph -->

<!-- wp:gallery {"columns":7,"linkTo":"none","sizeSlug":"medium","className":"is-style-viernes"} -->
<figure class="wp-block-gallery has-nested-images columns-7 is-cropped is-style-viernes"><?php foreach ( $cds_viernes as $cds_i => $cds_v ) : ?><!-- wp:image {"sizeSlug":"medium","linkDestination":"none"} -->
<figure class="wp-block-image size-medium"><img src="<?php echo $cds_img( $cds_v[0] ); ?>" alt="<?php echo esc_attr( $cds_v[1] ); ?>"/><figcaption class="wp-element-caption">Viernes <?php echo (int) $cds_i + 1; ?></figcaption></figure>
<!-- /wp:image -->

<?php endforeach; ?></figure>
<!-- /wp:gallery -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Galería</h4>
<!-- /wp:heading -->

<!-- wp:gallery {"linkTo":"none","sizeSlug":"large","className":"is-style-carrusel"} -->
<figure class="wp-block-gallery has-nested-images columns-default is-cropped is-style-carrusel"><?php foreach ( $cds_fotos as $cds_f ) : ?><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo $cds_img( $cds_f[0] ); ?>" alt="<?php echo esc_attr( $cds_f[1] ); ?>"/><figcaption class="wp-element-caption"><?php echo esc_html( $cds_f[2] ); ?></figcaption></figure>
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
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Cuando mi esposo se quedó sin trabajo, el grupo nos acompañó. No fue solo la ayuda: fue saber que no estábamos solos.</p>
<!-- /wp:paragraph --><cite>Miembro de la comunidad</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Llegué al seminario sin conocer a nadie y salí con una comunidad que ora por mí cada semana.</p>
<!-- /wp:paragraph --><cite>Participante del seminario</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Empecé como voluntaria por tres meses y llevo cuatro años. Aquí uno recibe más de lo que da.</p>
<!-- /wp:paragraph --><cite>Servidora del grupo</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Los miércoles llego cansada y salgo con el corazón en paz.</p>
<!-- /wp:paragraph --><cite>Miembro de la asamblea</cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

