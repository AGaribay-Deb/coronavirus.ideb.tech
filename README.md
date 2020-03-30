# coronavirus.ideb.tech
Página web sobre la propagación del coronavirus en el Perú
****************************************************************************************************************************
Requisitos para el sistema:
*Sistema operativo base Linux
*Un servidor web que pueda ejecutar php7.2 o superior
*Base de datos relacional, mariadb o mysql de preferencia
*Trabajos programados en linux (cron)


******************************************************************************************************************************
Funcionalidades:
*Ver la propagación del coronavirus en los paises que hayan sido agregados al sistema.
*Ver por dias, por defecto siempre aparece el día de hoy, usa un trabajo cron en linux para actualizarlo.
*Ver gráfico.
*Ver comparativas con respecto al día anterior.
*Navegar por los paises que fueron agregados.
**************************************************************************************************************************

Funcionalidades que quisiera agregar
*Comvertirlo a POO
*Hacer una administración para agregar paises, ya que por el momento sólo se ingresan o actualizan datos.
*Usar AJAX para que no se recargue la página y sea en tiempo real.


*****************************************************************************************************************************
Acerca de
Sistema diseñado por Carlos Alejos y desarrollado por Anthony Garibay, tiene por finalidad informar a la ciudadanía la propagación del coronavirus(COVID-19) en todo el territorio peruano. Impulsado con la tecnología de Google Maps y los datos GeoJSON de delimitación de territorios extraídos de GitHub.

La información que brindamos por medio de https://coronavirus.ideb.tech es extraída de la página oficial en Twitter del Ministerio de Salud y de los medios locales de mayor fiabilidad.

Leyenda sobre Infectados, Muertos y Recuperados:

Infectados: Persona que ha sido detectada con el virus COVID-19, NO SE CUENTAN PERSONAS FALLECIDAS O CURADAS.
Fallecidos: Persona que falleció a causa del virus COVID-19.
Curados: Persona que se recuperó del virus COVID-19.
El sitio https://coronavirus.ideb.tech usa cookies para que se obtenga una mejor experiencia, al navegar por ella usted las acepta.

El sitio https://coronavirus.ideb.tech se adapta la licencia GPL.

El sitio https://coronavirus.ideb.tech podrá ser incrustado en cualquier página web que desee usar el servicio siempre y cuando ponga la dirección completa en un lugar visible. Lo podrá hacer mediante la etiqueta iframe de html5.



****************************************************************************************************************

El archivo coronavirus.db.txt contiene todo lo relacionado con la base de datos del sistema

El archivo main.php contiene la mayoría de funcionalidades del sistema, está escrito en el paradigma funcional, la meta es convertirlo a POO.

El archivo geojson.php arma el mapa en formato geojson para que pueda conjugar con el API de Google Maps.

El archivo index.php contiene la vista del sistema.

El archivo update.php contiene el frontend de la actualización de datos del sistema.

El archivo update_exec.php contiene el backend de la actualización de datos del sistema.


