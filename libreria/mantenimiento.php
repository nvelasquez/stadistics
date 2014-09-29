<?php
	class mantenimiento
	{
		var $tabla;
		var $nFields;
		var $primario;
		var $titulo; //para ponerle el titulo que sale arriba del mantenimiento
		var $anchoencontroles;
		
		
		var $campos;
		var $ctitulos;//titulos de los campos
		var $tiposdedatos;
		var $flags;
		var $visibleEnBusqueda; //campos que se veran en la busqueda
		var $camposEnEdicion; //campos que se veran en la edicion
		
		var $camposBusqueda;
		var $camposEdicion;
		
		var $paginaActual;
	
		var $sqlMaestro;
		
		//Para el manejo de grupos.
		var $usarGrupos;
		var $grupos;
		
		var $tipoPrimario;
		var $puedesBorrar;
		
		function __construct($tabla, $extra=array())
		{
			///////////////////////////////////////////////////////////////////////////////
			$this->puedesBorrar = false;
			$this->usarGrupos = false;
			
			$this->paginaActual =  $_SERVER['PHP_SELF'];
			
			$this->dbcoment = array();
			$this->tabla = $tabla;
			
			$sql = "select * from `information_schema`.`COLUMNS` where `TABLE_SCHEMA` = '".DB_NAME."' and `TABLE_NAME` = '{$this->tabla}'";
			$this->tabla = $tabla;
			
			
			$rstabla = mysqli_query(asgMng::getCon(), $sql);

			$this->nFields = mysqli_num_rows($rstabla);
			$this->campos = array();
			
			$this->tipos = array();
			
			
			 
			$this->titulo = "Definition of table: $this->tabla";
			
			$this->tiposdedatos = Array();
			$this->campos = array();
			$this->ctitulos = array();
			$this->flags = array();
			$this->buscarPor = array();
			$this->anchoencontroles = 'auto';
			
			$this->camposBusqueda = array();
			$this->camposEnEdicion = array();
			$this->tipoPrimario = "auto";
			
			$x=0;
			
			while($row = mysqli_fetch_array($rstabla))
			{
				if($row['COLUMN_KEY']=='PRI')
				{
					$this->primario = $row['COLUMN_NAME'];
					
				}
				
				$campo = $row['COLUMN_NAME'];
				
				$this->campos[$x] = $campo;
				$this->ctitulos[$campo] = $campo;
				$this->tiposdedatos[$x] = $row['DATA_TYPE'];
				
				
				if($x ==0 && $this->tiposdedatos[$x] != "int")
				{
					$this->tipoPrimario = "manual";
					
				}
				
				//$this->flags[$campo] =  "..".mysql_field_flags($rstabla,$x);
				$this->flags[$campo] =  "..";
				
				$this->visibleEnBusqueda[] = $campo;
				
				$this->grupos[0][0][] = $campo;
				$this->gtitulo[0][0] = "General Data";
				
				$this->camposBusqueda[$campo] = new textBox("$campo");
				$this->camposEnEdicion[$campo] = new textBox("txt$campo");
				$this->camposBusqueda[$campo]->width = $this->anchoencontroles;
				$this->camposEnEdicion[$campo]->width = $this->anchoencontroles;
				
			}
			
			
			$this->sqlMaestro = "select * from $this->tabla";
			
			mysqli_free_result($rstabla);
			
			
		}
		
		function agrupar($lugar,$titulo, $elementos)
		{
			$this->usarGrupos = true;
			$this->grupos[$lugar][] = $elementos;
			$this->gtitulo[$lugar][] = $titulo;
			
			$this->grupos[0][0] = array_diff($this->grupos[0][0], $elementos);
		}

		
		function procesarCabecera($script="")
		{
			if(isset($_GET["mod"]))
				{
					switch($_GET["mod"])
					{
						case "consulta":
							?>
								
				            	<form method="post" action="<?php echo $this->paginaActual; ?>?mod=busqueda" id="frmBusqueda<?php echo $this->tabla; ?>">
								<br/>
								<div align="right">(dblClick to edit one row) &nbsp;<button style="margin-left:100px;" type='button' onclick='newObjItem()'>New</button></div>
				            	<table id="tableConsulta<?php echo $this->tabla; ?>" border="0" class="datagrid" style="margin-top:-15px;"  width="100%">
									<thead>
										<tr>
											<?php
												$fila1="";
												$fila2="";
												
												foreach ($this->visibleEnBusqueda as $campo)
												{
													
													$texto = "(Buscar)";
													$ttitulo = $this->ctitulos[$campo];
													if($this->camposBusqueda[$campo] != "" && get_class($this->camposBusqueda[$campo]) == "textBox")
													{
														$this->camposBusqueda[$campo]->placeholder = "(Buscar)";
													}
												
													$fila1 .= "<th class='grid-header-bg'>$ttitulo</th>";
													$fila2 .= "<td>{$this->camposBusqueda[$campo]}</td>";
													
												}
												
												
												echo "<tr>$fila1<th class='grid-header-bg' style='width:25px;'>Accion</th></tr><tr>$fila2<td>--</td></tr>";
											?>
										</tr>
									</thead>
									<tbody id="busquedaBody<?php echo $this->tabla; ?>">
										<?php
											$sql = $this->sqlMaestro;
											
											$dadetalle = mysqli_query(asgMng::getCon(), $sql); 
											
											$ncampos = mysqli_num_fields($dadetalle);
											
											$c = 1;
											while($row = mysqli_fetch_array($dadetalle))
											{
												$clase = "grid-row-style$c";
												echo "<tr  class='$clase' ondblclick='cargardatosde(\"{$row[0]}\")'>";
													foreach ($this->visibleEnBusqueda as $campo)
													{
														$valor =(!isset($row[$campo]) || $row[$campo] == "" )?"--":$row[$campo];
														
														echo "
															<td>
																$valor
															</td>
														";
													}
												
												echo "<td><button style='cursor:pointer;' type='button' onclick='cargardatosde(\"{$row[0]}\")'>Edit</button></td></tr>";
												$c++;
												if($c++>3)
												{$c=1;}
											}
										?>
									</tbody>
								</table>
								<div align="right"><button style="margin-left:100px;" type='button' onclick='newObjItem()'>New</button></div>
				                </form>
								<div id="divdelujo"></div>
								<script language="javascript">
									asgForm($("#frmBusqueda<?php echo $this->tabla; ?>"),$("#divdelujo"));
									$("input", "#tableConsulta<?php echo $this->tabla; ?>").keyup(function(e){
									
									AsFrmbuscar();
									
									});
								
									$("select", "#tableConsulta<?php echo $this->tabla; ?>").change(function(e){
							
									 AsFrmbuscar();
									
									});
									
									function AsFrmbuscar()
									{
										$.ajax({
											type: 'POST',
											url: $("#frmBusqueda<?php echo $this->tabla; ?>").attr('action'),
											data: $("#frmBusqueda<?php echo $this->tabla; ?>").serialize(),
											// Mostramos un mensaje con la respuesta de PHP
											success: function(data) {
												$('#busquedaBody<?php echo $this->tabla; ?>').html(data);
												
											}
										})  ;
										
									}
				
								</script>
							<?php
							exit();
						break;
						
						case "busqueda":
							$sql = "$this->sqlMaestro where 1 = 1";
							
							foreach($_POST as $llave=>$valor)
							{
								
								if($valor != "" && $valor != "(Buscar)")
								{
									$sql .= " and {$llave} like '%$valor%' ";
								}
							}
							
							$sql .= " limit 50 ";
						
							$dadetalle = mysqli_query(asgMng::getCon(), $sql);
												
							$ncampos = mysqli_num_fields($dadetalle);
							$c = 1;
							while($row = mysqli_fetch_array($dadetalle))
							{
								$clase = "grid-row-style$c";
								echo "<tr class='$clase' ondblclick=\"cargardatosde('{$row[0]}')\">";
									foreach ($this->visibleEnBusqueda as $campo)
									{
										echo "
											<td>
												{$row[$campo]}
											</td>
										";
									}
								
								echo "</tr>";
								$c++;
								if($c++>3)
								{$c=1;}
							
							}
							exit();
						break;
						
						case "edicion":
							$vprimario = 0;
							if(isset($_GET["id"]) )
							{
								
								$id = $_GET["id"];
							
								$sql = "select * from $this->tabla where $this->primario = '$id'";
								
							
								$rowLoad = mysqli_fetch_array(mysqli_query( asgMng::getCon(), $sql));
								
								
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
								
									$control->setValue($rowLoad[$campo]);
								}
								
							}
							else 
							{
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
									
									$control->setValue("");
								}
							}
						
							$vprimario = $this->camposEnEdicion[$this->primario]->text;
							echo "<form autocomplete='off' id='frm$this->tabla' class='validable' action='$this->paginaActual?mod=save' method='post'>";
							echo "<table>";
							
							if($this->usarGrupos)
							{
								echo "<tr>";
								
									echo "<td valign='top'>";
										$this->procesarGrupos(0);
									echo "</td>";
									echo "<td valign='top'>";
										$this->procesarGrupos(1);
									echo "</td>";
									echo "<td valign='top'>";
										$this->procesarGrupos(2);
									echo "</td>";
								
								
								echo "</tr>";
							}
							else 
							{
								
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
									
									$flags = $this->flags[$campo];
									$control->class .= (strpos($flags,"not_null")>0)?" requerido":"";
								
									
									if($campo==$this->primario)
									{
										$control->readonly = " readonly ";
										if($this->tipoPrimario == "auto")
										{
											$control->setValue($control->getValue()+0);
										}
										else
										{
											if($control->getValue() == "")
											{
												$control->setValue("auto");
											}
										}
										//$control->setValue($control->getValue()+0);
										$control->readonly = "readonly";
									}
									$ctitulo = $this->ctitulos[$campo];
									
									if($control->type=="password")
									{
										$control->setValue("");		
										
									}
								
								
									
									$ctitulo = ucwords($ctitulo);
									echo "<tr><th style='text-align:right' valign='top'>$ctitulo:</th><td valign='top'>$control </td></tr>";
					
								}	
							}
							
							$apagadoNuevos = ($vprimario > 0)?"":"disabled"; 
						
							echo "</table>
							<div>{$script}</div>
							<button id='btnNew$this->tabla' type='button' $apagadoNuevos onclick='newObjItem()'>New</button>
							<button onclick='asgMantGuardado()'>Save</button>
							<button type='button' onclick='buscarItems()'>Search</button><!-- -->
							<button onclick='eliminar($vprimario);' id='btnDel$this->tabla' type='button' $apagadoNuevos >Delete</button>
							</form>
							<div id='result$this->tabla'></div>
							<script language='javascript'>
								
								asgForm($('#frm$this->tabla'), $('#result$this->tabla'));
								
								
								
							</script>
							";
							exit();
						break;
						
						
						
						case "save":
							$cod = $_POST["txt$this->primario"];
							$valor = "";
							if($cod > 0)
							{
								
								$usqlCampos = array();
								foreach($this->camposEnEdicion as $campo=>$control)
								{
									if($campo != $this->primario)
									{
										$valor = (isset($_POST["txt$campo"]))?$_POST["txt$campo"]:$control->getValue();
										if($this->camposEnEdicion[$campo]->type == "password")
										{
											if($valor != "")
											{
												$valor = md5($valor);
												$usqlCampos[$campo] = "$campo = '$valor'";
											}	
											
										}
										else if( $this->camposEnEdicion[$campo]->type == "mDetalle")
										{
											
											$valor =  (is_array($valor))?"|". implode("|,|",$valor) . "|" : " ";
											
											if($valor != "")
											{
												$usqlCampos[$campo] = "$campo = '$valor'";
											} 
										}
										
										else 
										{
									
											$usqlCampos[$campo] = "$campo = '$valor'";
										}
									}
								}
								
								$usqlCampos = implode(",",$usqlCampos);
								
								$sql = "update $this->tabla set $usqlCampos where $this->primario = '$cod'";
								//echo $sql;
								mysqli_query(asgMng::getCon(), $sql);
								
								echo mysqli_error(asgMng::getCon());
								$mensaje =  mensajeDeAlerta("Data updated");
							}
							else
							{
								$icsqlCampos = array();
								$ivsqlCampos = array();
								
								$sql = "LOCK tables {$this->tabla}";
								mysqli_query(asgMng::getCon(), $sql);
								foreach($this->camposEnEdicion as $campo=>$control)
								{
									
									if($campo != $this->primario)
									{
										
									
									
									
										$valor = (isset($_POST["txt$campo"]))?$_POST["txt$campo"]:$control->getValue();
										$icsqlCampos[$campo] = "$campo";
										if($this->camposEnEdicion[$campo]->type == "password")
										{
											$valor = md5($valor);
											$ivsqlCampos[$campo] = "'$valor'";
										}
										else 
										{
										
											$ivsqlCampos[$campo] = "'$valor'";
										}
										
										
									}
										
								}
								
								$icsqlCampos = implode(",",$icsqlCampos);
								$ivsqlCampos = implode(",",$ivsqlCampos);
								
								$sql = "insert into $this->tabla ($icsqlCampos) values ($ivsqlCampos)";
								
								mysqli_query(asgMng::getCon(), $sql);
								echo mysqli_error(asgMng::getCon());
								
								$valor = $cod;
									$valor = mysqli_insert_id(asgMng::getCon());
								
								
								
								$sql = "UNLOCK TABLES";
								
								mysqli_query(asgMng::getCon(), $sql);
								echo "
									<script language='javascript'>
										document.getElementById('txt$this->primario').value = '$valor';
									</script>
								";
								
								$mensaje= mensajeDeAlerta("Record added");
							}
							if(mysqli_error(asgMng::getCon()) == "")
							{
							echo $mensaje;
							echo "<script language='javascript'>
								try
								{
									
									document.getElementById('btnNew$this->tabla').disabled = false; 
									document.getElementById('btnDel$this->tabla').disabled = false; 
								}
								catch(ex)
								{
									alert(ex);
								}
								</script>";
							}
							exit();
						break;
						
						case "delete":
							$cod = $_GET["code"];
							$sql = "delete from $this->tabla where $this->primario = '$cod'";
							
							mysqli_query(asgMng::getCon(), $sql);
							
							if(mysqli_error(asgMng::getCon()))
							{
								
								echo mensajeDeError("You can not delete this record, is already being used in the application");
							
							}
							else 
							{
								echo mensajeDeAlerta("Record Deleted!")	;
								
							}
					
							
							echo "
								<script language='javascript'>
									$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=edicion' );
								</script>
								<button type='button' onclick='newObjItem()'>New</button>
						
							<button type='button' onclick='buscarItems()'>Search</button>
							";
							exit();
						break;
					}
				}
				
				if($_POST)
				{
					for($x=0; $x<$nFields; $x++)
					{
						$campo = mysql_field_name($rstabla,$x);
						$$campo = $_POST["txt$campo"];
					}
				}
			
		}
		
		private  function procesarGrupos($gid)
		{
			
			if(isset($this->grupos[$gid]))
			foreach ($this->grupos[$gid] as $grupo=>$campos)
			{
				echo "<div>";
				echo "<fieldset><legend><b>{$this->gtitulo[$gid][$grupo]}</b></legend>";
				echo "<table>";
				foreach ($campos as $campo)
				{
					$control = $this->camposEnEdicion["$campo"];
					$flags = $this->flags[$campo];
									$control->class .= (strpos($flags,"not_null")>0)?" requerido":"";
								
									
									if($campo==$this->primario)
									{
										$control->setValue($control->getValue() + 0);
										$control->readonly = "readonly";
									}
									$ctitulo = $this->ctitulos[$campo];
									
									if($control->type=="password")
									{
										$control->setValue("");		
										
									}
								
								
									$ctitulo = ucwords($ctitulo);
									echo "<tr><th style='text-align:right' valign='top'>$ctitulo:</th><td valign='top'>$control </td></tr>";
					
				}
				echo "</table>";
				echo "</fieldset>";
				echo "</div>";
				
			}
			
			
					
				
			
			
		}
		
		function display()
		{
				$condicion = ($this->puedesBorrar)?"":"alert('Can not delete this record'); return false;";
				echo "
				<br/>
				<fieldset style='min-height:400px'>
				
				<legend class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>
					<font style='font-size:16px;'>{$this->titulo}</font>
				</legend>
					
					<div id='tabMantenimiento$this->tabla'>
						<ul>
				         <li><a href='$this->paginaActual?mod=consulta'><span>Show</span></a></li>
				         <li><a href='$this->paginaActual?mod=edicion'><span>Create / Edit Row</span></a></li>
				     </ul>
					</div>
					
					
				</fieldset>
				<script language='javascript'>
					$(function(){
						$('#tabMantenimiento$this->tabla').tabs();
						  
			
					});
					
					function cargardatosde(objid)
					{
						$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=edicion&id='+objid );
						$('#tabMantenimiento$this->tabla').tabs('select', 1);
					}
					
					function newObjItem()
					{
						$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=edicion' );
						$('#tabMantenimiento$this->tabla').tabs('select', 1);
						$('#tabMantenimiento$this->tabla').tabs('load', 1);
					}
					
					function buscarItems()
					{
						$('#tabMantenimiento$this->tabla').tabs('select', 0);
					
					}
					
					function eliminar(cod)
					{
						{$condicion}
						if(confirm(\"Desea realmente borrar este registro?\"))
						{
							$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=delete&code='+cod );
							$('#tabMantenimiento$this->tabla').tabs('select', 1);
							$('#tabMantenimiento$this->tabla').tabs('load', 1);
						}
					}
					
				</script>
				
			
				
				";
			
		}
		
		
		
		
		
	}
	
	
	
	
	class mantenimientoSimple
	{
		var $tabla;
		var $nFields;
		var $primario;
		var $titulo; //para ponerle el titulo que sale arriba del mantenimiento
		var $anchoencontroles;
		
		var $campos;
		var $ctitulos;//titulos de los campos
		var $tiposdedatos;
		var $flags;
		var $visibleEnBusqueda; //campos que se veran en la busqueda
		var $camposEnEdicion; //campos que se veran en la edicion
		
		var $camposBusqueda;
		var $camposEdicion;
		
		var $paginaActual;
		var $sqlMaestro;
		
		//Para el manejo de grupos.
		var $usarGrupos;
		var $grupos;
		
		var $tipoPrimario;
		var $puedesBorrar;
		
		var $idRow = 0;
		var $script = "";
		
		function __construct($tabla, $idRow)
		{
			///////////////////////////////////////////////////////////////////////////////
			$this->puedesBorrar = false;
			$this->usarGrupos = false;
		
			$this->idRow = $idRow;
			
			$this->paginaActual =  $_SERVER['PHP_SELF'];
			
			$this->dbcoment = array();
			$this->tabla = $tabla;
			
			$sql = "select * from `information_schema`.`COLUMNS` where `TABLE_SCHEMA` = '".DB_NAME."' and `TABLE_NAME` = '{$this->tabla}'";
			$this->tabla = $tabla;
			
			
			$rstabla = mysqli_query(asgMng::getCon(), $sql);

			$this->nFields = mysqli_num_rows($rstabla);
			$this->campos = array();
			
			$this->tipos = array();
			
			
			 
			$this->titulo = "Definition of table: $this->tabla";
			
			$this->tiposdedatos = Array();
			$this->campos = array();
			$this->ctitulos = array();
			$this->flags = array();
			$this->buscarPor = array();
			$this->anchoencontroles = 'auto';
			
			$this->camposBusqueda = array();
			$this->camposEnEdicion = array();
			$this->tipoPrimario = "auto";
			
			$x=0;
			
			while($row = mysqli_fetch_array($rstabla))
			{
				if($row['COLUMN_KEY']=='PRI')
				{
					$this->primario = $row['COLUMN_NAME'];
					
				}
				
				$campo = $row['COLUMN_NAME'];
				
				$this->campos[$x] = $campo;
				$this->ctitulos[$campo] = $campo;
				$this->tiposdedatos[$x] = $row['DATA_TYPE'];
				
				
				if($x ==0 && $this->tiposdedatos[$x] != "int")
				{
					$this->tipoPrimario = "manual";
					
				}
				
				//$this->flags[$campo] =  "..".mysql_field_flags($rstabla,$x);
				$this->flags[$campo] =  "..";
				
				$this->visibleEnBusqueda[] = $campo;
				
				$this->grupos[0][0][] = $campo;
				$this->gtitulo[0][0] = "General Data";
				
				$this->camposBusqueda[$campo] = new textBox("$campo");
				$this->camposEnEdicion[$campo] = new textBox("txt$campo");
				$this->camposBusqueda[$campo]->width = $this->anchoencontroles;
				$this->camposEnEdicion[$campo]->width = $this->anchoencontroles;
				
			}
			
			
			$this->sqlMaestro = "select * from $this->tabla";
			
			mysqli_free_result($rstabla);
			
			
		}
		
		function agrupar($lugar,$titulo, $elementos)
		{
			$this->usarGrupos = true;
			$this->grupos[$lugar][] = $elementos;
			$this->gtitulo[$lugar][] = $titulo;
			
			$this->grupos[0][0] = array_diff($this->grupos[0][0], $elementos);
		}

		
		function procesarCabecera($script="")
		{
			if(isset($_GET["mod"]))
				{
					switch($_GET["mod"])
					{
						case "consulta":
							?>
								
				            	<form method="post" action="<?php echo $this->paginaActual; ?>?mod=busqueda" id="frmBusqueda<?php echo $this->tabla; ?>">
								<br/>
								<div align="right">(dblClick to edit one row) &nbsp;<button style="margin-left:100px;" type='button' onclick='newObjItem()'>New</button></div>
				            	<table id="tableConsulta<?php echo $this->tabla; ?>" border="0" class="datagrid" style="margin-top:-15px;"  width="100%">
									<thead>
										<tr>
											<?php
												$fila1="";
												$fila2="";
												
												foreach ($this->visibleEnBusqueda as $campo)
												{
													
													$texto = "(Buscar)";
													$ttitulo = $this->ctitulos[$campo];
													if($this->camposBusqueda[$campo] != "" && get_class($this->camposBusqueda[$campo]) == "textBox")
													{
														$this->camposBusqueda[$campo]->placeholder = "(Buscar)";
													}
												
													$fila1 .= "<th class='grid-header-bg'>$ttitulo</th>";
													$fila2 .= "<td>{$this->camposBusqueda[$campo]}</td>";
													
												}
												
												
												echo "<tr>$fila1<th class='grid-header-bg' style='width:25px;'>Accion</th></tr><tr>$fila2<td>--</td></tr>";
											?>
										</tr>
									</thead>
									<tbody id="busquedaBody<?php echo $this->tabla; ?>">
										<?php
											$sql = $this->sqlMaestro;
											
											$dadetalle = mysqli_query(asgMng::getCon(), $sql); 
											
											$ncampos = mysqli_num_fields($dadetalle);
											
											$c = 1;
											while($row = mysqli_fetch_array($dadetalle))
											{
												$clase = "grid-row-style$c";
												echo "<tr  class='$clase' ondblclick='cargardatosde(\"{$row[0]}\")'>";
													foreach ($this->visibleEnBusqueda as $campo)
													{
														$valor =(!isset($row[$campo]) || $row[$campo] == "" )?"--":$row[$campo];
														
														echo "
															<td>
																$valor
															</td>
														";
													}
												
												echo "<td><button style='cursor:pointer;' type='button' onclick='cargardatosde(\"{$row[0]}\")'>Edit</button></td></tr>";
												$c++;
												if($c++>3)
												{$c=1;}
											}
										?>
									</tbody>
								</table>
								<div align="right"><button style="margin-left:100px;" type='button' onclick='newObjItem()'>New</button></div>
				                </form>
								<div id="divdelujo"></div>
								<script language="javascript">
									asgForm($("#frmBusqueda<?php echo $this->tabla; ?>"),$("#divdelujo"));
									$("input", "#tableConsulta<?php echo $this->tabla; ?>").keyup(function(e){
									
									AsFrmbuscar();
									
									});
								
									$("select", "#tableConsulta<?php echo $this->tabla; ?>").change(function(e){
							
									 AsFrmbuscar();
									
									});
									
									function AsFrmbuscar()
									{
										$.ajax({
											type: 'POST',
											url: $("#frmBusqueda<?php echo $this->tabla; ?>").attr('action'),
											data: $("#frmBusqueda<?php echo $this->tabla; ?>").serialize(),
											// Mostramos un mensaje con la respuesta de PHP
											success: function(data) {
												$('#busquedaBody<?php echo $this->tabla; ?>').html(data);
												
											}
										})  ;
										
									}
				
								</script>
							<?php
							exit();
						break;
						
						case "busqueda":
							$sql = "$this->sqlMaestro where 1 = 1";
							
							foreach($_POST as $llave=>$valor)
							{
								
								if($valor != "" && $valor != "(Buscar)")
								{
									$sql .= " and {$llave} like '%$valor%' ";
								}
							}
							
							$sql .= " limit 50 ";
						
							$dadetalle = mysqli_query(asgMng::getCon(), $sql);
												
							$ncampos = mysqli_num_fields($dadetalle);
							$c = 1;
							while($row = mysqli_fetch_array($dadetalle))
							{
								$clase = "grid-row-style$c";
								echo "<tr class='$clase' ondblclick=\"cargardatosde('{$row[0]}')\">";
									foreach ($this->visibleEnBusqueda as $campo)
									{
										echo "
											<td>
												{$row[$campo]}
											</td>
										";
									}
								
								echo "</tr>";
								$c++;
								if($c++>3)
								{$c=1;}
							
							}
							exit();
						break;
						
						case "edicion":
							
							exit();
						break;
						
						
						
						case "save":
							$cod = $_POST["txt$this->primario"];
							$valor = "";
							if($cod > 0)
							{
								
								$usqlCampos = array();
								foreach($this->camposEnEdicion as $campo=>$control)
								{
									if($campo != $this->primario)
									{
										$valor = (isset($_POST["txt$campo"]))?$_POST["txt$campo"]:$control->getValue();
										if($this->camposEnEdicion[$campo]->type == "password")
										{
											if($valor != "")
											{
												$valor = md5($valor);
												$usqlCampos[$campo] = "$campo = '$valor'";
											}	
											
										}
										else if( $this->camposEnEdicion[$campo]->type == "mDetalle")
										{
											
											$valor =  (is_array($valor))?"|". implode("|,|",$valor) . "|" : " ";
											
											if($valor != "")
											{
												$usqlCampos[$campo] = "$campo = '$valor'";
											} 
										}
										
										else 
										{
									
											$usqlCampos[$campo] = "$campo = '$valor'";
										}
									}
								}
								
								$usqlCampos = implode(",",$usqlCampos);
								
								$sql = "update $this->tabla set $usqlCampos where $this->primario = '$cod'";
								//echo $sql;
								mysqli_query(asgMng::getCon(), $sql);
								
								echo mysqli_error(asgMng::getCon());
								$mensaje =  mensajeDeAlerta("Data updated");
							}
							else
							{
								$icsqlCampos = array();
								$ivsqlCampos = array();
								
								$sql = "LOCK tables {$this->tabla}";
								mysqli_query(asgMng::getCon(), $sql);
								foreach($this->camposEnEdicion as $campo=>$control)
								{
									
									if($campo != $this->primario)
									{
										
									
									
									
										$valor = (isset($_POST["txt$campo"]))?$_POST["txt$campo"]:$control->getValue();
										$icsqlCampos[$campo] = "$campo";
										if($this->camposEnEdicion[$campo]->type == "password")
										{
											$valor = md5($valor);
											$ivsqlCampos[$campo] = "'$valor'";
										}
										else 
										{
										
											$ivsqlCampos[$campo] = "'$valor'";
										}
										
										
									}
										
								}
								
								$icsqlCampos = implode(",",$icsqlCampos);
								$ivsqlCampos = implode(",",$ivsqlCampos);
								
								$sql = "insert into $this->tabla ($icsqlCampos) values ($ivsqlCampos)";
								
								mysqli_query(asgMng::getCon(), $sql);
								echo mysqli_error(asgMng::getCon());
								
								$valor = $cod;
									$valor = mysqli_insert_id(asgMng::getCon());
								
								
								
								$sql = "UNLOCK TABLES";
								
								mysqli_query(asgMng::getCon(), $sql);
								echo "
									<script language='javascript'>
										try{
											document.getElementById('txt$this->primario').value = '$valor';
											
										}catch(ex){
											alert(ex);	
										}
									</script>
								";
								
								$mensaje= mensajeDeAlerta("Record added");
							}
							if(mysqli_error(asgMng::getCon()) == "")
							{
								echo $mensaje;
								echo "<script language='javascript'>
									actualizarPadreForm();
								
								</script>";
							
							}
							exit();
						break;
						
						case "delete":
							$cod = $_GET["code"];
							$sql = "delete from $this->tabla where $this->primario = '$cod'";
							
							mysqli_query(asgMng::getCon(), $sql);
							
							if(mysqli_error(asgMng::getCon()))
							{
								
								echo mensajeDeError("You can not delete this record, is already being used in the application");
							
							}
							else 
							{
								echo mensajeDeAlerta("Record Deleted!")	;
								
							}
					
							
							echo "
								<script language='javascript'>
									actualizarPadreForm();
								</script>
							";
							exit();
						break;
					}
				}
				
				if($_POST)
				{
					for($x=0; $x<$nFields; $x++)
					{
						$campo = mysql_field_name($rstabla,$x);
						$$campo = $_POST["txt$campo"];
					}
				}
			
		}
		
		private  function procesarGrupos($gid)
		{
			
			if(isset($this->grupos[$gid]))
			foreach ($this->grupos[$gid] as $grupo=>$campos)
			{
				echo "<div>";
				echo "<fieldset><legend><b>{$this->gtitulo[$gid][$grupo]}</b></legend>";
				echo "<table>";
				foreach ($campos as $campo)
				{
					$control = $this->camposEnEdicion["$campo"];
					$flags = $this->flags[$campo];
									$control->class .= (strpos($flags,"not_null")>0)?" requerido":"";
								
									
									if($campo==$this->primario)
									{
										$control->setValue($control->getValue() + 0);
										$control->readonly = "readonly";
									}
									$ctitulo = $this->ctitulos[$campo];
									
									if($control->type=="password")
									{
										$control->setValue("");		
										
									}
								
								
									$ctitulo = ucwords($ctitulo);
									echo "<tr><th style='text-align:right' valign='top'>$ctitulo:</th><td valign='top'>$control </td></tr>";
					
				}
				echo "</table>";
				echo "</fieldset>";
				echo "</div>";
				
			}
			
			
					
				
			
			
		}
		
		function display()
		{
				$condicion = ($this->puedesBorrar)?"":"alert('Can not delete this record'); return false;";
				echo "
				<style>
					#autoForm input[type=text]{width:300px;}
				</style>
				<br/>
				<fieldset id='autoForm' style='min-height:400px'>
				
				<legend class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>
					<font style='font-size:16px;'>{$this->titulo}</font>
				</legend>
					";
					
					
					$vprimario = 0;
					$_GET["id"] = $this->idRow;
					
					
							if(isset($_GET["id"]) )
							{
								
								$id = $_GET["id"];
								
								
							
								$sql = "select * from $this->tabla where $this->primario = '$id'";
								
								
								$rowLoad = mysqli_fetch_array(mysqli_query( asgMng::getCon(), $sql));
								
								
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
								
									$control->setValue($rowLoad[$campo]);
								}
								
							}
							else 
							{
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
									
									$control->setValue("");
								}
							}
						
							$vprimario = $this->camposEnEdicion[$this->primario]->text;
							echo "<form autocomplete='off' id='frm$this->tabla' class='validable' action='$this->paginaActual?mod=save' method='post'>";
							echo "<table>";
							
							if($this->usarGrupos)
							{
								echo "<tr>";
								
									echo "<td valign='top'>";
										$this->procesarGrupos(0);
									echo "</td>";
									echo "<td valign='top'>";
										$this->procesarGrupos(1);
									echo "</td>";
									echo "<td valign='top'>";
										$this->procesarGrupos(2);
									echo "</td>";
								
								
								echo "</tr>";
							}
							else 
							{
								
								foreach ($this->camposEnEdicion as $campo=>$control)
								{
									
									$flags = $this->flags[$campo];
									$control->class .= (strpos($flags,"not_null")>0)?" requerido":"";
								
									
									if($campo==$this->primario)
									{
										$control->readonly = " readonly ";
										if($this->tipoPrimario == "auto")
										{
											$control->setValue($control->getValue()+0);
										}
										else
										{
											if($control->getValue() == "")
											{
												$control->setValue("auto");
											}
										}
										//$control->setValue($control->getValue()+0);
										$control->readonly = "readonly";
									}
									$ctitulo = $this->ctitulos[$campo];
									
									if($control->type=="password")
									{
										$control->setValue("");		
										
									}
								
								
									
									$ctitulo = ucwords($ctitulo);
									echo "<tr><th style='text-align:right' valign='top'>$ctitulo:</th><td valign='top'>$control </td></tr>";
					
								}	
							}
							
							$apagadoNuevos = ($vprimario > 0)?"":"disabled"; 
						
							echo "</table>
							<div>{$this->script}</div>
							<button onclick='asgMantGuardado()'>Save</button>
							<button onclick='eliminar($vprimario);' id='btnDel$this->tabla' type='button' $apagadoNuevos >Delete</button>
							</form>
							<div id='result$this->tabla'></div>
							<script language='javascript'>
								
								asgForm($('#frm$this->tabla'), $('#result$this->tabla'));
								
								
								
							</script>
							";
					
				echo	"
					
					
				</fieldset>
				<script language='javascript'>
				
					
					function cargardatosde(objid)
					{
						$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=edicion&id='+objid );
						$('#tabMantenimiento$this->tabla').tabs('select', 1);
					}
					
					function newObjItem()
					{
						$('#tabMantenimiento$this->tabla').tabs( 'url' , 1 , '$this->paginaActual?mod=edicion' );
						$('#tabMantenimiento$this->tabla').tabs('select', 1);
						$('#tabMantenimiento$this->tabla').tabs('load', 1);
					}
					
					function buscarItems()
					{
						$('#tabMantenimiento$this->tabla').tabs('select', 0);
					
					}
					
					function eliminar(cod)
					{
						
						if(confirm(\"Desea realmente borrar este registro?\"))
						{
							cargarEn('frm{$this->tabla}', '$this->paginaActual?mod=delete&code='+cod);
						}
					}
					
				</script>
				
			
				
				";
			
		}
		
		
		
		
		
	}


?>