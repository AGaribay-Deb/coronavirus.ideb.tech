<?php
include("main.php");
$conex = conex();
if(!validar_id_pais($conex, $_POST['id_pais']) ){
	header("Location: update.php?msg=Error en id_pais.");
	exit();
}
if(!validar_id_dia_update($conex, $_POST['id_dia'], $_POST['id_pais'])){
	header("Location: update.php?msg=Error en id_dia.");
	exit();
}
if(preg_match("/^[0-9]{1,7}$/",$_POST['casos_descartados'])!=1){
	header("Location: update.php?msg=Error en casos_descartados.");
	exit();
}
if (preg_match("/^https:\/\/[a-zAZ0-9\-_]*[\.]*[a-zAZ0-9\-_]+\.[a-zAZ0-9\-_]+\/.*$/",$_POST['fuente'])!=1) {
    header("Location: update.php?msg=Error en fuente.");
	exit();
}
if(preg_match("/^[a-zA-Z0-9]{5,20}$/",$_POST['usuario'])!=1){
	header("Location: update.php?msg=Error en usuario.");
	exit();
}
if(preg_match("/^[a-zA-Z0-9]{15,20}$/",$_POST['password'])!=1){
	header("Location: update.php?msg=Error en password.");
	exit();
}
if(preg_match("/^([0-9]{1,3},)+$/",$_POST['arreglo'])!=1){
	header("Location: update.php?msg=Error en arreglo.");
	exit();
}
$_POST['arreglo'] = trim($_POST['arreglo'],",");
$arreglo_in = $_POST['arreglo'];
$_POST['arreglo'] = explode(",", $_POST['arreglo']);
$n=0;
foreach ($_POST['arreglo'] as $id_departamento) {
	if(preg_match("/^[0-9]{1,4}$/",$_POST['i'.$id_departamento])!=1){
		header("Location: update.php?msg=Error en i".$id_departamento.".");
		exit();
	}
	if(preg_match("/^[0-9]{1,4}$/",$_POST['c'.$id_departamento])!=1){
		header("Location: update.php?msg=Error en c".$id_departamento.".");
		exit();
	}
	if(preg_match("/^[0-9]{1,4}$/",$_POST['m'.$id_departamento])!=1){
		header("Location: update.php?msg=Error en m".$id_departamento.".");
		exit();
	}
	$n++;
}
#Validar id_departamento pertenezca a id_pais
$consulta_validar_id_departamento = $conex->query("SELECT COUNT(*) FROM departamentos WHERE id_pais = $_POST[id_pais] AND id_departamento IN ($arreglo_in);");
$resultado_validar_id_departamento = $consulta_validar_id_departamento->fetch_array();
if($resultado_validar_id_departamento[0] != $n){
	header("Location: update.php?msg=Error en id_departamento(no pertenece al país).");
	exit();
}
#Validar login y Validar permiso de usuario por pais y por dia
$consulta_validar_permiso_login = $conex->query("SELECT usuarios.id_usuario, usuarios.password FROM permisos INNER JOIN usuarios ON permisos.id_usuario = usuarios.id_usuario WHERE usuarios.usuario = '$_POST[usuario]' AND permisos.id_pais = $_POST[id_pais];");
if($consulta_validar_permiso_login->num_rows != 1){
	header("Location: update.php?msg=Error en login(usuario incorrecto o permisos insuficientes).");
	exit();
}
$resultado_validar_permiso_login = $consulta_validar_permiso_login->fetch_array();
if($resultado_validar_permiso_login['password'] !== $_POST['password']){
	header("Location: update.php?msg=Error en login(contraseña incorrecta).");
	exit();
}


#Eliminar registros de la tabla registros con el mismo id_pais y id_dia
$conex->query("DELETE FROM fuentes WHERE id_dia = $_POST[id_dia] AND id_pais = $_POST[id_pais];");
$conex->query("DELETE r FROM registros r INNER JOIN departamentos d ON r.id_departamento = d.id_departamento WHERE d.id_pais = $_POST[id_pais] AND r.id_dia = $_POST[id_dia];");
#Cargar los nuevos datos
$conex->query("INSERT INTO fuentes(id_fuente, id_dia, id_pais, id_usuario, fuente, casos_descartados) VALUES (NULL,$_POST[id_dia],$_POST[id_pais],$resultado_validar_permiso_login[id_usuario],'$_POST[fuente]',$_POST[casos_descartados]);");
foreach ($_POST['arreglo'] as $id_departamento) {
	if($_POST['i'.$id_departamento] == 0 && $_POST['c'.$id_departamento] == 0 && $_POST['m'.$id_departamento] == 0){
		#No hacer nada
	}else{
		#Cargar
		$conex->query("INSERT INTO registros(id_registro, id_dia, id_departamento, id_usuario, infectados, curados, muertos) VALUES (NULL,$_POST[id_dia],$id_departamento,$resultado_validar_permiso_login[id_usuario],".$_POST['i'.$id_departamento].",".$_POST['c'.$id_departamento].",".$_POST['m'.$id_departamento].");");
	}
}
#Redirigir a la página principal con los nuevos datos cargados
header("Location: index.php?id_pais=$_POST[id_pais]&id_dia=$_POST[id_dia]&msg=Datos cargados correctamente.");
exit();
#Eliminar funciones que no esté usando y documentar
?>