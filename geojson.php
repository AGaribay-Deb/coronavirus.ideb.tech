<?php
#Recibe GET de País y de Fecha, si algo está mal por defecto id_pais = 1 y id_dia = HOY
include("main.php");
$conex = conex();
$datos_array = inicializar_datos($conex);
$id_pais = $datos_array['id_pais'];
$id_dia = $datos_array['id_dia'];
print(obtener_geojson($conex,$id_pais,$id_dia))
?>