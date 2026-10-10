/* =============================================================================
   content.js — EL ÚNICO ARCHIVO QUE NECESITAS EDITAR
   Grupo de Oración Divino Niño Jesús — RCC — Diócesis de Chosica, Zona 3
   =============================================================================

   Si vas a cambiar un horario, un texto, una foto, una campaña de donación o una
   respuesta del bot, se hace AQUÍ. No hace falta tocar los .html ni styles.css.

   TRES REGLAS PARA NO ROMPER LA PÁGINA
   ------------------------------------
   1. Todo texto va entre comillas dobles:   nombre: "Hna. María"
   2. Cada línea termina en coma, menos la última de cada bloque.
   3. Si un texto lleva comillas dobles por dentro, usa comillas simples por fuera.

   LA ETIQUETA "Ejemplo / Por confirmar"
   -------------------------------------
   Cualquier bloque que lleve  esPlaceholder: true  muestra en la web una
   etiqueta amarilla que avisa que ese dato todavía no está confirmado.

   Cuando confirmes un dato con el coordinador o con el padre: corrige el texto
   y BORRA la línea  esPlaceholder: true  de ese bloque. La etiqueta desaparece.

   ¿Rompiste algo? Abre la página, presiona F12 y mira la pestaña "Console":
   ahí aparece en rojo la línea con el error.
   ============================================================================= */

const CONTENT = {

  /* --- Datos del grupo (salen del escudo oficial) -------------------------- */
  grupo: {
    nombre: "Grupo de Oración Divino Niño Jesús",
    nombreCorto: "G.O. Divino Niño Jesús",
    movimiento: "Renovación Carismática Católica",
    diocesis: "Diócesis de Chosica · Zona 3",
    // Escudo oficial. Si cambias el archivo, escribe aquí su nombre exacto.
    logo: "assets/img/escudo.webp"
  },

  /* --- Frase de la portada ------------------------------------------------
     Redacción provisional: necesita la aprobación del padre o del coordinador. */
  portada: {
    mision: "Un lugar para encontrarte con Dios y con una comunidad que ora contigo.",
    bajada: "Somos un grupo de oración de la Renovación Carismática Católica. Nos reunimos en la Capilla Divino Niño para alabar, escuchar la Palabra y orar unos por otros.",
    esPlaceholder: true
  },

  /* --- La reunión semanal del grupo ---------------------------------------
     Es lo primero que se ve en la portada, porque es lo que más busca alguien
     que quiere venir. Confirma el día y la hora con el coordinador. */
  reunion: {
    dia: "Viernes",
    hora: "7:30 p.m.",
    lugar: "Capilla Divino Niño",
    esPlaceholder: true
  },

  /* --- "¿Es tu primera vez?" ----------------------------------------------
     Puntos cortos para quien nunca vino. Los dos primeros están confirmados
     (entrevista: sin costo, sin cupos, sin inscripción). */
  primeraVez: [
    { titulo: "Sin costo", texto: "No se paga nada para participar, ni en las reuniones ni en los eventos." },
    { titulo: "Sin inscripción", texto: "No hace falta avisar antes ni reservar cupo. Solo ven." },
    { titulo: "Alabanza, Palabra y oración", texto: "Cantamos, escuchamos una enseñanza de la Biblia y oramos juntos por las intenciones de cada uno.", esPlaceholder: true },
    { titulo: "Ven como estás", texto: "No necesitas saber cantar ni conocer a nadie. Te recibimos en la puerta.", esPlaceholder: true }
  ],

  /* --- Quiénes somos (página "Grupo de oración") -------------------------- */
  quienesSomos: {
    rcc: "La Renovación Carismática Católica es un movimiento de la Iglesia que invita a vivir una relación personal con Jesús y a abrirse a la acción del Espíritu Santo, en comunión con la parroquia y la diócesis.",
    historia: "Aquí va la historia del grupo: cuándo empezó, quiénes lo iniciaron y cómo ha crecido. Pendiente de recopilar con los servidores del grupo.",
    esPlaceholder: true
  },

  /* --- Servidores ----------------------------------------------------------
     Foto del equipo. Para publicar nombres y cargos, pide permiso a cada uno. */
  servidores: {
    foto: "assets/img/seminario/servidores.webp",
    alt: "Equipo de cinco servidores del grupo de pie frente al altar de la capilla",
    texto: "Los servidores acompañan cada reunión: animan la alabanza, preparan los temas y guían los grupos de compartir. Nombres y cargos por confirmar.",
    esPlaceholder: true
  },

  /* --- Instagram -----------------------------------------------------------
     Las publicaciones NO se escriben aquí: las trae solas una tarea automática
     de GitHub (ver docs/guia-instagram.md) y se guardan en data/instagram.json.
     Aquí solo va el nombre de usuario, para el enlace "Ver en Instagram".
     Déjalo vacío ("") mientras no exista la cuenta. */
  redes: {
    instagram: "",
    facebook: ""
  },

  /* --- La capilla ----------------------------------------------------------
     Ubicación. Se escribe UNA sola vez aquí: de estas coordenadas salen el mapa,
     el botón "Abrir en Google Maps" y el enlace "Cómo llegar".

     Para cambiarlas: en Google Maps, clic derecho justo sobre la capilla y
     pulsa las dos cifras que aparecen arriba del menú (eso las copia).
     No uses enlaces cortos (maps.app.goo.gl): caducan y dejan el botón roto. */
  capilla: {
    nombre: "Capilla Divino Niño",
    direccion: "Irlanda, Lima 15494 · Plus Code X35F+QPP",
    coordenadas: "-12.0405377,-76.925658",
    etiquetaMapa: "Capilla Divino Niño"
  },

  /* --- Horarios de misa de la capilla --------------------------------------
     "horas" admite todas las que necesites: ["7:00 am", "10:00 am", "6:00 pm"]
     "nota" es opcional (por ejemplo: "Solo durante Cuaresma"). */
  horariosMisa: [
    { dia: "Domingo", horas: ["8:00 am", "6:00 pm"], nota: "", esPlaceholder: true },
    { dia: "Sábado", horas: ["7:00 pm"], nota: "Misa de vigilia", esPlaceholder: true },
    { dia: "Entre semana", horas: ["7:00 pm"], nota: "", esPlaceholder: true }
  ],

  /* --- Horarios especiales (Semana Santa, Cuaresma, difuntos) ------------- */
  horariosEspeciales: [
    { titulo: "Cuaresma", detalle: "Vía Crucis los viernes. Horario por confirmar.", esPlaceholder: true },
    { titulo: "Semana Santa", detalle: "Programación completa publicada dos semanas antes.", esPlaceholder: true },
    { titulo: "Misa de difuntos", detalle: "Día, hora y cómo solicitarla: por confirmar.", esPlaceholder: true }
  ],

  /* --- Servicios de la capilla --------------------------------------------- */
  servicios: [
    {
      nombre: "Bautizo",
      requisitos: ["Partida de nacimiento", "Copia del DNI de los padres", "Padrinos confirmados", "Charla prebautismal"],
      horario: "Requisitos, costo y fechas por confirmar.",
      esPlaceholder: true
    },
    {
      nombre: "Confesiones",
      requisitos: [],
      horario: "Días y horarios por confirmar.",
      esPlaceholder: true
    },
    {
      nombre: "Consejería y acompañamiento",
      requisitos: ["Cita previa"],
      horario: "Quién la brinda y cómo pedir cita: por confirmar.",
      esPlaceholder: true
    }
  ],

  /* --- Catequesis ----------------------------------------------------------
     Estos datos vienen del afiche de la Parroquia San Francisco Javier.
     Falta confirmar si también se dicta en la Capilla Divino Niño: mientras
     tanto lleva la etiqueta "Por confirmar".
     Para ocultar el bloque, pon inscripcionesAbiertas en false. */
  catequesis: {
    inscripcionesAbiertas: true,
    titulo: "Catequesis de iniciación cristiana",
    anuncio: "Inscripciones abiertas",
    informes: {
      dias: "Martes a viernes",
      horario: "De 7:00 p.m. a 9:00 p.m.",
      lugar: "Parroquia San Francisco Javier"
    },
    grupos: [
      { publico: "Para niños", edades: "desde los 8 hasta los 13 años", sacramentos: ["Bautismo", "Eucaristía"] },
      { publico: "Para jóvenes", edades: "desde los 15 hasta los 19 años", sacramentos: ["Bautismo", "Eucaristía", "Confirmación"] }
    ],
    esPlaceholder: true
  },

  /* --- Campaña de donaciones -----------------------------------------------
     Solo donaciones físicas: la web NUNCA recibe ni mueve dinero.

     csvAvance: el enlace del CSV publicado desde Google Sheets
     (paso a paso en docs/guia-donaciones-sheets.md). Si está vacío o falla,
     se muestran los números de "articulos" de aquí abajo.

     formularioDonacion: el Google Form para registrar una donación. Este NO es
     anónimo: se pide nombre y teléfono para coordinar la entrega.

     En "articulos", meta y actual van SIN comillas (son números). */
  donaciones: {
    titulo: "Campaña de Navidad",
    fechaLimite: "Recibimos donaciones hasta poco antes de Navidad.",
    destino: "Todo lo recolectado se entrega, de forma voluntaria, a familias de bajos recursos.",
    csvAvance: "",
    formularioDonacion: "",
    articulos: [
      { item: "Arroz", meta: 50, actual: 0, unidad: "kg", esPlaceholder: true },
      { item: "Leche", meta: 60, actual: 0, unidad: "tarros", esPlaceholder: true },
      { item: "Panetón", meta: 40, actual: 0, unidad: "unidades", esPlaceholder: true },
      { item: "Juguetes", meta: 50, actual: 0, unidad: "unidades", esPlaceholder: true }
    ],
    puntoEntrega: "Capilla Divino Niño. Días y horarios de recepción por confirmar.",
    noRecibimos: "Qué productos no se reciben: por confirmar.",
    esPlaceholder: true
  },

  /* --- Eventos ---------------------------------------------------------------
     Todos los eventos son sin costo, sin cupos y sin inscripción previa.

     CÓMO SE ESCRIBE UN EVENTO
       titulo, fecha (texto libre: "15 de noviembre, 7:00 pm"), lugar, descripcion
       pasado: true    → va a "Eventos pasados". Sin esa línea, es un evento próximo.
       portada         → foto que se ve en la tarjeta de la portada del sitio.
       simbolo         → (opcional) dibujo decorativo junto al título.
       viernes / dias  → (opcional) cada encuentro del evento: al tocarlo se abre
                         su foto en grande. "tema" puede quedar vacío ("").
       fotos           → (opcional) el carrusel de fotos del evento.

     FOTOS: se publican SOLO con permiso de quienes aparecen. Mientras no haya
     un criterio del grupo sobre fotos de niños, ninguna foto con menores.
     "alt" describe la foto para quien no puede verla: escríbelo siempre. */
  eventos: [
    {
      titulo: "Próximo evento del grupo",
      fecha: "Fecha por confirmar",
      descripcion: "Aquí aparecerán los retiros, vigilias y encuentros del grupo.",
      esPlaceholder: true
    },
    {
      titulo: "Seminario de Vida en el Espíritu",
      pasado: true,
      fecha: "7 viernes por la noche",
      lugar: "Capilla Divino Niño",
      descripcion: "Siete viernes para conocer el amor de Dios y abrir el corazón a la acción del Espíritu Santo. Cada tema se vivió primero en asamblea y luego en pequeños grupos de compartir.",
      portada: { foto: "assets/img/seminario/asamblea-mural.webp", alt: "Asamblea del seminario en la capilla, con el mural del Espíritu Santo detrás del altar" },
      simbolo: "assets/img/paloma-llamas.webp",

      /* Un encuentro por viernes. Las fotos asignadas a cada viernes son de
         EJEMPLO: cambia cada una por una foto de ese viernes y escribe su tema.
         Cuando estén confirmadas, borra la línea viernesEsPlaceholder. */
      viernesEsPlaceholder: true,
      viernes: [
        { tema: "", foto: "assets/img/seminario/asamblea-mural.webp", alt: "Asamblea del seminario en la capilla, con el mural del Espíritu Santo detrás del altar" },
        { tema: "", foto: "assets/img/seminario/compartir-1.webp", alt: "Grupo pequeño conversando con las hojas del tema en la mano" },
        { tema: "", foto: "assets/img/seminario/alabanza.webp", alt: "Participantes de rodillas en oración mientras un servidor toca la guitarra" },
        { tema: "", foto: "assets/img/seminario/compartir-3.webp", alt: "Hombres y mujeres comparten en círculo bajo el cartel del tema El pecado y sus consecuencias" },
        { tema: "", foto: "assets/img/seminario/asamblea-coronas.webp", alt: "Asamblea de participantes con coronas de papel amarillo" },
        { tema: "", foto: "assets/img/seminario/oracion.webp", alt: "Personas arrodilladas en oración frente al altar iluminado con velas" },
        { tema: "", foto: "assets/img/seminario/servidoras.webp", alt: "Cuatro servidoras sonríen frente al altar con las bolsas del seminario" }
      ],

      fotos: [
        { foto: "assets/img/seminario/asamblea-mural.webp", alt: "Asamblea del seminario en la capilla, con el mural del Espíritu Santo detrás del altar", pie: "Asamblea" },
        { foto: "assets/img/seminario/compartir-2.webp", alt: "Cuatro participantes conversan sentados en círculo durante el compartir", pie: "Grupos de compartir" },
        { foto: "assets/img/seminario/alabanza.webp", alt: "Participantes de rodillas en oración mientras un servidor toca la guitarra", pie: "Alabanza y oración" },
        { foto: "assets/img/seminario/compartir-1.webp", alt: "Grupo pequeño conversando con las hojas del tema en la mano", pie: "Grupos de compartir" },
        { foto: "assets/img/seminario/asamblea-coronas.webp", alt: "Asamblea de participantes con coronas de papel amarillo", pie: "Asamblea" },
        { foto: "assets/img/seminario/oracion.webp", alt: "Personas arrodilladas en oración frente al altar iluminado con velas", pie: "Oración" },
        { foto: "assets/img/seminario/compartir-3.webp", alt: "Hombres y mujeres comparten en círculo bajo el cartel del tema del día", pie: "Grupos de compartir" },
        { foto: "assets/img/seminario/servidoras.webp", alt: "Cuatro servidoras sonríen frente al altar con las bolsas del seminario", pie: "Servidores" },
        { foto: "assets/img/seminario/servidores-2.webp", alt: "Tres servidores posan frente al altar con las bolsas del seminario", pie: "Servidores" },
        { foto: "assets/img/seminario/asamblea.webp", alt: "Vista amplia de la capilla llena durante el seminario", pie: "Asamblea" }
      ]
    }
  ],

  /* --- Testimonios ---------------------------------------------------------
     PUBLICAR SOLO CON CONSENTIMIENTO DE LA PERSONA.
     Si prefiere no dar su nombre, escribe autor: "Participante del seminario". */
  testimonios: [
    { texto: "Llegué al seminario sin conocer a nadie y salí con una comunidad que ora por mí cada semana.", autor: "Participante del seminario", esPlaceholder: true },
    { texto: "Los viernes se volvieron el momento de la semana en que dejo todo en manos de Dios.", autor: "Miembro del grupo", esPlaceholder: true }
  ],

  /* --- Preguntas frecuentes del bot ---------------------------------------
     El bot compara lo que escribe la persona con las palabras de "claves".
     Pon varias formas de decir lo mismo, en minúsculas y sin tildes.
     "sugerida" es el botón de acceso rápido dentro del chat ("" = sin botón). */
  faqBot: [
    {
      claves: ["reunion", "reuniones", "cuando se reunen", "a que hora", "horario del grupo", "grupo de oracion"],
      sugerida: "¿Cuándo se reúnen?",
      respuesta: "Nos reunimos los viernes a las 7:30 p.m. en la Capilla Divino Niño (horario por confirmar). Es abierto a todos, sin costo y sin inscripción."
    },
    {
      claves: ["primera vez", "nuevo", "nueva", "puedo ir", "como participar", "unirme"],
      sugerida: "Es mi primera vez",
      respuesta: "¡Bienvenido! No necesitas inscribirte ni pagar nada. Solo ven a la reunión: cantamos, escuchamos la Palabra y oramos juntos. Puedes ver más en la pestaña Grupo de oración."
    },
    {
      claves: ["seminario", "vida en el espiritu", "evento", "eventos", "retiro", "fotos"],
      sugerida: "¿Qué eventos tienen?",
      respuesta: "Nuestro último evento fue el Seminario de Vida en el Espíritu: siete viernes de alabanza, enseñanza y compartir. Sus fotos y los próximos eventos están en la pestaña Comunidad. Todos son sin costo y sin inscripción."
    },
    {
      claves: ["misa", "misas", "horario de misa", "eucaristia"],
      sugerida: "¿A qué hora es la misa?",
      respuesta: "Los horarios de misa de la Capilla Divino Niño están en la pestaña Capilla. Algunos todavía están por confirmar."
    },
    {
      claves: ["donar", "donacion", "donaciones", "colaborar", "que necesitan", "navidad"],
      sugerida: "¿Cómo puedo donar?",
      respuesta: "Tenemos una campaña de Navidad. Solo recibimos donaciones físicas (alimentos, juguetes), nunca dinero. En la pestaña Solidaridad ves qué falta y cómo registrar tu donación."
    },
    {
      claves: ["ayuda", "necesito ayuda", "apoyo", "pedir ayuda"],
      sugerida: "Necesito pedir ayuda",
      respuesta: "En la pestaña Solidaridad hay un formulario para pedir apoyo. Puedes enviarlo de forma anónima. Alguien del grupo lo revisará."
    },
    {
      claves: ["donde", "direccion", "ubicacion", "como llego", "capilla"],
      sugerida: "¿Dónde quedan?",
      respuesta: "Nos reunimos en la Capilla Divino Niño. En la portada hay un mapa y un botón para abrir la ruta en Google Maps."
    },
    {
      claves: ["catequesis", "primera comunion", "comunion", "confirmacion"],
      sugerida: "",
      respuesta: "Los datos de catequesis están en la pestaña Capilla. Informes de martes a viernes, de 7:00 p.m. a 9:00 p.m."
    }
  ],

  /* --- Texto que usa el bot cuando NO entiende la pregunta ---------------- */
  botFallback: "No estoy seguro de cómo responder eso. Si quieres, escríbele directamente al coordinador del grupo por WhatsApp.",

  /* --- Contacto --------------------------------------------------------------
     whatsapp: código de país + número, sin +, sin espacios. Perú = 51.
     Los formularios se crean en Google Forms y se pega el enlace completo. */
  contacto: {
    whatsapp: "51900000000",
    responsable: "Coordinador del grupo",
    horarioAtencion: "Horario de atención por confirmar.",
    googleFormContacto: "https://forms.gle/XXXXXXXXXXXX",
    googleFormAyuda: "https://forms.gle/YYYYYYYYYYYY",
    esPlaceholder: true
  },

  /* --- Aviso de privacidad (se muestra en letra pequeña bajo los formularios) */
  privacidad: "Tus datos solo los ve el equipo del grupo, se usan únicamente para responderte y no se comparten con nadie (Ley N.° 29733)."
};
