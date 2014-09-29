	<?php
	include('../librerias/motor.php');

	$nombres = array();
	$dato= array();

	$query1 = 'select Nombre from MunicipiosJCE';

	$pedido1 = mssql_query($query1);

	while($fila1=mssql_fetch_assoc($pedido1)){
		foreach($fila1 as $value){
			$nombres[]=$value;
		}
		
	}
	mssql_free_result($pedido1);

		
		$query = "select IdEstudiante from  RegistroEstudiantes where IdMunicipioJCE = 1";
		$pedido=mssql_query($query);

		while($fila=mssql_fetch_assoc($pedido)){
		foreach($fila as $value1){
			$dato[] = mssql_num_rows($pedido);
			}

		}
		mssql_free_result($pedido);

		
	echo "var jsons = {
		type: 'pie',
        name: 'estudiantes por municipio',
        data:[";
        for($i=0;$i<count($nombres);$i++){
        	echo "['".$nombres[$i]."',".$dato[$i]."],";
        }
        echo "]
    	}
    	";

	

?>