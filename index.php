<?php
#Recibe GET de País y de Fecha, si algo está mal por defecto id_pais = 1 y id_dia = HOY
include("main.php");
$conex = conex();
$datos_array = inicializar_datos($conex);
$id_pais = $datos_array['id_pais'];
$id_dia = $datos_array['id_dia'];
$id_dia = no_manana($conex, $id_pais, $id_dia);
$url_geojson = 'geojson.php?id_pais='.$id_pais.'&id_dia='.$id_dia;
$datos_pais = obtener_datos_pais($conex,$id_pais);
$pais_nombre = ucwords(mb_strtolower($datos_pais['nombre']));
$pais_latitud = $datos_pais['latitud'];
$pais_longitud = $datos_pais['longitud'];
$pais_zoom_mapa = $datos_pais['zoom_mapa'];



$resumen_dia = obtener_resumen_dia($conex, $id_pais, $id_dia);
$resumen_total_infectados = $resumen_dia['infectados'];
$resumen_total_curados = $resumen_dia['curados'];
$resumen_total_muertos = $resumen_dia['muertos'];
$resumen_tabla = $resumen_dia['tabla'];
$fuentes = obtener_fuente($conex,$id_pais,$id_dia);
$fuente = $fuentes['fuente'];
$today = obtener_fecha_actual($conex, $id_dia);
$today = $today['fecha'];
if($fuente != ''){
  $fuente = "<tr><th colspan='3' class='text-cente'><a class='btn btn-primary' href='$fuente' role='button' target='_blank'>Fuente</a></th></tr>";
}
$casos_descartados = $fuentes['casos_descartados'];
if($casos_descartados == ''){
  $casos_descartados =0;
}
$total = $casos_descartados+$resumen_total_infectados+$resumen_total_curados+$resumen_total_muertos;
$more_visibility='';
if($total == 0){
  $more_visibility = 'display: none;';
}







$resumen_dia_ayer = obtener_resumen_dia($conex, $id_pais, $id_dia-1);
$resumen_total_infectados_ayer = $resumen_dia_ayer['infectados'];
$resumen_total_curados_ayer = $resumen_dia_ayer['curados'];
$resumen_total_muertos_ayer = $resumen_dia_ayer['muertos'];
$fuentes_ayer = obtener_fuente($conex,$id_pais,$id_dia-1);
$casos_descartados_ayer = $fuentes_ayer['casos_descartados'];
$total_ayer = $casos_descartados_ayer+$resumen_total_infectados_ayer+$resumen_total_curados_ayer+$resumen_total_muertos_ayer;

if($total_ayer == 0){
  $estado_descartados = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
  $estado_infectados = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
  $estado_curados = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
  $estado_muertos = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
  $estado_total = "<strong class='text-danger'>Nuevo(s) Caso(s).</strong>";
}else{

  $diferencia_descartados = $casos_descartados - $casos_descartados_ayer;
  $diferencia_infectados = $resumen_total_infectados - $resumen_total_infectados_ayer;
  $diferencia_curados = $resumen_total_curados - $resumen_total_curados_ayer;
  $diferencia_muertos = $resumen_total_muertos - $resumen_total_muertos_ayer;
  $diferencia_total = $total - $total_ayer;



  $estado_descartados =$diferencia_descartados;
  if($casos_descartados_ayer == $casos_descartados){
    $estado_descartados = 'Se mantiene';
  }elseif($diferencia_descartados > 0){
    $estado_descartados = '<strong class="text-danger">Aumentaron '.$estado_descartados.'</strong>';
  }elseif($diferencia_descartados < 0){
    $estado_descartados = 'Disminuyeron '.$estado_descartados;
  }



  $estado_infectados =$diferencia_infectados;
  if($resumen_total_infectados_ayer == $resumen_total_infectados){
    $estado_infectados = 'Se mantiene';
  }elseif($diferencia_infectados > 0){
    $estado_infectados = '<strong class="text-danger">Aumentaron '.$estado_infectados.'</strong>';
  }elseif($diferencia_infectados < 0){
    $estado_infectados = 'Disminuyeron '.$estado_infectados;
  }

  $estado_curados =$diferencia_curados;
  if($resumen_total_curados_ayer == $resumen_total_curados){
    $estado_curados = 'Se mantiene';
  }elseif($diferencia_curados > 0){
    $estado_curados = '<strong class="text-danger">Aumentaron '.$estado_curados.'</strong>';
  }elseif($diferencia_curados < 0){
    $estado_curados = 'Disminuyeron '.$estado_curados;
  }

  $estado_muertos =$diferencia_muertos;
  if($resumen_total_muertos_ayer == $resumen_total_muertos){
    $estado_muertos = 'Se mantiene';
  }elseif($diferencia_muertos > 0){
    $estado_muertos = '<strong class="text-danger">Aumentaron '.$estado_muertos.'</strong>';
  }elseif($diferencia_muertos < 0){
    $estado_muertos = 'Disminuyeron '.$estado_muertos;
  }

  $estado_total = $diferencia_total;
  if($total_ayer == $total){
    $estado_total = 'Se mantiene';
  }elseif($diferencia_total > 0){
    $estado_total = '<strong class="text-danger">Aumentaron '.$estado_total.'</strong>';
  }elseif($diferencia_total < 0){
    $estado_total = 'Disminuyeron '.$estado_total;
  }

}









$paises = obtener_paises($conex, $id_pais, $id_dia);
$fechas = obtener_fechas($conex, $id_pais, $id_dia, 'index');
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Coronavirus en <?php print($pais_nombre);?></title>
    <link href="virus.ico" rel="shortcut icon" type="image/x-icon">
    <!--SEO-->
    <meta name="description" content="Información de la propagación del coronavirus.">
    <meta name=keywords content="coronavirus, infectados, casos de coronavirus, covid-19, covid 19">
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <!--For Facebook-->
    <meta property="og:url"           content="https://coronavirus.ideb.tech" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="Coronavirus en <?php print($pais_nombre);?>" />
    <meta property="og:description"   content="Información de la propagación del coronavirus." />
    <meta property="og:image"         content="coronavirus.jpg" />
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
      #map {
        height: 100%;
      }
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #facebook{
        top: 50px;
        right: 15px;
        position: absolute;
      }
      #twitter{
        top: 80px;
        right: 15px;
        position: absolute;
      }
      #acerda_de{
        top: 10px;
        right: 15px;
        position: absolute;
      }

      #paises{
        background-color: rgba(255,255,255,0.8);
        border-radius: 10px;
        padding: 10px 15px;
        top: 120px;
        left: 10px;
        position: absolute;
      }

      #fechas{
      	background-color: rgba(255,255,255,0.8);
      	border-radius: 10px;
      	padding: 10px 15px;
        top: 10px;
        left: 10px;
        position: absolute;
      }
      #info-box {
        background-color: rgba(255,255,255,0.8);
        border: 1px solid black;
        border-radius: 10px;
        right: 10px;
        bottom: 30px;
        padding: 10px;
        position: absolute;
        min-width: 200px;
      }
  	  #into-info-box{
        <?php print($more_visibility);?>
  	  	position: absolute;
  	  	right: 10px;
  	  	bottom: 140px;
  	  	font-size: 200%;
  	  	color: #1969FF;
  	  }
      #contenedor-tabla {
          display: flex;
          align-items: center;
      }
      #contenido-tabla {
              margin: 0 auto; /* requerido para alineación horizontal */
      }
      table, tr, td, th{
        border: 1px solid black !important;
      }
    </style>
    <script src="https://kit.fontawesome.com/bec2db9e54.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
  </head>
  <body>
    <div id="map"></div>
    <script>
      var map;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: <?php print($pais_zoom_mapa);?>,
          center: {lat: <?php print($pais_latitud);?>, lng: <?php print($pais_longitud);?>},
          fullscreenControl: false,
          mapTypeControl: false,
          streetViewControl: false,
          zoomControl: false
        });
        map.data.loadGeoJson('<?php print($url_geojson);?>');
        map.data.setStyle(function(feature) {
    		var color = feature.getProperty('color');
    		return {
    			fillOpacity:0.7,
    			fillColor: color,
    			strokeWeight: 1,
    		};
    	});


    	map.data.addListener('mouseover', function(event) {
        map.data.revertStyle();
        document.getElementById('ubicacion').innerHTML = event.feature.getProperty('nombre_departamento');
        document.getElementById('infectados').innerHTML = event.feature.getProperty('infectados');
        document.getElementById('curados').innerHTML = event.feature.getProperty('curados');
        document.getElementById('muertos').innerHTML = event.feature.getProperty('muertos');
        document.getElementById('more').innerHTML = "";	
    		map.data.overrideStyle(event.feature, {strokeWeight: 3});
    	});
    		map.data.addListener('mouseout', function(event) {
    		map.data.revertStyle();
    		document.getElementById('ubicacion').innerHTML = '<?php print($pais_nombre);?>';
        document.getElementById('infectados').innerHTML = '<?php print($resumen_total_infectados);?>';
        document.getElementById('curados').innerHTML = '<?php print($resumen_total_curados);?>';
        document.getElementById('muertos').innerHTML = '<?php print($resumen_total_muertos);?>';
        document.getElementById('more').innerHTML = '<i class="fa fa-plus-circle"></i>';
    	});

      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPnO-jnAYgE-m51aW-At2DMJXveSks3mI&callback=initMap">
    </script>
	




	   <!--Bootstrap-->
	   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<!--Cookies-->
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
    <script>
    window.addEventListener("load", function(){
    window.cookieconsent.initialise({
      "palette": {
      "popup": {
        "background": "rgba(0,0,0,0.8)"
      },
      "button": {
        "background": "#f1d600"
      }
      },
      "theme": "classic",
      "content": {
      "message": "Usamos cookies para que obtenga una mejor experiencia en nuestro sitio web, al navegar por ella usted las acepta.",
      "dismiss": "Entendido",
      "link": "Conozca más...",
      "href": "https://ideb.tech/politicas.html#cookies"
      }
    })});
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-160642964-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-160642964-1');
    </script>

	<!-- Load Facebook SDK for JavaScript -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v6.0&appId=2286449781641184&autoLogAppEvents=1"></script>
    <!-- Your share button code -->
    <div id="facebook" class="fb-share-button" data-href="https://coronavirus.ideb.tech" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fcoronavirus.ideb.tech%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Compartir</a></div>
    <!--Twitter-->
	<div id="twitter"><a href="https://twitter.com/intent/tweet?text=#Coronavirus%0A&url=https%3A%2F%2Fcoronavirus.ideb.tech" class="twitter-hashtag-button" data-show-count="false"></a></div><script async src="https://platform.twitter.com/widgets.js"></script>
	<a id="acerda_de" class="btn btn-secondary" href="acerca_de.html" target="_blank">Acerca de</a>
	
  <!--Seleccionar Paises-->
  <?php print($paises);?>
	<!--Seleccionar Fechas-->
	<?php print($fechas);?>



	<div id="info-box">
        <p id="ubicacion" class="text-center h1"><?php print($pais_nombre);?></p>
        <ul class="list-group">
		  <li class="d-flex justify-content-between">
		    Infectados: <span id="infectados"><?php print($resumen_total_infectados);?></span>
		  </li>
		  <li class="d-flex justify-content-between">
		    Curados: <span id="curados"><?php print($resumen_total_curados);?></span>
		  </li>
		  <li class="d-flex justify-content-between">
		    Fallecidos: <span id="muertos"><?php print($resumen_total_muertos);?></span>
		  </li>
		</ul>
    </div>
	<div id="into-info-box">
		<a id="more" type="button" data-toggle="modal" data-target=".bd-example-modal-xl"><i class="fa fa-plus-circle"></i></a>
	</div>












	<!-- Extra large modal -->
	<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h2 class="modal-title">Coronavirus en <?php print($pais_nombre);?></h2>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div><br>
        <h1 class="text-center">REPORTE DE FECHA: <?php print($today);?></h1>
        <div class="container">
          <div class="row">

            <div id="contenedor-tabla" class="col-sm">
              <div id="contenido-tabla" class='table-responsive'>
                <table style='margin:0 auto;width:80%;' class='text-center table table-hover table-bordered table-sm'>
                  <thead>
                    <tr>
                      <th>EVALUADOS</th>
                      <th>CANTIDAD</th>
                      <th>RESPECTO AL DÍA ANTERIOR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Descartados</td>
                      <td><?php print($casos_descartados);?></td>
                      <td><?php print($estado_descartados);?></td>
                    </tr>
                    <tr>
                      <td class="text-right">Infectados</td>
                      <td><?php print($resumen_total_infectados);?></td>
                      <td><?php print($estado_infectados);?></td>
                    </tr>
                    <tr>
                      <td class="text-right">Curados</td>
                      <td><?php print($resumen_total_curados);?></td>
                      <td><?php print($estado_curados);?></td>
                    </tr>
                    <tr>
                      <td class="text-right">Fallecidos</td>
                      <td><?php print($resumen_total_muertos);?></td>
                      <td><?php print($estado_muertos);?></td>
                    </tr>
                    <tr>
                      <th class="text-right">TOTAL</th>
                      <th><?php print($total);?></th>
                      <th><?php print($estado_total);?></th>
                    </tr>
                    <?php print($fuente);?>
                  </tbody>
                </table>
              </div>

              


            </div>
            <div class="col-sm">
              <canvas id="oilChart" width="400" height="400"></canvas>
            </div>
          </div>
        </div>

        


		    
  
  		  <br>
	      <div class="container">
	      	<?php print($resumen_tabla);?>
	      </div>
        
		    

      
        <br>
        <br>
        <br>
	    </div>
	  </div>
	</div>


  <script>
    var oilCanvas = document.getElementById("oilChart");
    var oilData = {
        labels: [
            "Descartados",
            "Infectados",
            "Curados",
            "Fallecidos"
        ],
        datasets: [
            {
                data: [<?php print($casos_descartados);?>, <?php print($resumen_total_infectados);?>, <?php print($resumen_total_curados);?>, <?php print($resumen_total_muertos);?>],
                backgroundColor: [
                    "#32FF00",
                    "#FF0000",
                    "#FFF700",
                    "#000000"
                ]
            }]
    };
    var pieChart = new Chart(oilCanvas, {
      type: 'pie',
      data: oilData
    });
  </script>











  </body>
</html>