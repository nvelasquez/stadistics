<?php
	include("implement.php");
	include("libreria/engine.php");
?>

<script>	

		function crear(){
			$.get("crearVentana.php?municipios",function(data){
				$('#tablas').append(data);
			});
		}			
		function cambio(id){
			$.get('funcion/cambio.php?id='+id,function(data){
				$('#municipios').html(data);
				completado(document.getElementById("municipios").value);
			});
		}
		function getInfo(name1,name2,name3,name4){
			$.get('tablaresult.php?busqueda='+name1+'&provincia='+name2+'&municipio='+name3+'&multigrado='+name4,function(data){
					
					$('#tablaInfo').html(data);
			});
		}
		$("#grafico").fadeOut();
</script>
<div id='wrapper'>
  <div class="row">
  <script>
</script>
    <div class="large-12 columns">
 
    <!-- Content Slider -->
 
      <div class="row">
        <div class="large-12">
			  
			  <div id="tablas"  class="large-12 small-12 columns " >
				
			  	<!--<a onclick="if(!document.getElementById('idd')){crear()}else{}"><input type='button' value='Agregar' /></a>-->
					
					<form  id='formulario' action="" method="post" class="large-6 small-6 columns">
					<h6>Busqueda personalizada</h6>
					<table class='noborder'>
				
					<tr>
						
						<?php
							$input = new tInput();
							$input->crearSelect("Nombre","IdProvincia","ProvinciasJCE","provincias","Provincias");
						?>
						
					
					
						
							<label>Municipios</label>
							<select placeholder='Municipios por provincia' id='municipios' name='municipios' onchange='completado(this.value);'>
								<option  value='nodefinido'>No definido</option>
							</select>
						
						
						<td>
							
							
							
							<tr>
							<td>
							<label>Multigrados</label>
							<select class="input" placeholder='multigrado' id='multigrado' name='multigrado'>
								<option  value=''>todas</option>
								<option  value='no multigrado'>No multigrado</option>
								<option  value='multigrado'>multigrado</option>
							</select>
						</td>
						<td>
							<label>Privadas o publicas</label>
							<select placeholder='Privadas o publicas' id='tipo' name='tandas'>
								<option  value=''>Todas</option>
								<option  value='privadas'>Privadas</option>
								<option  value='publicas'>Publicas</option>
							</select>
								
							</td>
						</tr>
						</td>
						</tr>
						<tr>
						<td>
							<label>Busqueda</label>
							<input  placeholder='busqueda' value='' type='text' name='busqueda' id='busqueda' />						

						</td>
					</tr>
					</tr>
					<tr>
						<td><input type='button' onclick='getInfo(document.getElementById("busqueda").value,document.getElementById("provincias").value,document.getElementById("municipios").value,document.getElementById("multigrado").value)' value='buscar' /></td>
					</tr>

					</table>

					</form>
					<form   id='formulario' action="" method="post" class="large-6 small-6 columns">
						<h6>Comparaciones</h6>
						<table class="noborder">
							
							<td>
								<label>Publicas o privadas</label>
								Publicas <input type='radio' name='publipriva' value='publicas' />
								Privadas <input type='radio' name='publipriva' value='privadas' />
							</td>
							
							
							<td>
								<label>Centros por municipios o provincias</label>
								Publicas <input type='radio' name='publipriva' value='publicas' />
							</td>
						
						</table>
					</form>
					<script>

					</script>
				</div>
         
			  
			  <div class="large-8 small-12 columns hide-for-small ">
					<style>
						

						.nave li{
							margin-left: -40px;
							display: inline-block;
							height: 28px;
							text-align: center;
							color:rgb(177, 177, 177);
							width: 70px;
							cursor: pointer;
							background: rgb(249, 252, 255);
							border: solid 1px rgb(209, 209, 209);
							box-shadow: 1px 1px 3px rgb(231, 227, 227);
							
						}
						.nave li:hover {
							background:rgb(23, 73, 230);
							color: white;
						}

						.nave {
							margin-left:40px;
						}
						.divider {
							border:none !important;
							background:none !important;
							box-shadow:none !important;
						}
					</style>
					
			  </div>
			  <div class="large-12 small-12 columns" id="mapa">
					<div id='grafico' class='large-5 columns'>
						
					</div>
					<div id='divta' style="border: solid 1px rgb(226, 225, 225);height:450px;overflow:scroll;">
					
					<table style="overflow:scroll;margin-left;10px;background:rgb(243, 243, 243);box-shadow: 1px 1px 3px rgb(231, 227, 227); "  class='large-7'>
							
					
						<tr id='tablaInfo'>

						</tr>	
					</table>
					</div>
					
			  </div>
             
            
 
      </div>
      <script>
	
		
			function emergir(name,tabla){
				
				$.get('librerias/emergir.php?name='+name+'&&tabla='+tabla,function(data){
					
					$('#wrapper').append(data);
				});
				
			}

			function desemerger(name){
				$('#'+name).remove();
			}

			function borrarBusqueda(name){
				$("#"+name).remove();
			}
			function completado(name){
				$.get('funcion/cambio.php?completado='+name,function(data){
					
					$("#scripte").html(data);
				});
			}

				var jsons = { type: 'pie', name: 'estudiantes por municipio', data:[['DISTRITO NACIONAL',344093],['SAN CRISTOBAL',344093],['BANI',344093],['BAYAGUANA',344093],['YAMASA',344093],['MONTE PLATA',344093],['AZUA',344093],['LAS MATAS DE FARFAN',344093],['SAN JUAN DE LA MAGUANA',344093],['SAN JOSE DE OCOA',344093],['EL CERCADO',344093],['BANICA',344093],['COMENDADOR',344093],['PADRE LAS CASAS',344093],['BARAHONA',344093],['CABRAL',344093],['DUVERGE',344093],['ENRIQUILLO',344093],['NEYBA',344093],['SAN PEDRO DE MACORIS',344093],['LOS LLANOS',344093],['EL SEIBO',344093],['LA ROMANA',344093],['HATO MAYOR',344093],['HIGUEY',344093],['MICHES',344093],['RAMON SANTANA',344093],['SANTIAGO DE LOS CABALLEROS',344093],['TAMBORIL',344093],['ESPERANZA',344093],['MAO',344093],['JANICO',344093],['SAN JOSE DE LAS MATAS',344093],['PUERTO PLATA',344093],['IMBERT',344093],['ALTAMIRA',344093],['LUPERON',344093],['MONTECRISTI',344093],['MONCION',344093],['RESTAURACION',344093],['DAJABON',344093],['GUAYUBIN',344093],['SAN IGNACIO DE SABANETA',344093],['LA VEGA',344093],['BONAO',344093],['COTUI',344093],['JARABACOA',344093],['VILLA TAPIA',344093],['CEVICOS',344093],['CONSTANZA',344093],['MOCA',344093],['SALCEDO',344093],['SAN FRANCISCO DE MACORIS',344093],['PIMENTEL',344093],['VILLA RIVA',344093],['CASTILLO',344093],['CABRERA',344093],['GASPAR HERNANDEZ',344093],['EUGENIO MARIA DE HOSTOS',344093],['TENARES',344093],['SAMANA',344093],['SANCHEZ',344093],['SABANA DE LA MAR',344093],['VILLA ALTAGRACIA',344093],['PEDERNALES',344093],['LA DESCUBIERTA',344093],['NAGUA',344093],['VILLA VASQUEZ',344093],['LOMA DE CABRERA',344093],['PEDRO SANTANA',344093],['HONDO VALLE',344093],['TAMAYO',344093],['JIMANI',344093],['VILLA JARAGUA',344093],['VICENTE NOBLE',344093],['PARAISO',344093],['RIO SAN JUAN',344093],['YAGUATE',344093],['SABANA GRANDE DE PALENQUE',344093],['NIZAO',344093],['SAN RAFAEL DEL YUMA',344093],['PEPILLO SALCEDO',344093],['FANTINO',344093],['CAYETANO GERMOSEN',344093],['JOSE CONTRERAS (DM)',344093],['SABANA GRANDE DE BOYA',344093],['OVIEDO',344093],['LAGUNA SALADA',344093],['BAJOS DE HAINA',344093],['VILLA GONZALEZ',344093],['LICEY AL MEDIO',344093],['VILLA BISONO -NAVARRETE-',344093],['SOSUA',344093],['POSTRER RIO',344093],['EL VALLE',344093],['CASTAÑUELAS',344093],['LOS HIDALGOS',344093],['GUAYMATE',344093],['CAMBITA GARABITOS',344093],['GUAYABAL',344093],['PERALTA',344093],['SABANA YEGUA',344093],['VALLEJUELO',344093],['BOHECHIO',344093],['EL LLANO',344093],['POLO',344093],['LOS RIOS ',344093],['GALVAN',344093],['MELLA',344093],['PARTIDO',344093],['VILLA LOS ALMACIGOS',344093],['LAS MATAS DE SANTA CRUZ',344093],['MAIMON',344093],['ARENOSO',344093],['GUANANICO',344093],['VILLA ISABELA',344093],['JIMA ABAJO',344093],['PIEDRA BLANCA',344093],['LA CUEVA (DM)',344093],['LAS YAYAS DE VIAJAMA',344093],['TABARA ARRIBA',344093],['UVILLA (DM)',344093],['CRISTOBAL',344093],['JUAN DE HERRERA',344093],['ESTEBANIA',344093],['JAMAO AL NORTE',344093],['LAS TERRENAS',344093],['LAS CHARCAS',344093],['EL FACTOR',344093],['LAS SALINAS',344093],['CONSUELO',344093],['LOS CACAOS',344093],['SAN GREGORIO DE NIGUA',344093],['LAS GUARANAS',344093],['QUISQUEYA',344093],['SABANA IGLESIA',344093],['SAN VICTOR (DM)',344093],['SABANA LARGA',344093],['RANCHO ARRIBA',344093],['VILLA LA MATA',344093],['LA CIENAGA',344093],['SANTO DOMINGO ESTE',344093],['SANTO DOMINGO OESTE',344093],['SANTO DOMINGO NORTE',344093],['BOCA CHICA',344093],['SAN ANTONIO DE GUERRA',344093],['PEDRO BRAND',344093],] }
			
</script>
	<div id='scripte'></div>
	<div id='jsonsdiv'>
		
	</div>
    </div>
			

 
    <!-- End Content Slider -->
 
 
 
      
    <!-- End Content -->
 
 
