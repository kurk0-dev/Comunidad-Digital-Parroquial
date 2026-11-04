<?php
/**
 * Title: Página de Donaciones
 * Slug: cds/pagina-donaciones
 * Categories: cds-paginas
 * Description: Campaña de acopio de víveres y ropa con contador de avance, punto de entrega, registro de donación y QR.
 * Inserter: yes
 *
 * @package cds
 */

?>
<!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Solidaridad</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">Campaña de acopio</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Reunimos víveres y ropa para familias de bajos recursos de la comunidad. Aquí ves qué se necesita y cuánto falta para cada meta. <strong>Solo recibimos donaciones en especie: esta página nunca recibe ni pide dinero.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_donaciones]
<!-- /wp:shortcode -->

<!-- wp:heading {"className":"cds-seccion"} -->
<h2 class="wp-block-heading cds-seccion">Cómo donar</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"level":3,"className":"is-style-por-confirmar"} -->
<h3 class="wp-block-heading is-style-por-confirmar">Punto y jornada de acopio</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Oficina parroquial, después de las misas dominicales. La jornada principal se realizará a más tardar el 15 de noviembre.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_como_llegar]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Registra tu donación</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cuéntanos qué vas a traer para coordinar la entrega. Te pedimos nombre y teléfono solo para eso.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_formulario tipo="donacion" texto="Registrar mi donación"]
<!-- /wp:shortcode -->

<!-- wp:shortcode -->
[cds_privacidad]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","className":"is-style-tarjeta cds-seccion"} -->
<div class="wp-block-columns are-vertically-aligned-center is-style-tarjeta cds-seccion"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Comparte la campaña</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Este código abre esta página: imprímelo en los volantes o muéstralo para que más personas se sumen.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"200px"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:200px"><!-- wp:shortcode -->
[cds_qr]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
