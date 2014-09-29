<?php
	class busqueda{
		public $nom="idd";
	

	function crearBusqueda(){
	echo "
	<div id='{$this->nom}'>
		<form action='' method='POST'>
		<table>
			<tr>
			<label>Agregar</label>
			<td>
				<input type='checkbox' name='check'></td>
				<h4 style='float:right;cursor:pointer;' onclick='borrarBusqueda(\"{$this->nom}\")'>X</h4>
			</tr>
			<tr>
				
				<td>
				<select name='based'>
					<option value='OPEN_DATA'>open data</option>
					<option value='MIERD'>mierd</option>
					<option value='SGC_2010'>sgc-2010</option>
				</select></td>
			</tr>
			<tr>
			
			<td>
				<select name='tablas'>
					<option value='nombre'>nombre</option>
					<option value='nombre'>nombre</option>
				</select></td>
			</tr>
			
			<td>
				<select name='relacion'>
					<option value='nombre'>nombre</option>
					<option value='nombre'>nombre</option>
				</select></td>
			</tr>
			<tr><td>
				<input type='text' name='filnombre' value='nombre' placeholder='nombre'></input></td>
			</tr>
			</table>
		</form>
		</div>

	";
}
}

$busqueda = new busqueda();
$busqueda->crearBusqueda();


?>