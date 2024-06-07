# Com instal·lar el projecte

## Passos per la instal·lació del projecte
- Creem el projecte: "laravel new pt07"
- Importa el projecte de github al nou projecte
- Al nou projecte executem un migrate: "php artisan migrate"
- Afegim el paquet de socialite: "composer require laravel/socialite"
- Executem al projecte la comanda: "php artisan storage:link"
- A la carpeta de storage/public creem la carpeta de avatars i afegim la imatge de default.png

## Canvis al fitxer .env
- Canviem el APP_URL a "http://pt07.com"
- Canviem el MAIL_MAILER a "smtp"
- Canviem el MAIL_HOST a "smtp.gmail.com"
- Canviem el MAIL_PORT a 587
- Canviem el MAIL_USERNAME a el "correu electrònic de google que es vol utilitzar per enviar els email"
- Canviem el MAIL_PASSWORD a "contrasenya del correu electrònic google"
- Canviem el MAIL_ENCRYPTION a tls
- Canviem el MAIL_FROM_ADDRESS="correu electrònic de google"
- Canviem el MAIL_FROM_NAME a "pt07"
- Afegim GOOGLE_OAUTH_ID=""
- Afegim GOOGLE_OAUTH_KEY=""
- Afegim GOOGLE_REDIRECT_URI="/google-callback"

## Canvis al correu electrònic

### Per poder utilitzar el teu correu per l'enviació de correus s'han de fer un canvia la configuració
- En la gestió del compte en l'apartat de seguretat, hi ha de activar el accés d'aplicacions menys segures.

## Oauth
-  Per poder utilitzar el OAuth hauríem de crar un projecte a console.cloud.google.com, configurar la pantalla de consentiment i crear les credencials de client OAuth2 per agafar el OAuth id i la key.
-  Github no em deixa afegir-les al README. 



