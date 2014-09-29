<?php
$_SESSION["ASGtienda"] = 0;
class genclas
{
	var $tabla;
	var $numFields;
	var $campos;
	var $tienda;
	var $tipos;
	
	var $dbcoment;
	var $dbname;
	
	function setVal($nombre, $valor)
	{
		$this->$nombre = mysql_real_escape_string(stripslashes($valor));
	
	}
	
	function getPOST()
	{
		
		echo "<pre>";
		
		foreach($this->campos as $campo)
		{
			$c = ucwords($campo);
			echo "
\${$this->tabla}->{$campo} = (isset(\$_POST['txt{$c}']))?\$_POST['txt{$c}']:\${$this->tabla}->{$campo};";
		}
		echo "
		
\${$this->tabla}->guardar();
		</pre>";
	}
	
	
	function getTEXT()
	{
		//title=\"<?php echo \${$this->tabla}->dbcoment['$campo']; ? > \";
		$t = "
<form method='post' id='frm{$this->tabla}' action='modulos/sfsdfsdf/pagina.php'><table>
		";
		foreach($this->campos as $campo)
		{
			$c = ucwords($campo);
			$t .=   ("
	<tr>
		<th>
			$c:
		</th>
		<td>
			<input type='text' name='txt{$c}' id='txt{$c}' value=\"<?php echo htmlentities(\${$this->tabla}->{$campo}); ?>\"  />
		</td>
	</tr>
");
		}
		$t .= "</table>
		<div>
			<button type='submit'>Send</button>
		</div>
		</form>
		<div id='divRs{$this->tabla}'></div>
		<script language='javascript'>
			asgForm(\$('#frm{$this->tabla}'),\$('#divRs{$this->tabla}'));
		</script>
		";
		
		echo "<pre>";
		echo htmlentities($t) ;
		echo "</pre>";
	}
	
	function __construct($tabla, $cod = 0, $file = false, $dbname=null)
	{
		
		$this->dbcoment = array();
		$this->tabla = $tabla;
		
		$this->dbname = (is_null($dbname))?DB_NAME:$dbname;
		
		$sql = "select * from `information_schema`.`COLUMNS` where `TABLE_SCHEMA` = '".$this->dbname."' and `TABLE_NAME` = '{$this->tabla}'";
		
		$rs = mysqli_query(asgMng::getCon(), $sql);

		$this->numFields = mysqli_num_rows($rs);
		$this->campos = array();
		
		$this->tipos = array();
		$x=0; 
		while($row = mysqli_fetch_array($rs))
		{
			if($x==0)
			{
				$this->primario = $row['COLUMN_NAME'];
			}
			
			$campo = $row['COLUMN_NAME'];
			$this->$campo = "";
			$this->dbcoment[$campo] = $row['COLUMN_COMMENT'];
			$this->campos[] = $campo;
			$x++;
			$this->tipos[$campo] =  $this->etipos($row['DATA_TYPE']);
		}
		
		$pri = $this->primario;
		mysqli_free_result($rs);
		
		if($cod > 0)
		{
			$this->$pri = $cod;
			$this->cargar();
		}
		
		
		if($file)
		{
			
			$fecha = date("d/m/Y");
			
			$ruta = __DIR__;
			$separador = "/";
			if(strpos($ruta, '\\')){
				$separador = "\\";
			}
			$ruta = $ruta."{$separador}auto{$separador}$tabla.php";
			
			$datos = "<?php
			
class auto_{$tabla}{

	function __construct()
	{
			
	}
	
	function guardar(){
			
		
	}
				
	function cargar(){
		
		
	}			
				
}

//Creado en fecha {$fecha}";
			
			$f = fopen($ruta,'w');
			fwrite($f, $datos);
			fclose($f);
			
			
		}
	}
	
	
	
	function guardar($mostrarError = true)
	{
		$todoBien = true;
		$pri = $this->primario;
		//$this->tienda = $_SESSION["ASGtienda"];
		$sqlI1 = array();
		$sqlI2 = array();
		$parametros = array();
		$tdatos = "";
		$sqlu = array();
		//$this->tienda = $_SESSION["ASGtienda"];
		foreach($this->campos as $x=>$campo)
		{
			if($x > 0)
			{
				//$v = mysql_real_escape_string(stripslashes($this->$campo));
				$v = $this->$campo;
				$sqlI1[] = "`{$campo}`";
				$sqlI2[] = "?";
				$parametros[] = "\$this->{$campo}";
				
				$tdatos .= $this->tipos[$campo];
				
				$sqlu[] = "`{$campo}`=? ";
			}
		}
		
		$sqlI1 = implode(",",$sqlI1);
		$sqlI2 = implode(",",$sqlI2);
		$sqlu= implode(",",$sqlu);
		
		
		if($this->$pri > 0)
		{
			$sql = "update {$this->dbname}.{$this->tabla} set {$sqlu} where `{$this->primario}` = '{$this->$pri}'";
			
			
			$stmt = mysqli_prepare(asgMng::getCon(), $sql);
				
			$parametros = implode(", ",$parametros);
			
			$p = "mysqli_stmt_bind_param(\$stmt, \$tdatos, {$parametros});";
			
			eval($p);
			
			mysqli_stmt_execute($stmt);
			
			
			//mysql_query($sql);
			/*
			if(mysql_error())
			{
				$todoBien = false;	
			}*/
		}
		else
		{
			$sql = "insert into {$this->dbname}.{$this->tabla} ({$sqlI1}) values ({$sqlI2})";
			
			$stmt = mysqli_prepare(asgMng::getCon(), $sql);
				
			$parametros = implode(", ",$parametros);
			
			$p = "mysqli_stmt_bind_param(\$stmt, \$tdatos, {$parametros});";
						
			eval($p);
			
			mysqli_stmt_execute($stmt);
			if($mostrarError)
			{
				echo mysqli_error(asgMng::getCon());
			}
			$this->$pri = mysqli_insert_id(asgMng::getCon());
			
		}
		return $todoBien;
	}
	
	function cargar()
	{
		$pri = $this->primario;
		$sql = "select * from {$this->dbname}.{$this->tabla} where `{$this->primario}` = '{$this->$pri}'";
		
		
		$rs = mysqli_query(asgMng::getCon(), $sql);
		
		if(mysqli_num_rows($rs) > 0)
		{
			$row = mysqli_fetch_array($rs);
			foreach($this->campos as $x=>$campo)
			{
				$this->$campo = $row[$campo];
			}
		}
		mysqli_free_result($rs);
	}
	
	
	function etipos($t)
	{
		switch($t)
		{
			case "varchar":
				$t = 's';
			break;
			
			case "date";
				$t = 's';
			break;
			
			case "smallint";
				$t = 'i';
			break;
			
			case "text";
				$t = 's';
			break;
			
			default:
				$t = 's';
			break;
			
		}
		return substr($t, 0, 1);
	}

}


//echo rutaRelativa(__FILE__);
//echo"<br/>".rutaRelativa(__DIR__);
function rutaRelativa($ruta){
	$pos = strlen(getcwd());
	return substr($ruta, $pos);
	}
?>