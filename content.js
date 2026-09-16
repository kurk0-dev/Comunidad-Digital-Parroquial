/* =============================================================================
   content.js — EL ÚNICO ARCHIVO QUE NECESITAS EDITAR
   Parroquia San Francisco Javier — Comunidad Digital y Solidaria
   =============================================================================

   Si vas a cambiar un horario, un texto, una foto, una campaña de donación o una
   respuesta del bot, se hace AQUÍ. No hace falta tocar index.html ni styles.css.

   TRES REGLAS PARA NO ROMPER LA PÁGINA
   ------------------------------------
   1. Todo texto va entre comillas dobles:   nombre: "Padre Juan"
   2. Cada línea termina en coma, menos la última de cada bloque.
   3. Si un texto lleva comillas dobles por dentro, usa comillas simples por fuera.

   LA ETIQUETA "Ejemplo / Por confirmar"
   -------------------------------------
   Los datos de este archivo son de EJEMPLO. Cualquier bloque que lleve
   esPlaceholder: true  muestra en la web una etiqueta amarilla que avisa que ese
   dato todavía no está confirmado por la parroquia.

   Cuando confirmes un dato con el párroco: corrige el texto y BORRA la línea
   esPlaceholder: true  de ese bloque. La etiqueta desaparece sola.

   ¿Rompiste algo? Abre la página, presiona F12 y mira la pestaña "Console":
   ahí aparece en rojo la línea con el error.
   ============================================================================= */

const CONTENT = {

  /* --- Datos generales de la parroquia ------------------------------------ */
  parroquia: {
    nombre: "Parroquia San Francisco Javier",
    distrito: "Ate, Lima",
    // Del sello oficial de la parroquia.
    diocesis: "Diócesis de Chosica · Vicaría II",
    // Escudo oficial. Si cambias el archivo, escribe aquí su nombre exacto.
    logo: "assets/img/logo.png",
    // Frase corta que aparece grande en la portada.
    mision: "Una comunidad que se acompaña, se escucha y se sostiene.",
    // Texto de 2 o 3 líneas debajo de la frase.
    bajada: "Somos una parroquia de puertas abiertas en Ate Vitarte. Aquí encuentras nuestros horarios de misa, los servicios parroquiales y las formas concretas de ayudar o de pedir ayuda.",
    esPlaceholder: true
  },

  /* --- Redes sociales. Deja "" (vacío) en la que no tengan. ---------------
     El Facebook es el oficial de la parroquia. Si abren Instagram, pega aquí
     el enlace y aparecerá solo. */
  redes: {
    instagram: "",
    facebook: "https://www.facebook.com/parroquiasfjlima/"
  },

  /* --- Sobre nosotros -----------------------------------------------------
     El mapa ya apunta a la ubicación real de la parroquia.
     La historia y la dirección exacta siguen pendientes de confirmar. */
  sobreNosotros: {
    historia: "La parroquia acompaña a las familias de Ate desde hace décadas. Aquí va la historia real de la parroquia: cuándo se fundó, quiénes la levantaron y qué comunidades la sostienen hoy.",

    /* Ubicación. Se escribe UNA sola vez aquí: de estas coordenadas salen
       tanto el mapa que se ve en la página como el botón "Abrir en Google Maps".

       Para cambiarlas: entra a Google Maps, haz clic derecho justo sobre la
       parroquia y pulsa las dos cifras que aparecen arriba del menú (eso las
       copia). Pégalas aquí tal cual, separadas por coma y sin espacios.

       No uses enlaces cortos (maps.app.goo.gl): caducan y dejan el botón roto. */
    coordenadas: "-12.0365691,-76.928581",
    etiquetaMapa: "Parroquia San Francisco Javier",

    direccion: "Ate, Lima",
    esPlaceholder: true
  },

  /* --- Directiva / equipo pastoral ----------------------------------------
     Las fotos van en la carpeta assets/img/ y se nombran aquí tal cual.
     Si aún no tienes la foto, deja foto: "" y se muestra un círculo con la inicial. */
  directiva: [
    { nombre: "P. Nombre Apellido", cargo: "Párroco", foto: "", esPlaceholder: true },
    { nombre: "Nombre Apellido", cargo: "Coordinadora de Pastoral", foto: "", esPlaceholder: true },
    { nombre: "Nombre Apellido", cargo: "Catequesis", foto: "", esPlaceholder: true }
  ],

  /* --- Horarios de misa ---------------------------------------------------
     "horas" admite todas las que necesites: ["7:00 am", "10:00 am", "6:00 pm"]
     "nota" es opcional (por ejemplo: "Solo durante Cuaresma"). */
  horariosMisa: [
    { dia: "Lunes a viernes", horas: ["7:00 am", "7:00 pm"], nota: "", esPlaceholder: true },
    { dia: "Sábado", horas: ["7:00 pm"], nota: "Misa de vigilia", esPlaceholder: true },
    { dia: "Domingo", horas: ["7:00 am", "10:00 am", "12:00 m.", "6:00 pm"], nota: "", esPlaceholder: true }
  ],

  /* --- Horarios especiales (Semana Santa, Cuaresma, difuntos) ------------- */
  horariosEspeciales: [
    { titulo: "Cuaresma", detalle: "Vía Crucis todos los viernes, 6:00 pm.", esPlaceholder: true },
    { titulo: "Semana Santa", detalle: "Programación completa publicada dos semanas antes.", esPlaceholder: true },
    { titulo: "Misa de difuntos", detalle: "Primer lunes de cada mes, 7:00 pm.", esPlaceholder: true }
  ],

  /* --- Servicios parroquiales --------------------------------------------- */
  servicios: [
    {
      nombre: "Bautizo",
      requisitos: ["Partida de nacimiento del niño", "Copia del DNI de los padres", "Padrinos confirmados", "Charla prebautismal"],
      horario: "Ceremonias: sábados 4:00 pm. Inscripciones: lunes a viernes por la mañana.",
      esPlaceholder: true
    },
    {
      nombre: "Catequesis de iniciación cristiana",
      requisitos: ["Inscripción en la oficina parroquial", "Partida de nacimiento"],
      horario: "Informes e inscripciones de martes a viernes, de 7:00 pm a 9:00 pm. Ver la sección de catequesis para los grupos por edad."
    },
    {
      nombre: "Confesiones",
      requisitos: [],
      horario: "Media hora antes de cada misa, y sábados de 5:00 pm a 6:30 pm.",
      esPlaceholder: true
    },
    {
      nombre: "Consejería y acompañamiento",
      requisitos: ["Cita previa"],
      horario: "Jueves de 4:00 pm a 7:00 pm.",
      esPlaceholder: true
    }
  ],

  /* --- Catequesis de iniciación cristiana ---------------------------------
     Estos datos vienen del afiche oficial de la parroquia, así que NO llevan
     la bandera esPlaceholder. Si cambian las edades, los horarios o se cierran
     las inscripciones, se edita aquí.

     Para ocultar el aviso de inscripciones abiertas, pon inscripcionesAbiertas
     en false: el bloque desaparece de la página. */
  catequesis: {
    inscripcionesAbiertas: true,
    titulo: "Catequesis de iniciación cristiana",
    anuncio: "Iniciamos las inscripciones",
    informes: {
      dias: "Martes a viernes",
      horario: "De 7:00 p.m. a 9:00 p.m.",
      lugar: "Parroquia San Francisco Javier"
    },
    grupos: [
      {
        publico: "Para niños",
        edades: "desde los 8 hasta los 13 años",
        sacramentos: ["Bautismo", "Eucaristía"]
      },
      {
        publico: "Para jóvenes",
        edades: "desde los 15 hasta los 19 años",
        sacramentos: ["Bautismo", "Eucaristía", "Confirmación"]
      }
    ]
  },

  /* --- Campañas de donación -----------------------------------------------
     La barra de avance se calcula sola con "actual" y "meta".
     IMPORTANTE: meta y actual van SIN comillas (son números).
     La parroquia NO recibe dinero por la web: esto solo informa qué se necesita. */
  campanasDonacion: [
    { item: "Arroz", meta: 50, actual: 32, unidad: "kg", nota: "Para las canastas de fin de mes", esPlaceholder: true },
    { item: "Aceite", meta: 30, actual: 11, unidad: "litros", nota: "", esPlaceholder: true },
    { item: "Útiles escolares", meta: 100, actual: 64, unidad: "paquetes", nota: "Campaña de marzo", esPlaceholder: true },
    { item: "Frazadas", meta: 40, actual: 40, unidad: "unidades", nota: "Meta cumplida, gracias.", esPlaceholder: true }
  ],

  /* --- Dónde se entregan las donaciones ----------------------------------- */
  donacionesInfo: {
    puntoEntrega: "Oficina parroquial, de lunes a sábado de 9:00 am a 1:00 pm.",
    contactoNombre: "Coordinación de Cáritas parroquial",
    esPlaceholder: true
  },

  /* --- Eventos y actividades ----------------------------------------------
     "fecha" se escribe como texto libre: "15 de septiembre, 6:00 pm".
     "pasado: true" lo manda a la galería de eventos anteriores. */
  eventos: [
    { titulo: "Almuerzo solidario", fecha: "Primer domingo de cada mes, 1:00 pm", descripcion: "Almuerzo comunitario abierto a todas las familias del barrio.", foto: "", pasado: false, esPlaceholder: true },
    { titulo: "Jornada de salud", fecha: "20 de octubre, 9:00 am", descripcion: "Chequeo médico gratuito con voluntarios de la comunidad.", foto: "", pasado: false, esPlaceholder: true },
    { titulo: "Fiesta patronal 2025", fecha: "3 de diciembre de 2025", descripcion: "Procesión, misa central y compartir en el atrio.", foto: "", pasado: true, esPlaceholder: true }
  ],

  /* --- Testimonios --------------------------------------------------------
     PUBLICAR SOLO CON CONSENTIMIENTO DE LA PERSONA.
     Si prefiere no dar su nombre, escribe autor: "Miembro de la comunidad". */
  testimonios: [
    { texto: "Cuando mi esposo se quedó sin trabajo, la parroquia nos acompañó. No fue solo la ayuda: fue saber que no estábamos solos.", autor: "Miembro de la comunidad", esPlaceholder: true },
    { texto: "Empecé como voluntaria por tres meses y llevo cuatro años. Aquí uno recibe más de lo que da.", autor: "Voluntaria de Cáritas parroquial", esPlaceholder: true }
  ],

  /* --- Preguntas frecuentes del bot ---------------------------------------
     Cómo funciona: el bot compara lo que escribe la persona con las palabras de
     "claves". Si encuentra una, responde. Por eso conviene poner varias formas
     de decir lo mismo, y en minúsculas y sin tildes.

     "sugerida" es el texto del botón de acceso rápido dentro del chat.
     Si no quieres que aparezca como botón, deja sugerida: "". */
  faqBot: [
    {
      claves: ["horario", "horarios", "misa", "misas", "a que hora", "cuando hay misa"],
      sugerida: "¿A qué hora son las misas?",
      respuesta: "Los domingos hay misa a las 7:00 am, 10:00 am, 12:00 m. y 6:00 pm. De lunes a viernes, 7:00 am y 7:00 pm. Puedes ver el detalle completo en la sección Misas y horarios."
    },
    {
      claves: ["bautizo", "bautismo", "bautizar"],
      sugerida: "¿Qué necesito para un bautizo?",
      respuesta: "Necesitas la partida de nacimiento del niño, copia del DNI de los padres, los padrinos confirmados y haber asistido a la charla prebautismal. Las inscripciones son de lunes a viernes por la mañana en la oficina parroquial."
    },
    {
      claves: ["donar", "donacion", "donaciones", "ayudar", "colaborar", "que necesitan"],
      sugerida: "¿Cómo puedo donar?",
      respuesta: "Puedes ver en la sección Donaciones qué se necesita ahora mismo y cuánto falta para cada meta. Las entregas se reciben en la oficina parroquial de lunes a sábado, de 9:00 am a 1:00 pm. No recibimos dinero por la página web."
    },
    {
      claves: ["ayuda", "necesito ayuda", "apoyo", "pedir ayuda"],
      sugerida: "Necesito pedir ayuda",
      respuesta: "Puedes llenar el formulario de solicitud de ayuda en la sección Contacto. Tiene opción de enviarlo de forma anónima. Alguien de la parroquia se comunicará contigo."
    },
    {
      claves: ["catequesis", "primera comunion", "comunion", "confirmacion", "inscripcion", "inscripciones"],
      sugerida: "¿Cuándo es la catequesis?",
      respuesta: "Las inscripciones para la catequesis de iniciación cristiana están abiertas. Informes de martes a viernes, de 7:00 p.m. a 9:00 p.m., en la parroquia. Hay dos grupos: niños de 8 a 13 años (Bautismo y Eucaristía) y jóvenes de 15 a 19 años (Bautismo, Eucaristía y Confirmación)."
    },
    {
      claves: ["confesion", "confesiones", "confesar"],
      sugerida: "",
      respuesta: "Hay confesiones media hora antes de cada misa, y los sábados de 5:00 pm a 6:30 pm."
    },
    {
      claves: ["donde", "direccion", "ubicacion", "como llego"],
      sugerida: "¿Dónde quedan?",
      respuesta: "Estamos en Ate Vitarte, Lima. En la sección Sobre nosotros hay un mapa para llegar."
    },
    {
      claves: ["matrimonio", "casarse", "boda"],
      sugerida: "",
      respuesta: "Para matrimonios se coordina una cita con el párroco con anticipación. Escríbenos por WhatsApp y te damos las fechas disponibles."
    }
  ],

  /* --- Texto que usa el bot cuando NO entiende la pregunta ---------------- */
  botFallback: "No estoy seguro de cómo responder eso. Si quieres, escríbenos directamente por WhatsApp y te atiende una persona de la parroquia.",

  /* --- Contacto -----------------------------------------------------------
     whatsapp: código de país + número, sin +, sin espacios. Perú = 51.
     Los formularios se crean en Google Forms y se pega el enlace completo. */
  contacto: {
    whatsapp: "51900000000",
    googleFormContacto: "https://forms.gle/XXXXXXXXXXXX",
    googleFormAyuda: "https://forms.gle/YYYYYYYYYYYY",
    correo: "",
    telefono: "",
    esPlaceholder: true
  }
};
