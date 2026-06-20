# WebII_TPE_API

#Integrantes
Facundo Figueroa - facu.agus.figueroa@gmail.com

#Tematica

Este Trabajo se basa en distintos locales que se encuentran en una tabla llamada local tengan una relacion con la tabla comidas donde cada comida tiene un local asignado, y a futuro en el MVC, uno pueda ver los distintos locales donde cada uno muestre las distintas comidas que ofrecen

#API RESTful
Siguiendo con la misma base de datos cree una API RESTful que trabaja sobre la tabla comida, esta Api puede:
Enlistar toda la tabla comidas
Ordenarse por cualquiera de los campos de forma ascendente o descendente
Puede traer una comida según su id
Puede paginar la tabla comidas
Puede filtrarse por el campo de tipos, que era el más lógico de usar
Puede crear un usuario requiriendo
Puede generar un token jwt
Puede crear una comida requiriendo un token jwt
Puede actualizar una comida requiriendo un token jwt 
Puede eliminar una comida requiriendo un token jwt
Esta API maneja los códigos de respuesta 200, 201, 400 y 404 

A parte aquí le dejos los endpoints
-GET http://localhost/WEB_II/comidas
Al solicitarle a la API esta URL con el método GET te traerá todas las comidas de la tabla comida de la base de datos 

-GET http://localhost/WEB_II/comidas/[ID]
Al solicitarle a la API esta URL con el método GET te traerá la comida con la ID solicitada de la tabla comida de la base de datos
 
-GET http://localhost/WEB_II/comidas?tipo=[ID]
Al solicitarle a la API esta URL con el método GET te traerá la comida todas las comidas con la ID del tipo de comida solicitado de la tabla comida de la base de datos 

-GET http://localhost/WEB_II/comidas?sort=[variable a ordenar]&order=[DESC o ASC]
Al solicitarle a la API esta URL con el método GET te traerá todas las comidas de la tabla comida de la base de datos ordenadas de manera descendente[DESC] o ascendente[ASC] según la variable que hayas solicitado [id, nombre, img, descripcion, tipo, precio y clocal] 

-GET http://localhost/WEB_II/comidas?page=[el número de la página]&limit=[límite de comidas por página]
Al solicitarle a la API esta URL con el método GET te traerá la comida todas las comidas paginadas, siendo page el número de la página y límite de comidas por página

-GET http://localhost/WEB_II/comidas? [Combinaciones]
Puedes realizar combinaciones de los GET anteriores para que la API te traiga las comidas de un tipo específico ordenadas y paginadas por ejemplo podrías solicitar esto:
http://localhost/WEB_II/comidas?tipo=2&sort=nombre&order=ASC&page=1&limit=5
Y te deberia traer todas las comidas del tipo dos ordenadas por nombre ascendente en la página 1 y que cada página tenga el límite de 5 comidas, se puede probar cualquier cosa mientras se mantenga el orden de, primero el tipo (?tipo=[ID]) si no se usa lo primero el orden y lo segundo la paginación, segundo es el orden (?sort=[variable a ordenar]&order=[DESC o ASC]) si no se usa lo segundo seria la paginación y lo ultimo seria la paginación (?page=[el número de la página]&limit=[límite de comidas por página]) si no se usa, no se coloca el final y listo.

-POST http://localhost/WEB_II/usuarios
Al solicitarle a la API esta URL con el método POST y colocar en body la opción raw JSON y pasarle 
{
    "usuario": "[Nombre de usuario]",
    "contrasena": "[Contraseña]"
}
Te debería generar un nuevo usuario, sin admin; no era necesario pero vi bien colocarlo

-GET http://localhost/WEB_II/token
Al solicitarle a la API esta URL con el método GET y colocando en authorization basic auth y llenándolo los datos de username con el nombre y password con la contraseña debería devolverte un token JWT que contiene una fecha de expiración de 1 hora al momento de crearse

POST http://localhost/WEB_II/comidas [Requiere Token]
Al solicitarle a la API esta URL con el método POST colocando en authorization Bearer Token un Token JWT que no haya expirado y tenga permisos de administrador, y colocando en el body la opción raw JSON y pasarle
{
    "nombre": "[Nombre de comida]",
    "img": "[URL de la imagen de la comida]",
    "descripcion": "[Descripción de la comida]",
    "tipo": [Tipo de la comida, como número entero],
    "precio": [Precio de la comida, como número entero],
    "clocal": [Local que pertenece la comida, como número entero]
}
Si los datos están correctos y el token contiene el role admin, se creará la comida y te devolverá el json de la comida creada, ósea la id, el nombre, la URL de la imagen, la descripción, el tipo, el precio, y el local

PUT http://localhost/WEB_II/comidas/[ID] [Requiere Token]
Al solicitarle a la API esta URL con el método PUT colocando en authorization Bearer Token un Token JWT que no haya expirado y tenga permisos de administrador, y colocando en el body la opción raw JSON y pasarle
{
    "nombre": "[Nombre de comida]",
    "img": "[URL de la imagen de la comida]",
    "descripcion": "[Descripción de la comida]",
    "tipo": [Tipo de la comida, como número entero],
    "precio": [Precio de la comida, como número entero],
    "clocal": [Local que pertenece la comida, como número entero]
}
Si los datos están correctos y el token contiene el role admin, se modificara la comida y te devolverá el json de la comida creada, ósea la id, el nombre, la URL de la imagen, la descripción, el tipo, el precio, y el local

DELETE http://localhost/WEB_II/comidas/[ID] [Requiere Token]
Al solicitarle a la API esta URL con el método DELETE colocando en authorization Bearer Token un Token JWT que no haya expirado, si el token tiene el role admin se eliminará la comida de la ID pasada


Si quedo alguna duda realice una documentación de toda la página, donde se también se encontrar los Endpoints, El funcionamiento y ejecución de funciones al utilizar cada Endpoint y luego tienen un apartado donde podrán cada archivo a detalles y sus funciones que lo componen

Aquí les dejo la documentación:

https://docs.google.com/document/d/1mvLUGtBf34bwKFTkzOplBSPnfAQuY9qicfftyUN3Jl8/edit?usp=sharing

#Relacion de tablas 

<img width="617" height="617" alt="Local Comida" src="https://github.com/CROSSZero97/Web_TP-II/blob/main/Local%20Comida.png">
