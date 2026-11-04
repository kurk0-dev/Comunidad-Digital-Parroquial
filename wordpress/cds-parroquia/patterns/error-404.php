<?php
/**
 * Title: Página no encontrada
 * Slug: cds/error-404
 * Inserter: no
 *
 * Es un patrón (no HTML fijo) para que "Volver al inicio" funcione también
 * cuando WordPress vive en una subcarpeta.
 *
 * @package cds
 */

?>
<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">No encontramos esta página</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Puede que el enlace haya cambiado. Vuelve al inicio o usa el menú de arriba.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Volver al inicio</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
