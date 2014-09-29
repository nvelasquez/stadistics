<?php
	include("motor.php");

	class ventana{
	public $nom;

			function crear(){
			
			$nombre=$_GET['name'];
			$tablas = $_GET['tabla'];
			$this->nom=$nombre;

			$query = "select * from $tablas Where nombre = '$nombre'";
			$pedido = mssql_query($query);
			$bd = DB_NAME;
			$query2 = "SELECT COLUMN_NAME
					FROM information_schema.COLUMNS
					WHERE TABLE_SCHEMA  LIKE '$bd'
					AND TABLE_NAME = '$tablas'";

				$pedido2=mssql_query($query2);

			echo "
				<div id='{$this->nom}' style='margin-top:-400px;box-shadow: 1px 1px 3px rgb(44, 44, 44);background: rgb(248, 247, 247);position: absolute;padding:10px;border:solid 1px rgb(207, 207, 207); width:220px;'>
				<theader><h4 style='float:left;'>Detalles</h4><h3 onclick='desemerger(\"$this->nom\")' style='float:right;cursor:pointer;'>X</h3></theader>
				<table>
				";
						$campos = array();
						$numero = 0;
						
						while ( $filat=mssql_fetch_array($pedido2)) 
						{ 

							$campos[] =	$filat;			
						}
						
						
						while($fila=mssql_fetch_assoc($pedido)){
						
						echo "<tr>";
						foreach($fila as $value){
							echo "
									<tr><td>".$campos[$numero][0]."</td><td style='color:blue;'>".$value."</td></tr>
									
							";
							$numero++;

						}
						echo '</tr>';
						break;
					}

						
					
				echo "</table>
				</div>
			";
		}
}
$ventana=new ventana();
$ventana->crear();
?>