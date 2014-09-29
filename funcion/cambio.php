
<?php
include('../librerias/motor.php');

if(isset($_GET['id'])){
	$name = $_GET['id'];
	$query = "select * from MunicipiosJCE where IdProvincia = '$name'";
	$pedido = mssql_query($query);

	while($fila=mssql_fetch_assoc($pedido)){
		echo "<option value='".$fila['IdMunicipio']."'>".$fila['Nombre'].'</option>';
	}
	mssql_free_result($pedido);
}


if(isset($_GET['completado'])){
					$idm=$_GET['completado'];
					$query = "select pf.Nombre from PlantasFisica as pf where pf.IdMunicipio = '$idm'";
					$pedido=mssql_query($query);
					$datosa = array();
					while($fila = mssql_fetch_assoc($pedido)){
						foreach($fila as $valor){
							$datosa[] = $valor;
						}
					}

	echo '<script>
				 $(function(){
		

	var datos = [';
					for($i=0;$i<20;$i++){
						echo "'".$datosa[$i]."',";
					}
					echo "];";

	echo '
		$( "#busqueda" ).autocomplete({
      source: datos
    });
  });
	</script>';
}
mssql_free_result($pedido);
?>