<?php

include("engine.php");

	if(isset($_GET["descargar"]) && $_GET["descargar"] > 0)
	{
		$id = $_GET["descargar"];		
		fileDownload($id);
	}



function fileDownload($id)
{
	$sql = "select * from files where id = $id";
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);
	
	$filename = $row["nombre"];
	$ctype = $row["tipo"];
	$tabla = $row["tabla"];
	$registro = $row["registro"];
	$file = "../files/$tabla/r$registro/$id";
	
    //First, see if the file exists
    if (!is_file($file)) { die("<b>404 File not found!</b>"); }
	
    //Gather relevent info about file
    $len = filesize($file);
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
   
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");

    //Force the download
    $header="Content-Disposition: innline; filename=".$filename.";";
    header($header );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    readfile($file);
    exit;
}
	if(isset($_POST["displayAsgFiles"]))
	{
		
		$tabla = $_POST["tabla"];
		$registro = $_POST["registro"];
		
		if($registro > 0)
		{
		
		$sql = "select * from files where registro='$registro' and tabla = '$tabla'";
		$rs = mysql_query($sql);
		echo mysql_error();
		while ($row = mysql_fetch_array($rs)) {
			echo "<div ondblclick='descargarFile({$row['id']});' >
				<img alt='' src='images/icons/{$row['extension']}.png' width='16'/>{$row['nombre']}
			
			</div>";
			
		}
		}
		
		exit();
	}

$tabla = $_GET["tabla"];
		$registro = $_GET["registro"];

	if($_POST)
	{
		
		$tabla = $_GET["tabla"];
		$registro = $_GET["registro"];
		
		
		$desc = $_POST["txtDescripcion"];
		$file = "";
		
		print_r($_FILES);
		if($_FILES["txtFile"]["error"] != 4)
		{
			$nombre = $_FILES["txtFile"]["name"];
			$type = $_FILES["txtFile"]["type"];
			
			$extension = substr($nombre, strrpos($nombre,".")+1);
			
			$sql = "insert into files (tabla, registro, tipo, nombre, descripcion,extension) 
			values ('$tabla','$registro', '$type', '$nombre', '$desc','$extension') ";
			mysql_query($sql);
			$tmpname = mysql_insert_id();
			
			
			
			if(!is_dir("../files/$tabla"))
			{
				mkdir("../files/$tabla");
			}
			echo "../files/$tabla/$registro";
			if(!is_dir("../files/$tabla/r$registro"))
			{
				 mkdir("../files/$tabla/r$registro");
			}
			
			move_uploaded_file($_FILES["txtFile"]["tmp_name"], "../files/$tabla/r$registro/$tmpname");
			
		}
		
		echo "
		<script language='javascript'>
		
		parent.cerrarFileManager();
		
		
		
		</script>
		
		";
		
		
		
		exit();
	}
?>

<center>
	<form autocomplete="false" onsubmit="return validarFilesForm();" method="POST" enctype="multipart/form-data" action="libreria/asgFiles.php?tabla=<?php echo $tabla ?>&registro=<?php echo $registro ?>" target="ifFiles">
		<h3>Datos del Archivo</h3>
		<table>
			<tr>
				<th>Descripci&oacute;n</th>
				<td><input type="text" name="txtDescripcion" id="txtDescripcion"/></td>
			</tr>
			<tr>
				<th>Archivo</th>
				<td><input type="file" name="txtFile" id="txtFile"/></td>
			</tr>
			
		
		</table>
	
		<button type="submit">Enviar</button>
	
	
	
	
	
	</form>
	<iframe name="ifFiles">
	
	
	</iframe>

</center>

<script language="javascript">
	
	function validarFilesForm()
	{
		rs = true;
		desc = document.getElementById("txtDescripcion").value;
		file = document.getElementById("txtFile").value;
		mensaje = "Debe llenar todos los campos para poder agregar el archivo: \n";
	
		if(desc == "")
		{
			mensaje += " * Debe digitar la descripcion del archivo \n";
			rs = false;
		}
		if(file == "")
		{
			mensaje += " * Debe seleccionar el archivo \n";
			rs = false;
		}
		
		if(rs == false)
		{
			
			alert(mensaje);
		}
		
		
		return rs;
	}

</script>