<?php
/**
 * Title: Página de Contacto
 * Slug: cds/pagina-contacto
 * Categories: cds-paginas
 * Description: WhatsApp, formulario de contacto y solicitud de ayuda anónima (Google Forms).
 * Inserter: yes
 *
 * @package cds
 */

?>
<!-- wp:paragraph {"className":"cds-sobretitulo"} -->
<p class="cds-sobretitulo">Escríbenos</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"is-style-con-filete"} -->
<h1 class="wp-block-heading is-style-con-filete">Contacto y solicitud de ayuda</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Estamos para escucharte. Elige la vía que te resulte más cómoda.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Contacto general</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dudas sobre la reunión, los horarios de la capilla o cómo participar en el grupo.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_formulario tipo="contacto" texto="Abrir formulario de contacto"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Solicitud de ayuda</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Si tú o una familia cercana necesitan apoyo, escríbenos. <strong>Puedes enviarlo de forma anónima</strong>: el nombre no es obligatorio.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_formulario tipo="ayuda" texto="Pedir ayuda"]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-tarjeta"} -->
<div class="wp-block-column is-style-tarjeta"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">WhatsApp</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>¿Prefieres hablar con una persona? Escríbenos directamente.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[cds_whatsapp]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:shortcode -->
[cds_privacidad]
<!-- /wp:shortcode -->
