<?php
/**
 * Title: Página de Misas y horarios
 * Slug: cds/pagina-misas
 * Categories: cds-paginas
 * Description: Horarios de misa por día, fechas especiales, QR y botón para Google Calendar.
 * Inserter: yes
 *
 * @package cds
 */

?>
<!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Celebraciones</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">Misas y horarios</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Estos son los horarios habituales de la parroquia. En fechas especiales pueden cambiar: revisa el bloque de más abajo.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":"15rem"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Domingo</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>7:00 a. m. · 10:00 a. m. · 12:00 m. · 6:00 p. m.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Sábado</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>7:00 p. m. — Misa de vigilia</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-tarjeta"} -->
<div class="wp-block-group is-style-tarjeta"><!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Lunes a viernes</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>7:00 a. m. · 7:00 p. m.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:heading {"className":"cds-seccion"} -->
<h2 class="wp-block-heading cds-seccion">Fechas especiales</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-por-confirmar"} -->
<ul class="wp-block-list is-style-por-confirmar"><!-- wp:list-item -->
<li><strong>Cuaresma:</strong> Vía Crucis todos los viernes, 6:00 p. m.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Semana Santa:</strong> programación completa publicada dos semanas antes.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Misa de difuntos:</strong> primer lunes de cada mes, 7:00 p. m.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:columns {"verticalAlignment":"center","className":"is-style-tarjeta cds-seccion"} -->
<div class="wp-block-columns are-vertically-aligned-center is-style-tarjeta cds-seccion"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Llévate los horarios</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Escanea el código para abrir esta página en tu celular, o guarda la misa dominical directamente en tu calendario.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_calendario titulo="Misa dominical" dia="domingo" hora="10:00"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"200px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:200px"><!-- wp:shortcode -->
[cds_qr]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
