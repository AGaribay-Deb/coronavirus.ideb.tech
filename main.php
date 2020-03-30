<?php
date_default_timezone_set('UTC');
function conex(){
	#Conexión a la base de datos
	$conex = new mysqli("localhost","user","pass","db");
	if ($conex->connect_error) {
	    die('Error de Conexión a la Base de Datos. Inténtelo de nuevo más tarde');
	}
	$conex->set_charset("utf8");
	return $conex;
}
function validar_id_pais($conex, $id_pais){
	#Sólo acepta números de 1 a 3 dígitos,sino devuelve falso
	if(preg_match("/^[0-9]{1,3}$/",$id_pais)!=1){
		return false;
		exit();
	}
	$consulta_obtener_pais = $conex->query("SELECT nombre FROM paises WHERE id_pais = $id_pais;");
	#Si no obtiene un resultado devuelve falso
	if($consulta_obtener_pais->num_rows != 1){
		return false;
		exit();
	}
	return true;
}
function validar_id_dia($conex, $id_dia, $id_pais){
	#Sólo acepta números de 1 a 4 dígitos y que el dia le pertenezca al pais, sino devuelve falso
	if(preg_match("/^[0-9]{1,4}$/",$id_dia)!=1){
		return false;
		exit();
	}
	/*
	$consulta_obtener_dia = $conex->query("SELECT DISTINCT(dias.fecha) FROM dias INNER JOIN registros on dias.id_dia = registros.id_dia INNER JOIN departamentos ON registros.id_departamento = departamentos.id_departamento WHERE departamentos.id_pais = $id_pais AND registros.id_dia = $id_dia;");
	#Si no obtiene un resultado devuelve falso
	if($consulta_obtener_dia->num_rows != 1){
		return false;
		exit();
	}*/
	return true;
}
function validar_id_dia_update($conex, $id_dia, $id_pais){
	#Sólo acepta números de 1 a 4 dígitos y que el dia le pertenezca al pais, sino devuelve falso
	if(preg_match("/^[0-9]{1,4}$/",$id_dia)!=1){
		return false;
		exit();
	}
	$resultado_datos_pais = obtener_datos_pais($conex,$id_pais);
	$date = new DateTime();
	$date->modify($resultado_datos_pais['zona_horaria'].' hours');
	$hoy = $date->format('Y-m-d');
	$id_today =  obtener_id_dia_por_fecha($conex, $hoy);
	
	$consulta_obtener_dia = $conex->query("SELECT fecha FROM dias WHERE id_dia = $id_dia AND id_dia <= $id_today[id_dia];");
	
	#Si no obtiene un resultado devuelve falso
	if($consulta_obtener_dia->num_rows == 0){
		return false;
		exit();
	}
	return true;
}


function obtener_fechas($conex, $id_pais, $id_dia,$tipo){
	#Listar desde hoy hasta el primer día hasta hoy, si no hay primer día poner sólo hoy
	#Devuelve las fechas del pais seleccionado listo para html(select)
	$id_dia_hoy = obtener_id_hoy($conex, $id_pais);
	if($tipo == 'update'){
		$consulta_obtener_fechas = $conex->query("SELECT fecha, id_dia FROM dias WHERE id_dia <= $id_dia_hoy ORDER BY dias.fecha DESC;");
	}else{
		$consulta_primer_dia = $conex->query("SELECT registros.id_dia FROM dias INNER JOIN registros on dias.id_dia = registros.id_dia INNER JOIN departamentos ON registros.id_departamento = departamentos.id_departamento WHERE departamentos.id_pais = $id_pais ORDER BY dias.fecha ASC LIMIT 1;");
		$resultado_primer_dia = $consulta_primer_dia->fetch_array();
		if($resultado_primer_dia['id_dia'] == ''){
			#retornar los 40 ultimas fechas
			$consulta_obtener_fechas = $conex->query("SELECT fecha, id_dia FROM dias WHERE id_dia <= $id_dia_hoy ORDER BY dias.fecha DESC LIMIT 40;");
		}else{
			$consulta_obtener_fechas = $conex->query("SELECT fecha, id_dia FROM dias WHERE id_dia BETWEEN $resultado_primer_dia[id_dia] AND $id_dia_hoy ORDER BY dias.fecha DESC;");
		}
	}

	
	




	$lineas = $consulta_obtener_fechas->num_rows;
	$fechas = "<div id='fechas' class='form-group'><label for='inputState'>Fecha:</label><select id='inputState' class='form-control' onchange='location = this.value;'>";
	$primero = '';
	$segundo = '';
	while($resultado_obtener_fechas = $consulta_obtener_fechas->fetch_array()){
		if($resultado_obtener_fechas['id_dia'] == $id_dia){
			$primero .= "<option selected>$resultado_obtener_fechas[fecha]</option>";
		}else{
			$segundo .= "<option value='$_SERVER[PHP_SELF]?id_dia=$resultado_obtener_fechas[id_dia]'>$resultado_obtener_fechas[fecha]</option>";
		}
		$lineas--;
	}
	if($primero == ''){
		$primero = "Hoy";
	}
	$fechas = $fechas.$primero.$segundo."</select></div>";
	return $fechas;
}

function no_manana($conex, $id_pais, $id_dia){
	#Funcion que devuelve el día de hoy	
	$resultado_datos_pais = obtener_datos_pais($conex,$id_pais);
	$date = new DateTime();
	$date->modify($resultado_datos_pais['zona_horaria'].' hours');
	$hoy = $date->format('Y-m-d');
	$resultado_obterner_hoy =  obtener_id_dia_por_fecha($conex, $hoy);
	if($id_dia > $resultado_obterner_hoy['id_dia']){
		$id_dia = $resultado_obterner_hoy['id_dia'];
	}
	return $id_dia;
}



function obtener_paises($conex, $id_pais, $id_dia){
	#Devuelve paises listo para html(select)
	$consulta_obtener_paises = $conex->query("SELECT id_pais,nombre,zona_horaria,latitud,longitud,zoom_mapa FROM paises WHERE 1;");
	$paises = "<div id='paises' class='form-group'><label for='inputState'>Pais:</label><select id='inputState' class='form-control' onchange='location = this.value;'>";
	$primero = '';
	$segundo = '';
	while($resultado_obtener_paises = $consulta_obtener_paises->fetch_array()){
		if($resultado_obtener_paises['id_pais'] == $id_pais){
			$primero .= "<option selected>$resultado_obtener_paises[nombre]</option>";
		}else{
			$segundo .= "<option value='$_SERVER[PHP_SELF]?id_dia=$id_dia&id_pais=$resultado_obtener_paises[id_pais]'>$resultado_obtener_paises[nombre]</option>";
		}
	}
	$paises = $paises.$primero.$segundo."</select></div>";
	return $paises;
}
function obtener_datos_pais($conex,$id_pais){
	#Si todo está bien devuelve los datos del pais, sino devuelve falso
	$consulta_obtener_pais = $conex->query("SELECT nombre,zona_horaria,latitud,longitud,zoom_mapa FROM paises WHERE id_pais = $id_pais;");
	#Si no obtiene un resultado devuelve falso
	if($consulta_obtener_pais->num_rows != 1){
		return false;
		exit();
	}
	return $consulta_obtener_pais->fetch_array();
}
function obtener_fuente($conex,$id_pais,$id_dia){
	$consulta_obtener_fuente = $conex->query("SELECT fuente,casos_descartados FROM fuentes WHERE id_pais = $id_pais AND id_dia = $id_dia;");
	return $consulta_obtener_fuente->fetch_array();
}



function obtener_id_hoy($conex, $id_pais){
	$resultado_datos_pais = obtener_datos_pais($conex,$id_pais);
	$date = new DateTime();
	$date->modify($resultado_datos_pais['zona_horaria'].' hours');
	$hoy = $date->format('Y-m-d');
	$resultado_obterner_hoy =  obtener_id_dia_por_fecha($conex, $hoy);
	return $resultado_obterner_hoy['id_dia'];
}
function inicializar_datos($conex){
	#Pais por defecto Perú
	$id_pais = 1;
	if(isset($_GET['id_pais'])){
		if(validar_id_pais($conex, $_GET['id_pais'])){
			$id_pais = $_GET['id_pais'];
		}
	}
	#Dia por defecto Hoy
	$id_dia = obtener_id_hoy($conex, $id_pais);
	if(isset($_GET['id_dia'])){
		if(validar_id_dia($conex, $_GET['id_dia'], $id_pais)){
			$id_dia = $_GET['id_dia'];
		}
	}
	return ["id_pais"=>$id_pais, "id_dia"=>$id_dia];
}



function obtener_resumen_dia($conex, $id_pais, $id_dia){
	$consulta_obtener_resumen_dia = $conex->query("SELECT registros.id_departamento, departamentos.nombre, registros.infectados, registros.curados, registros.muertos FROM registros INNER JOIN departamentos ON registros.id_departamento = departamentos.id_departamento WHERE  registros.id_dia = $id_dia AND departamentos.id_pais = $id_pais ORDER BY registros.infectados DESC, departamentos.nombre ASC;");
	$infectados = 0;
	$curados = 0;
	$muertos = 0;
	$tabla = "<div class='table-responsive'>
	<table style='margin:0 auto;width:80%;' class='text-center table table-hover table-bordered table-sm'>
		<thead>
			<tr>
				<th rowspan='2'>DEPARTAMENTO</th>
				<th colspan='2'>INFECTADOS</th>
				<th colspan='2'>CURADOS</th>
				<th colspan='2'>FALLECIDOS</th>
			</tr>
			<tr>
				<th>N°</th>
				<th>RESPECTO AL DÍA ANTERIOR</th>
				<th>N°</th>
				<th>RESPECTO AL DÍA ANTERIOR</th>
				<th>N°</th>
				<th>RESPECTO AL DÍA ANTERIOR</th>
			</tr>
		</thead>
	<tbody>";
	while( $resultado_obtener_resumen_dia = $consulta_obtener_resumen_dia->fetch_array() ){



		#Consultar cantidad día anterior y pintarlo
		$datos_dia_anterior = obtener_dato_por_dia_deparamento($conex, $resultado_obtener_resumen_dia['id_departamento'], $id_dia-1);
		if($datos_dia_anterior['num_rows'] == 0){
			$estado_infectados = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
			$estado_curados = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
			$estado_muertos = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
		}else{

		
			$infectados_dia_anterior = $datos_dia_anterior['result']['infectados'];
			$muertos_dia_anterior = $datos_dia_anterior['result']['muertos'];
			$curados_dia_anterior = $datos_dia_anterior['result']['curados'];
			$diferencia_infectados = $resultado_obtener_resumen_dia['infectados'] - $infectados_dia_anterior;
			$diferencia_curados = $resultado_obtener_resumen_dia['curados'] - $curados_dia_anterior;
			$diferencia_muertos = $resultado_obtener_resumen_dia['muertos'] - $muertos_dia_anterior;


			
			$estado_infectados =$diferencia_infectados;
			if($infectados_dia_anterior == $resultado_obtener_resumen_dia['infectados']){
				$estado_infectados = 'Se mantiene';
			}elseif($diferencia_infectados > 0){
				$estado_infectados = '<strong class="text-danger">Aumentaron '.$estado_infectados.'</strong>';
			}elseif($diferencia_infectados < 0){
				$estado_infectados = 'Disminuyeron '.$estado_infectados;
			}


			$estado_curados =$diferencia_curados;
			if($curados_dia_anterior == $resultado_obtener_resumen_dia['curados']){
				$estado_curados = 'Se mantiene';
			}elseif($diferencia_curados > 0){
				$estado_curados = '<strong class="text-danger">Aumentaron '.$estado_curados.'</strong>';
			}elseif($diferencia_curados < 0){
				$estado_curados = 'Disminuyeron '.$estado_curados;
			}


			$estado_muertos =$diferencia_muertos;
			if($muertos_dia_anterior == $resultado_obtener_resumen_dia['muertos']){
				$estado_muertos = 'Se mantiene';
			}elseif($diferencia_muertos > 0){
				$estado_muertos = '<strong class="text-danger">Aumentaron '.$estado_muertos.'</strong>';
			}elseif($diferencia_muertos < 0){
				$estado_muertos = 'Disminuyeron '.$estado_muertos;
			}

		}
		



		$infectados = $infectados + $resultado_obtener_resumen_dia['infectados'];
		$curados = $curados + $resultado_obtener_resumen_dia['curados'];
		$muertos = $muertos + $resultado_obtener_resumen_dia['muertos'];
		$tabla .= "
		<tr>
			<td class='text-left'>$resultado_obtener_resumen_dia[nombre]</td>
			<td>$resultado_obtener_resumen_dia[infectados]</td>
			<td class=''>$estado_infectados</td>
			<td>$resultado_obtener_resumen_dia[curados]</td>
			<td class=''>$estado_curados</td>
			<td>$resultado_obtener_resumen_dia[muertos]</td>
			<td class=''>$estado_muertos</td>
		</tr>";
	}
	$tabla .= "</tbody></table></div>";
	return ["infectados"=>$infectados,"curados"=>$curados,"muertos"=>$muertos,"tabla"=>$tabla];
}








function obtener_fecha_actual($conex, $id_dia){
	$consulta_obtener_hoy = $conex->query("SELECT fecha FROM dias WHERE id_dia = '$id_dia';");
	return $consulta_obtener_hoy->fetch_array();
}

function obtener_id_dia_por_fecha($conex, $fecha){
	$consulta_obtener_hoy = $conex->query("SELECT id_dia FROM dias WHERE fecha = '$fecha';");
	return $consulta_obtener_hoy->fetch_array();
}
function obtener_dato_por_dia_deparamento($conex, $id_departamento, $id_dia){
	#Inicializar en 0
	$consulta_obtener_dato_por_dia_deparamento = $conex->query("SELECT infectados,curados,muertos FROM registros WHERE id_dia = $id_dia  AND id_departamento = $id_departamento;");
	return ["num_rows"=>$consulta_obtener_dato_por_dia_deparamento->num_rows,"result"=>$consulta_obtener_dato_por_dia_deparamento->fetch_array()];
}
function obtener_geojson($conex,$id_pais,$id_dia){
	#Esta funcion arma el geojson del país seleccionado
	#Si día es 0 significa que será hoy
	$consulta_obtener_departamentos = $conex->query("SELECT departamentos.id_departamento,departamentos.nombre,departamentos.tipo_coordenada,departamentos.coordenadas, IFNULL(registros.infectados, 0) AS infectados, IFNULL(registros.curados, 0) AS curados, IFNULL(registros.muertos, 0) AS muertos FROM departamentos LEFT JOIN registros ON departamentos.id_departamento = registros.id_departamento AND registros.id_dia = $id_dia WHERE departamentos.id_pais = $id_pais;");
	$departamentos = '';
	$resumen_dia = obtener_resumen_dia($conex, $id_pais, $id_dia);
	$resumen_total_infectados = $resumen_dia['infectados'];
	while($resultados_obtener_departamentos = $consulta_obtener_departamentos->fetch_array()){
		#Colores, verde = no infectados; rojo claro = infectados %menor; rojo oscuro = infectados %mayor
		if($resultados_obtener_departamentos['infectados'] == 0){
			$color = 'green';
		}else{ 
			#Si infectados está entre 0-35% = #FF0400;Si infectados está entre 36-70% = #AF0300;;Si infectados está entre 71-100% = #750200;
			if( $resultados_obtener_departamentos['infectados'] >= $resumen_total_infectados*0.71 ){
				$color = '#FF0000';
			}elseif ( $resultados_obtener_departamentos['infectados'] >= $resumen_total_infectados*0.36 ) {
				$color = '#FF1919';
			}else{
				$color = '#FF3232';
			}
		}
		#feature = departamento
		$departamentos .= '{"type":"Feature", "properties":{"id_departamento":'.$resultados_obtener_departamentos['id_departamento'].', "nombre_departamento":"'.ucwords(mb_strtolower($resultados_obtener_departamentos['nombre'])).'", "infectados":'.$resultados_obtener_departamentos['infectados'].', "curados":'.$resultados_obtener_departamentos['curados'].', "muertos":'.$resultados_obtener_departamentos['muertos'].', "color":"'.$color.'"}, "geometry": {"type":"'.$resultados_obtener_departamentos['tipo_coordenada'].'", "coordinates": '.$resultados_obtener_departamentos['coordenadas'].'}},';
	}
	$departamentos = trim($departamentos, ',');
	$geojson = '{"type": "FeatureCollection", "features":[';
	$geojson .= $departamentos;
	$geojson .= ']}';
	return $geojson;
}
?>