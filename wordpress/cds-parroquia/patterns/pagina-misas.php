<?php
/**
 * Title: Página de Misas y horarios
 * Slug: cds/pagina-misas
 * Categories: cds-paginas
 * Description: Horarios de misa en la capilla y en la parroquia, cómo llegar a cada una, QR y botones para Google Calendar.
 * Inserter: yes
 *
 * @package cds
 */

// La parroquia no tiene ajuste propio: su ubicación va aquí, como coordenadas
// (los enlaces cortos maps.app.goo.gl caducan).
$cds_parroquia = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( '-12.0365691,-76.928581' );
?>
<!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Celebraciones</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">Horarios de misa</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Puedes ir a misa en la Capilla Divino Niño, donde se reúne el grupo, o en la parroquia. Cada misa dura alrededor de una hora y media.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"fontSize":"mediana"} -->
<h2 class="wp-block-heading has-mediana-font-size">Capilla Divino Niño</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"cds-puntos"} -->
<ul class="wp-block-list cds-puntos"><!-- wp:list-item -->
<li><strong>Domingos:</strong> 10:00 a. m. (hasta las 11:30 a. m.)</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:shortcode -->
[cds_como_llegar texto="Cómo llegar a la capilla"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"fontSize":"mediana"} -->
<h2 class="wp-block-heading has-mediana-font-size">Parroquia San Francisco Javier</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"cds-puntos"} -->
<ul class="wp-block-list cds-puntos"><!-- wp:list-item -->
<li><strong>Domingos:</strong> 8:00 a. m. (hasta las 9:30 a. m.) y 7:00 p. m.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Entre semana:</strong> 7:00 p. m.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"linkTarget":"_blank","rel":"noreferrer noopener"} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $cds_parroquia ); ?>" target="_blank" rel="noreferrer noopener">Cómo llegar a la parroquia</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","className":"is-style-tarjeta cds-seccion"} -->
<div class="wp-block-columns are-vertically-aligned-center is-style-tarjeta cds-seccion"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Llévate los horarios</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Escanea el código para abrir esta página en tu celular, o guarda la misa del domingo en tu Google Calendar: se repite cada semana.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_calendario titulo="Misa en la Capilla Divino Niño" dia="domingo" hora="10:00" duracion="90" texto="Guardar la misa de la capilla"]
<!-- /wp:shortcode -->

<!-- wp:shortcode -->
[cds_calendario titulo="Misa en la Parroquia San Francisco Javier" dia="domingo" hora="8:00" duracion="90" lugar="-12.0365691,-76.928581" texto="Guardar la misa de la parroquia"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"200px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:200px"><!-- wp:shortcode -->
[cds_qr]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
