
<?php

			function getContent($query)
{
$Equery=urlencode($query);
$q = "http://vixicom.com/od/?sql={$Equery}";
$datos = file_get_contents($q);
$datos = json_decode($datos, true);

return $datos;
}
	
	class mostrar {
		function mostro($tabla){
			include 'libreria/conexion.php';
			
			$query = "SELECT * FROM $tabla";
			$pedido = mysql_query($query);
			
			echo "<h3>Agregados</h3>";
			while($fila = mysql_fetch_assoc($pedido)){
				echo $fila["nombre"].' '.$fila["apellido"]."</br>";
			}
			
		
		}
		

		
		public function posted($tabla){
			include("libreria/conexion.php");
			
			
			$query = "SELECT COLUMN_NAME
			FROM information_schema.COLUMNS
			WHERE TABLE_SCHEMA  LIKE '$bd'
			AND TABLE_NAME = '$tabla'";
			
			$pedido = mysql_query($query);
			
			
			while($fila = mysql_fetch_assoc($pedido)){
				foreach($fila as $valor){
					echo "\$".$valor." = \$_POST['".$valor."'];<br/>";
				}
			}
		}
	}
	
	
	class mostrarTabla {
		function tablaMostrar($tabla){
			include("libreria/conexion.php");
			
			$query = "SELECT * FROM $tabla";
			$pedido = mysql_query($query);
			
			$query1 = "SELECT COLUMN_NAME
			FROM information_schema.COLUMNS
			WHERE TABLE_SCHEMA  LIKE '$bd'
			AND TABLE_NAME = '$tabla'";
			
			$pedido1 = mysql_query($query1);
			
			
			
			
			
			echo '<table style="width:100%;
    background: rgb(181, 253, 253);
    
			" ><tr>';
			
			while ($fila1 = mysql_fetch_assoc($pedido1)) {
				foreach ($fila1 as $valor) {
					echo "<td>".$valor."</td>"; 
				}
			}
			echo "</tr>";
			
			while($fila = mysql_fetch_assoc($pedido)){
					echo "<tr>";
				foreach ($fila as $valor) {
					echo '<td>'.$valor.'</td>';
				}
				echo "</tr>";
			}
			echo '</table>';
			//mysql_free_result($pedido1);
			mysql_free_result($pedido);
		}
				

			
	}
	
	 class tInput {
	 	//function creadora y rellenadora de selects
	 	
		public  function crearSelect($aimprimir,$id,$nametabla,$nameselect,$nameLabel){
		
		
		echo "<td><label>{$nameLabel}</label><select id='{$nameselect}' onChange='cambio(this.value);' name='$nameselect'>";
		
		$query = "SELECT Nombre, IdProvincia FROM $nametabla ";
		
		$pedido =getContent($query);
		
		
		
		
		foreach ($pedido as $valor){
			
			echo "<option  value='{$valor["$id"]}'>".$valor["$aimprimir"]."</option>";
			
			
		}
		echo "<select/><td/>";
	
		

		
		
		}
		
		
		//function creadora de radio
		public function crearRadio($name,$para2){
			
			foreach($para2 as $valor){
				
				echo "<input type='radio' name='{$name}' />".$valor."<br/>";  
			}
			
		}
		
		
		//function creadora y inputs con valores de base de datos
		public function inputTXT($titulo,$tabla,$action){
			include("conexion.php");
			
			$query = "SELECT COLUMN_NAME
			FROM information_schema.COLUMNS
			WHERE TABLE_SCHEMA  LIKE '$bd'
			AND TABLE_NAME = '$tabla'";
	
			$pedido = getContent($query);
			
			echo "<form method='post' action='$action' ><fieldset><theader><h2>".$titulo."</h2></theader><table style='text-align:right;'>";
			while($fila = mysql_fetch_assoc($pedido)){
				foreach ($fila as $valor) {
					echo "
					<tr>
						<th>$valor</th>
						<td>
							<input type='text' name='$valor' value='' />
						<td/>
						<tr/>
					"; 
				}
			}
			echo "</table><input type='submit' value='enviar' style='float:left;'/></fieldset></form>";
			mysql_free_result($pedido);
			
		}
		
		
		public function crearCheck($name,$array){
			foreach ($array as $valor) {
				
				echo "<input type='checkbox' name='$name' />{$valor}<br/>";
			}
		}
	}
	
	
	
		  
	
?>