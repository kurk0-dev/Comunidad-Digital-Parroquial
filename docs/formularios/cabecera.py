"""Cabecera 1600x400 para los Formularios de Google del grupo."""
import sys
from PIL import Image, ImageDraw, ImageFont, ImageFilter

raiz, salida = sys.argv[1], sys.argv[2]
W, H = 1600, 400
ROJO, OSCURO, AMARILLO, CREMA = (207, 4, 13), (120, 1, 8), (246, 197, 38), (251, 247, 240)

# Fondo: degradado diagonal de rojo a rojo oscuro.
fondo = Image.new("RGB", (W, H), ROJO)
px = fondo.load()
for y in range(H):
    for x in range(W):
        t = min(1, max(0, (x / W) * 0.75 + (y / H) * 0.35))
        px[x, y] = tuple(int(ROJO[i] + (OSCURO[i] - ROJO[i]) * t) for i in range(3))

# Resplandor suave detrás del escudo.
luz = Image.new("L", (W, H), 0)
ImageDraw.Draw(luz).ellipse((90, 30, 430, 370), fill=55)
luz = luz.filter(ImageFilter.GaussianBlur(70))
fondo = Image.composite(Image.new("RGB", (W, H), (255, 214, 120)), fondo, luz)

d = ImageDraw.Draw(fondo)
# Escudo dentro de un círculo crema con filete amarillo.
cx, cy, r = 260, 200, 150
d.ellipse((cx - r - 6, cy - r - 6, cx + r + 6, cy + r + 6), fill=AMARILLO)
d.ellipse((cx - r, cy - r, cx + r, cy + r), fill=CREMA)
escudo = Image.open(f"{raiz}/assets/img/escudo.webp").convert("RGBA")
# El escudo trae fondo blanco: se recorta en círculo.
lado = min(escudo.size)
escudo = escudo.crop(((escudo.width - lado) // 2, (escudo.height - lado) // 2, (escudo.width + lado) // 2, (escudo.height + lado) // 2))
escudo = escudo.resize((2 * r - 12, 2 * r - 12), Image.LANCZOS)
mascara = Image.new("L", (escudo.width * 4, escudo.height * 4), 0)
ImageDraw.Draw(mascara).ellipse((0, 0, mascara.width - 1, mascara.height - 1), fill=255)
mascara = mascara.resize(escudo.size, Image.LANCZOS)
fondo.paste(escudo, (cx - escudo.width // 2, cy - escudo.height // 2), mascara)

f = "C:/Windows/Fonts/"
sobre = ImageFont.truetype(f + "segoeuib.ttf", 26)
titulo = ImageFont.truetype(f + "georgiab.ttf", 64)
sub = ImageFont.truetype(f + "segoeui.ttf", 32)
x0 = 480
d.text((x0, 112), "RENOVACIÓN CARISMÁTICA CATÓLICA", font=sobre, fill=AMARILLO)
d.text((x0, 150), "Grupo de Oración", font=titulo, fill="white")
d.text((x0, 222), "Divino Niño Jesús", font=titulo, fill="white")
d.line((x0, 312, x0 + 90, 312), fill=AMARILLO, width=4)
d.text((x0 + 110, 292), "Capilla Divino Niño · Diócesis de Chosica", font=sub, fill=CREMA)

fondo.save(salida, optimize=True)
print(salida)
