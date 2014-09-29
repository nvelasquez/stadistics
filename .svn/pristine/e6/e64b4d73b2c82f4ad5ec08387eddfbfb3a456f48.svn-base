
		<?php
						include('librerias/motor.php');


						$bolean = true;
						
							$provinciasn = $_GET['provincia'];
							$Municipiosn=$_GET['municipio'];
							$busqueda = $_GET['busqueda'];
							$radiomulti = $_GET['multigrado'];

							$multigra = " and (c.EsMultigrado = 1)";
							$multigra = " and (c.EsMultigrado = 0)";
							
							if($provinciasn == 0){
								echo '<h6>No ha seleccionado la provincia</h6>';
								exit();
							}
							else{

							if($busqueda == ""){

									
										$query= "select *  from Centros as c where (c.IdDistritoMunicipal = '$Municipiosn')";

										
										if($radiomulti == 'multigrado'){
											$query = $query . $multigra;
										}
										if($radiomulti == 'no multigrado'){
											$query = $query . $multigra0;
										}
									
								}
								else {
									
									$query= "select *  from Centros as c where c.nombre = '$busqueda' and (c.IdDistritoMunicipal = '$Municipiosn')";
									if(!$radiomulti == ''){
											$query = $query . $multigra;
										}
								}


								$query1 = "SELECT COLUMN_NAME
											FROM information_schema.COLUMNS
											
											where TABLE_NAME = 'centros'
								 ";
								$pedido1 = mssql_query($query1);
								echo '<tr>';
								while($fila1 = mssql_fetch_assoc($pedido1)){

									foreach($fila1 as $value1){
										echo "<td>".$value1."</td>";
									}
									
									
								}
								echo '</tr>';
	
						$pedido = mssql_query($query);
						
						if(mssql_num_rows($pedido) < 0){
					echo "<h3>No se han encontrado datos</h3>";
							
						
					}
					else {

					while($fila=mssql_fetch_row($pedido)){
						$bolean = true;
						echo '<tr>';
							

								$clase='ra';
									foreach($fila as $value){
									echo "<td style='font-size:11px;' onclick='if(!document.getElementById(\"{$nombre}\")){emergir(\"{$nombre}\",\"Centros\");}else{}' class='{$clase}'>".$value."</td>";
									$bolean=false;
							}
							
						echo '</tr>';
						}
						mssql_free_result($pedido);

					}
				}
				

						
						?>