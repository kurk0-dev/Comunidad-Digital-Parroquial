#!/usr/bin/env python3
"""Descarga las últimas publicaciones de Instagram del grupo para la web.

Lo ejecuta la tarea automática .github/workflows/instagram.yml cada 6 horas.

Qué hace:
  1. Pide a la API de Instagram las últimas publicaciones de la cuenta.
  2. Descarga cada foto a assets/instagram/<id>.jpg (las direcciones de
     Instagram caducan a los pocos días, así que no se pueden enlazar).
  3. Escribe data/instagram.json, que es lo único que lee la página.

SEGURIDAD — el token:
  · Llega solo por la variable de entorno IG_TOKEN (un secreto de GitHub).
  · Nunca se escribe en ningún archivo ni se imprime: los errores se muestran
    sin la dirección de la petición, que es donde viaja el token.
  · El navegador del visitante nunca lo ve: la web solo lee el JSON.

Solo usa la biblioteca estándar de Python: no instala nada.
"""

import json
import os
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

API = "https://graph.instagram.com/v25.0"
RAIZ = Path(__file__).resolve().parent.parent
CARPETA_FOTOS = RAIZ / "assets" / "instagram"
ARCHIVO_JSON = RAIZ / "data" / "instagram.json"

MAXIMO_PUBLICACIONES = 9
LIMITE_IMAGEN = 8 * 1024 * 1024
LIMITE_RESPUESTA = 1024 * 1024
CDN_PERMITIDOS = ("cdninstagram.com", "fbcdn.net")
TIPOS = {"IMAGE", "VIDEO", "CAROUSEL_ALBUM"}
AGENTE = {"User-Agent": "go-divino-nino-web/1.0"}


class ErrorInstagram(Exception):
    """Error con un mensaje seguro de mostrar (nunca contiene el token)."""


def pedir_json(ruta, parametros, token):
    consulta = urllib.parse.urlencode(dict(parametros, access_token=token))
    peticion = urllib.request.Request(f"{API}{ruta}?{consulta}", headers=AGENTE)
    try:
        with urllib.request.urlopen(peticion, timeout=20) as respuesta:
            return json.loads(respuesta.read(LIMITE_RESPUESTA))
    except urllib.error.HTTPError as error:
        try:
            detalle = json.loads(error.read(100_000)).get("error", {})
        except (ValueError, OSError):
            detalle = {}
        if detalle.get("code") == 190:
            raise ErrorInstagram(
                "El token de Instagram venció o no es válido. Genera uno nuevo y "
                "actualiza el secreto IG_TOKEN (ver docs/guia-instagram.md)."
            ) from None
        raise ErrorInstagram(
            f"Instagram respondió {error.code}: {detalle.get('message', 'sin detalle')}"
        ) from None
    except (urllib.error.URLError, TimeoutError, OSError, ValueError):
        # "from None" corta la cadena de errores: así no se imprime la URL.
        raise ErrorInstagram("No se pudo conectar con Instagram.") from None


def es_cdn_de_instagram(url):
    partes = urllib.parse.urlparse(url)
    host = partes.hostname or ""
    return partes.scheme == "https" and any(
        host == cdn or host.endswith("." + cdn) for cdn in CDN_PERMITIDOS
    )


def extension_de_imagen(datos):
    """Comprueba por la firma del archivo que de verdad sea una imagen."""
    if datos[:3] == b"\xff\xd8\xff":
        return "jpg"
    if datos[:4] == b"RIFF" and datos[8:12] == b"WEBP":
        return "webp"
    if datos[:8] == b"\x89PNG\r\n\x1a\n":
        return "png"
    return None


def descargar_imagen(url, identificador):
    if not es_cdn_de_instagram(url):
        raise ErrorInstagram("la imagen no viene del servidor de Instagram")
    peticion = urllib.request.Request(url, headers=AGENTE)
    try:
        with urllib.request.urlopen(peticion, timeout=30) as respuesta:
            datos = respuesta.read(LIMITE_IMAGEN + 1)
    except (urllib.error.URLError, TimeoutError, OSError):
        raise ErrorInstagram("no se pudo descargar la imagen") from None
    if len(datos) > LIMITE_IMAGEN:
        raise ErrorInstagram("la imagen pesa demasiado")
    extension = extension_de_imagen(datos)
    if not extension:
        raise ErrorInstagram("el archivo descargado no es una imagen (¿un video?)")
    ruta = CARPETA_FOTOS / f"{identificador}.{extension}"
    ruta.write_bytes(datos)
    return ruta


def foto_ya_descargada(identificador):
    for extension in ("jpg", "webp", "png"):
        ruta = CARPETA_FOTOS / f"{identificador}.{extension}"
        if ruta.exists():
            return ruta
    return None


def leer_json_anterior():
    try:
        return json.loads(ARCHIVO_JSON.read_text(encoding="utf-8"))
    except (OSError, ValueError):
        return {}


def actualizar(token):
    perfil = pedir_json("/me", {"fields": "username"}, token)
    usuario = perfil.get("username", "")
    if not re.fullmatch(r"[A-Za-z0-9._]{1,30}", usuario or ""):
        usuario = ""

    respuesta = pedir_json(
        "/me/media",
        {
            "fields": "id,caption,media_type,media_url,thumbnail_url,permalink,timestamp",
            "limit": MAXIMO_PUBLICACIONES,
        },
        token,
    )

    CARPETA_FOTOS.mkdir(parents=True, exist_ok=True)
    publicaciones = []

    for medio in respuesta.get("data", [])[:MAXIMO_PUBLICACIONES]:
        identificador = str(medio.get("id", ""))
        tipo = medio.get("media_type")
        enlace = str(medio.get("permalink", ""))

        # El id se usa como nombre de archivo: solo dígitos, nada de rutas.
        if not re.fullmatch(r"\d{1,30}", identificador) or tipo not in TIPOS:
            continue
        if not enlace.startswith("https://www.instagram.com/"):
            continue

        # Los videos no traen foto en media_url: se usa su miniatura.
        fuente = medio.get("thumbnail_url") if tipo == "VIDEO" else medio.get("media_url")
        if not fuente:
            continue

        try:
            ruta = foto_ya_descargada(identificador) or descargar_imagen(fuente, identificador)
        except ErrorInstagram as error:
            print(f"Publicación {identificador} omitida: {error}")
            continue

        publicaciones.append({
            "id": identificador,
            "tipo": tipo,
            "imagen": ruta.relative_to(RAIZ).as_posix(),
            "enlace": enlace,
            "texto": str(medio.get("caption") or "")[:300],
            "fecha": str(medio.get("timestamp", "")),
        })

    # Borra las fotos de publicaciones que ya no están entre las últimas.
    vigentes = {Path(p["imagen"]).name for p in publicaciones}
    for archivo in CARPETA_FOTOS.iterdir():
        if archivo.is_file() and archivo.name not in vigentes and archivo.name != ".gitkeep":
            archivo.unlink()

    nuevo = {"usuario": usuario, "publicaciones": publicaciones}
    anterior = leer_json_anterior()
    anterior.pop("actualizado", None)
    if anterior == nuevo:
        print("Sin publicaciones nuevas.")
        return

    # La fecha solo cambia si cambió el contenido: así no hay un commit cada 6 horas.
    nuevo["actualizado"] = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
    ARCHIVO_JSON.parent.mkdir(parents=True, exist_ok=True)
    ARCHIVO_JSON.write_text(
        json.dumps(nuevo, ensure_ascii=False, indent=2, sort_keys=True) + "\n",
        encoding="utf-8",
    )
    print(f"Guardadas {len(publicaciones)} publicaciones de @{usuario or '?'}.")


def main():
    token = os.environ.get("IG_TOKEN", "").strip()
    if not token:
        print("IG_TOKEN no está configurado: no se actualiza Instagram (ver docs/guia-instagram.md).")
        return 0
    try:
        actualizar(token)
    except ErrorInstagram as error:
        print(f"ERROR: {error}", file=sys.stderr)
        return 1
    except Exception as error:  # noqa: BLE001 — último recurso, sin detalles que filtren el token
        print(f"ERROR inesperado ({type(error).__name__}). La web conserva las publicaciones anteriores.", file=sys.stderr)
        return 1
    return 0


if __name__ == "__main__":
    sys.exit(main())
