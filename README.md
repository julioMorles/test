# test
    Esta prueba se realiza usuando los frameworks Laravel para el backend y jQuery para el frondend.
Prueba de Programación

## Configuración de Laravel
Lo primero que debes tener en cuenta es que Laravel es un framework para PHP, por lo cual debes contar con un servidor web

Para instalar los componentes se recomienda descargar e instalar Composer (https://getcomposer.org/download/), luego en la ruta /api/ ejecutar en consola

    $ composer install
existe un archivo llamado .env.example que es un ejemplo de como crear un el archivo de configuración, podemos copiar este archivo desde la consola con:

    $ cp .env.example .env
Por medidas de seguridad cada proyecto de Laravel cuenta con una clave única que se crea en el archivo .env al iniciar el proyecto.

    $ php artisan key:generate

Desde la consola (usando MySql) podrías hacer algo similar a esto

    $ mysql -uroot -psecret

    mysql> CREATE DATABASE tu_base_de_datos;
Posteriormente debes agregar las credenciales al archivo .env

    DB_HOST=localhost
    DB_DATABASE=tu_base_de_datos
    DB_USERNAME=root
    DB_PASSWORD=
    
Finalmente estarás habilitado para ejecutar la migración desde la consola usando artisan
    
    $ php artisan migrate 




