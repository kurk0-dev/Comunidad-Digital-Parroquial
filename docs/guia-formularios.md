# Los 3 formularios de Google del grupo

Se crean con la cuenta **diocesisdechosica.zona3@gmail.com** en <https://forms.google.com>.
La web no guarda ningún dato: cada botón abre el formulario de Google y las respuestas
llegan a una hoja de cálculo de esa cuenta.

| Formulario | Historia | ¿Anónimo? | Campo en WordPress (Ajustes → Datos del sitio) |
|---|---|---|---|
| Registro de donación | RM (HU-8, campaña) | **No**: hay que coordinar la entrega | Formulario para registrar una donación |
| Contacto y sugerencias | RM-006 (HU-11) | No | Formulario de contacto |
| Solicitud de ayuda | RM-007 (HU-12) | **Sí**: el nombre es opcional | Formulario de solicitud de ayuda |

---

## Configuración que llevan los 3

En el formulario, pestaña **Configuración**:

1. **Respuestas → Recopilar direcciones de correo electrónico: No recopilar.**
   Si se activa, Google pide iniciar sesión y muchos adultos mayores se quedan ahí.
   En el de ayuda es obligatorio que esté apagado: si no, la promesa de anonimato es falsa.
2. **Respuestas → Limitar a 1 respuesta: apagado** (también obliga a iniciar sesión).
3. **Presentación → Mensaje de confirmación**: el texto de cada formulario (abajo).

En la pestaña **Respuestas**:

4. **Vincular con Hojas de cálculo → Crear una hoja nueva.** Ahí queda todo ordenado.
5. Menú **⋮ → Recibir notificaciones por correo electrónico de nuevas respuestas.**
   Así el Gmail avisa cada vez que alguien escribe.

Para pegar el enlace en la web: botón **Enviar → ícono de cadena (🔗) → Acortar URL →
Copiar**. Pégalo en el campo de WordPress que dice la tabla de arriba y guarda.

> La hoja con las respuestas **nunca se comparte ni se publica**: tiene nombres y
> teléfonos. Solo la ven el coordinador y quien atiende.

---

## 1. Registro de donación (Campaña de Navidad)

**Título:** Quiero donar a la Campaña de Navidad

**Descripción:**
> Gracias por sumarte. Cuéntanos qué vas a traer para coordinar la entrega.
> Solo recibimos donaciones en especie (víveres y ropa). **Este formulario no recibe
> dinero y nadie del grupo te pedirá dinero.**

| # | Pregunta | Tipo | Obligatoria | Detalle |
|---|---|---|---|---|
| 1 | Nombre y apellido | Respuesta corta | Sí | |
| 2 | Celular o WhatsApp | Respuesta corta | Sí | Validación (⋮ → Validación de respuesta) → *Expresión regular* → *Coincide con* `^9\d{8}$`. Mensaje de error: "Escribe tu celular de 9 dígitos, empieza con 9". |
| 3 | ¿Qué vas a donar? | Casillas de verificación | Sí | Las **mismas** opciones de la lista de necesidades de la web: Arroz, Aceite, Menestras, Ropa de abrigo… y activa **Otro**. Si cambia la lista, cambia también aquí. |
| 4 | ¿Cuánto, más o menos? | Respuesta corta | Sí | Texto de ayuda: "Ejemplo: 5 kg de arroz y 2 litros de aceite". |
| 5 | ¿Cuándo la traerías? | Opción múltiple | Sí | "El viernes, después de la reunión en la Capilla Divino Niño", "Prefiero coordinar por WhatsApp". *Ajusta los días cuando pastoral los confirme.* |
| 6 | ¿Algo más que debamos saber? | Párrafo | No | |
| 7 | Uso de tus datos | Casillas (una sola) | Sí | "Acepto que el equipo del grupo use mi nombre y celular solo para coordinar esta donación. No se comparten con nadie (Ley N.° 29733)." |

**Mensaje de confirmación:**
> ¡Gracias! Te escribiremos por WhatsApp para coordinar la entrega. Recuerda: solo
> recibimos víveres y ropa, nunca dinero.

**Importante para el contador de la web:** este formulario registra lo que la gente
**promete** traer. El contador de avance cuenta lo que **de verdad llegó**: eso se anota
en la pestaña "Avance" cuando se recibe (guía: `guia-donaciones-sheets.md`), o a mano en
Ajustes → Datos del sitio → Lista de necesidades.

---

## 2. Contacto y sugerencias (RM-006)

El criterio del Backlog pide: nombre, contacto y mensaje obligatorios, y que llegue a un
correo u hoja que alguien revise.

**Título:** Escríbenos

**Descripción:**
> Déjanos tu consulta o sugerencia. Te respondemos por el medio que elijas.

| # | Pregunta | Tipo | Obligatoria | Detalle |
|---|---|---|---|---|
| 1 | Nombre | Respuesta corta | Sí | |
| 2 | ¿Cómo te respondemos? | Respuesta corta | Sí | Texto de ayuda: "Tu celular o tu correo". |
| 3 | ¿Sobre qué es? | Desplegable | Sí | Consulta, Quiero unirme al grupo, Pedido de oración, Sugerencia para la web, Otro. |
| 4 | Tu mensaje | Párrafo | Sí | |
| 5 | Uso de tus datos | Casillas (una sola) | Sí | "Acepto que el equipo del grupo use estos datos solo para responderme (Ley N.° 29733)." |

**Mensaje de confirmación:**
> Recibimos tu mensaje. Te responderemos lo antes posible.

---

## 3. Solicitud de ayuda, con opción anónima (RM-007, Sprint 2)

Antes de publicarlo, pastoral debe definir **qué tipos de ayuda se atienden** y **en
cuántos días se responde** (riesgo RSK-023). Mientras tanto, las opciones de la pregunta 1
son de ejemplo.

**Título:** Pedir ayuda

**Descripción:**
> Si tú o una familia cercana necesitan apoyo, escríbenos. **No es obligatorio dar tu
> nombre.** Las respuestas solo las lee el coordinador de pastoral social.
> Respondemos en un plazo de ___ días *(lo define pastoral)*.

| # | Pregunta | Tipo | Obligatoria | Detalle |
|---|---|---|---|---|
| 1 | ¿Qué tipo de ayuda necesitas? | Casillas | Sí | Víveres, Ropa, Acompañamiento y oración, Orientación (a dónde acudir), Otro. *Por confirmar con pastoral.* |
| 2 | Cuéntanos un poco más | Párrafo | Sí | Texto de ayuda: "No hace falta que pongas nombres". |
| 3 | ¿Quieres que te contactemos? | Opción múltiple | Sí | "Sí, les dejo un número", "No, prefiero quedar en anónimo". Con **Ir a la sección según la respuesta**: "No" salta al final. |
| 4 | Tu nombre | Respuesta corta | **No** | Texto de ayuda: "Puedes dejarlo en blanco". |
| 5 | Celular o WhatsApp | Respuesta corta | Sí (solo en la sección "Sí") | Misma validación que en donaciones. |
| 6 | Uso de tus datos | Casillas (una sola) | Sí | "Entiendo que esta información solo la ve el coordinador de pastoral social y se usa solo para ayudarme (Ley N.° 29733)." |

**Mensaje de confirmación:**
> Recibimos tu pedido. Si dejaste un número, te escribiremos. Si no, puedes acercarte
> el viernes a la Capilla Divino Niño: te recibimos con gusto.

Revisa dos veces que **"Recopilar direcciones de correo electrónico" esté en "No
recopilar"**: es lo que hace que de verdad sea anónimo.
