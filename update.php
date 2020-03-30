<?php
#Este archivo es el frontend para actualizar los datos, recibe id_pais y id_dia. OJO SÓLO PARAACTUALIZAR
include("main.php");
$conex = conex();

$datos_array = inicializar_datos($conex);
$id_pais = $datos_array['id_pais'];
$id_dia = $datos_array['id_dia'];
$id_dia = no_manana($conex, $id_pais, $id_dia);


$datos_pais = obtener_datos_pais($conex,$id_pais);
$pais_nombre = ucwords(mb_strtolower($datos_pais['nombre']));
$today = obtener_fecha_actual($conex, $id_dia);
$consulta_obtener_departamentos = $conex->query("SELECT departamentos.id_departamento, departamentos.nombre, IFNULL(registros.infectados, 0) AS infectados, IFNULL(registros.curados, 0) AS curados, IFNULL(registros.muertos, 0) AS muertos FROM departamentos LEFT JOIN registros ON departamentos.id_departamento = registros.id_departamento AND registros.id_dia = $id_dia WHERE id_pais = $id_pais ORDER BY infectados DESC, departamentos.nombre ASC;");
$rows_tabla = '';
$arreglo = '';
$n=1;
while($resultado_obtener_departamentos = $consulta_obtener_departamentos->fetch_array()){
	$arreglo .= "$resultado_obtener_departamentos[id_departamento],";
	$rows_tabla .= "
		<tr>
							<td class='text-left'>$resultado_obtener_departamentos[nombre]</td>
							<td><input style='width:50px;text-align:center;' type='number' name='i$resultado_obtener_departamentos[id_departamento]' value='$resultado_obtener_departamentos[infectados]' required></td>
							<td><input style='width:50px;text-align:center;' type='number' name='c$resultado_obtener_departamentos[id_departamento]' value='$resultado_obtener_departamentos[curados]' required></td>
							<td><input style='width:50px;text-align:center;' type='number' name='m$resultado_obtener_departamentos[id_departamento]' value='$resultado_obtener_departamentos[muertos]' required></td>
						</tr>
	";
}
$fuentes = obtener_fuente($conex,$id_pais,$id_dia);
$fuente = $fuentes['fuente'];
$casos_descartados = $fuentes['casos_descartados'];
$fechas = obtener_fechas($conex, $id_pais, $id_dia, 'update');
$paises = obtener_paises($conex, $id_pais, $id_dia);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Actualizar datos</title>
	<!--Bootstrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<h1 class="text-center">Actualizar datos de <?php print($pais_nombre);?> para el día <?php print($today['fecha']);?></h1>
		<?php print($fechas);?>
  		<?php print($paises);?>
		<form action="update_exec.php" method="POST">
			<input type="hidden" name="id_pais" value="<?php print($id_pais);?>">
			<input type="hidden" name="id_dia" value="<?php print($id_dia);?>">
			<input type="hidden" name="arreglo" value="<?php print($arreglo);?>">
			<div class='table-responsive'>
				<table class='text-center table table-hover table-bordered table-sm'>
					<thead>
						<tr>
							<th>DEPARTAMENTO</th>
							<th>INFECTADOS</th>
							<th>CURADOS</th>
							<th>FALLECIDOS</th>
						</tr>
					</thead>
					<tbody>
						<?php print($rows_tabla);?>




					</tbody>
				</table>
			</div>
			<div class="form-group">
			    <label >Casos descartados:</label>
			    <input type="text" class="form-control" name="casos_descartados" value="<?php print($casos_descartados);?>">
			</div>
			<div class="form-group">
			    <label >Fuente:</label>
			    <input type="text" class="form-control" name="fuente" value="<?php print($fuente);?>">
			</div>
			<div class="form-group">
			    <label >Usuario:</label>
			    <input type="text" class="form-control" name="usuario">
			</div>
			<div class="form-group">
			    <label >Contraseña:</label>
			    <input type="password" class="form-control" name="password">
			</div>
			<button type="submit" class="btn btn-primary">Enviar</button>
		</form>
		<br>
		<br>
	</div>
	<!--Bootstrap-->
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>