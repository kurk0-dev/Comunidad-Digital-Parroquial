# Contador de donaciones con Google Forms + Google Sheets

Objetivo: que los coordinadores actualicen el avance de la campaña **desde una
hoja de cálculo**, y que la barra de la web cambie sola, sin tocar código.

```
Google Form "Registrar donación"
        │  (cada respuesta cae en la hoja)
        ▼
Pestaña "Respuestas"  ── nombres y teléfonos: PRIVADA, nunca se publica
        │  (fórmulas que suman lo recibido)
        ▼
Pestaña "Avance"      ── solo item | meta | unidad | actual
        │  (Archivo → Publicar en la web → CSV, SOLO esta pestaña)
        ▼
La web lee el CSV y dibuja las barras
```

> 🔒 **Lo más importante de esta guía:** se publica **únicamente la pestaña
> "Avance"**. La pestaña de respuestas tiene nombres y teléfonos de los
> donantes (este formulario no es anónimo) y **nunca** debe publicarse.

Usa el **Gmail del grupo** para todo, no una cuenta personal: así el
formulario y la hoja no se pierden cuando cambie el equipo.

---

## Paso 1 — Crear el formulario "Registrar donación"

En <https://forms.google.com>, crea un formulario con estas preguntas, **en
este orden** (las fórmulas de abajo dependen de él):

| # | Pregunta | Tipo | Obligatoria |
|---|---|---|---|
| 1 | Nombre | Respuesta corta | Sí |
| 2 | Teléfono o WhatsApp | Respuesta corta | Sí |
| 3 | ¿Qué vas a donar? | **Desplegable**: Arroz, Leche, Panetón, Juguetes | Sí |
| 4 | Cantidad | Respuesta corta, con validación **Número** | Sí |

Los nombres del desplegable deben escribirse **exactamente igual** que en la
pestaña "Avance" (paso 3), con las mismas tildes y mayúsculas.

En la descripción del formulario, pega el aviso de privacidad:

> Tus datos solo los ve el equipo del grupo, se usan únicamente para
> coordinar la entrega y no se comparten con nadie.

En **Configuración → Respuestas**, deja **desactivado** "Recopilar
direcciones de correo electrónico". No hace falta: ya pedimos el teléfono.

## Paso 2 — Vincular el formulario con una hoja

En el formulario: pestaña **Respuestas → Vincular con Hojas de cálculo →
Crear una hoja de cálculo nueva**.

Se crea una pestaña llamada **"Respuestas de formulario 1"**, con estas columnas:

| A | B | C | D | E |
|---|---|---|---|---|
| Marca temporal | Nombre | Teléfono o WhatsApp | ¿Qué vas a donar? | Cantidad |

### Agregar la columna "Recibido"

Una persona puede **registrar** una donación y no traerla. Para que la barra
cuente solo lo que **de verdad llegó**:

1. En la celda **F1** escribe `Recibido`.
2. Selecciona la columna **F** desde la fila 2 → **Insertar → Casilla de verificación**.
3. Cuando la donación llegue a la capilla, el coordinador marca la casilla.

## Paso 3 — Crear la pestaña "Avance"

Crea una pestaña nueva (el **+** de abajo a la izquierda) y llámala **Avance**.
La primera fila **debe** ser exactamente:

| | A | B | C | D |
|---|---|---|---|---|
| **1** | item | meta | unidad | actual |
| **2** | Arroz | 50 | kg | *(fórmula)* |
| **3** | Leche | 60 | tarros | *(fórmula)* |
| **4** | Panetón | 40 | unidades | *(fórmula)* |
| **5** | Juguetes | 50 | unidades | *(fórmula)* |

En **D2** pega esta fórmula y arrástrala hacia abajo hasta D5:

```
=SUMIFS('Respuestas de formulario 1'!E:E; 'Respuestas de formulario 1'!D:D; A2; 'Respuestas de formulario 1'!F:F; TRUE)
```

Suma la cantidad (columna E) de todas las respuestas cuyo producto (D) coincide
con el de esa fila **y** que tienen la casilla "Recibido" (F) marcada.

> Si la hoja marca error en la fórmula:
> - cambia los `;` por `,` (depende del idioma de la hoja), o
> - cambia `TRUE` por `VERDADERO`.

Para **sumar o quitar productos**: agrega o borra filas en "Avance" y
actualiza el desplegable del formulario con el mismo nombre.

## Paso 4 — Publicar SOLO la pestaña "Avance" como CSV

1. **Archivo → Compartir → Publicar en la web**.
2. En el primer desplegable elige **Avance**. ⚠️ **No** "Documento completo".
3. En el segundo desplegable elige **Valores separados por comas (.csv)**.
4. Despliega **Contenido publicado y configuración** y deja marcado
   **"Volver a publicar automáticamente cuando se realicen cambios"**.
5. Pulsa **Publicar** y copia el enlace. Se ve así:
   `https://docs.google.com/spreadsheets/d/e/2PACX-.../pub?gid=123&single=true&output=csv`

### Comprobación de privacidad (no te la saltes)

Abre ese enlace en una ventana de incógnito. Debes ver **solo** esto:

```
item,meta,unidad,actual
Arroz,50,kg,0
...
```

Si ves algún nombre o teléfono, publicaste la pestaña equivocada: vuelve a
**Publicar en la web → Detener la publicación** y repite el paso 4.

## Paso 5 — Pegar los dos enlaces en content.js

En `content.js`, dentro de `donaciones`:

```js
csvAvance: "https://docs.google.com/spreadsheets/d/e/2PACX-.../pub?gid=123&single=true&output=csv",
formularioDonacion: "https://forms.gle/...",
```

Haz commit y push. Al publicarse, la página **Solidaridad** mostrará el
avance de la hoja y el texto *"Avance actualizado desde la hoja del grupo."*

---

## Cosas a saber

- Google tarda **unos 5 minutos** en actualizar el CSV publicado después de un
  cambio en la hoja.
- Si Google no responde, la web muestra los números de `articulos` en
  `content.js`: nunca queda en blanco.
- Por seguridad, la web **solo acepta** CSV de `docs.google.com/spreadsheets`.
  Cualquier otro enlace se ignora.
- La web nunca recibe ni mueve dinero: solo muestra cuánto se ha recibido en
  donaciones físicas.
