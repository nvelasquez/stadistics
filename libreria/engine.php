<?php
session_start();

include("configx.php");
include("conexion.php");
include("asgControls.php");
include("genclas.php");
include("utils.php");
include("sfacil.php");
include("mantenimiento.php");
include("shortclass.php");
include("Calendario.php");
include("language.php");


date_default_timezone_set("America/Santo_Domingo");





$n = strpos($_SERVER['HTTP_USER_AGENT'],"MSIE");
$isie = false;

if(!($n === false))
{
	$isie= true;
}
define("ISIE",$isie);


function createSelect($name,$span,$rangeI=1,$rangeF=30,$addZero=false,$array=array())
	{	echo "<span>{$span}</span>";
		echo "<select name={$name} class=cls{$name}>";
		if(count($array)>0)
		{
			for ($i=$rangeI; $i <= $rangeF; $i++) { 
				echo "<option value='{$i}'>{$array[$i]}</option>";
			}
		}
		else
		{
			for ($i=$rangeI; $i <= $rangeF; $i++)
			{
				if($addZero)
				{
					if($i<10)
					{
						echo "<option value='{$i}'>0".$i."</option>";
					}
					else
					{
						echo "<option value='{$i}'>$i</option>";
					}
				}
				else
				{
					echo "<option value='{$i}'>$i</option>";
				}
			}
		}
		echo '</select>';
	}

//cargarConfiguracion();

function cargarConfiguracion()
{

	$sql = "select * from config";

	$datos = array();

	$rs = mysqli_query(asgMng::getCon(), $sql);

	echo mysqli_error(asgMng::getCon());
	while($fila = mysqli_fetch_array($rs))

	{
		$datos[$fila["propiedad"]] = $fila["valor"];

	}

	
	$_SESSION["config"] = $datos;

}


//Para limpiar los datos extra�os
foreach($_POST as $llave=>&$valor)
{
	
	if(is_array($valor))
	{	
		foreach($valor as &$det)
		{
			$det = addslashes($det);
		}
	}
	else
	{
		if($llave != "txtContent" || $llave != "javascript" ) //estos estan omitidos
		{
			$valor = addslashes($valor);
		}
	}
}
//Para limpiar los gets
foreach($_GET as &$valor)
{
	//Corregir error para cuando venga datos de tipo arreglo
	if(is_array($valor))
	{	
		foreach($valor as &$det)
		{
			$det = addslashes($det);
		}
	}
	else
	{
		$valor = addslashes($valor);
	}
}






function validarfile($file)
{
	$result = false;
	if($file['size'] < 300000 && ($file['type'] == "image/jpeg" || $file['type'] == "image/pjpeg" || $file['type'] == "image/gif" || $file['type'] == "image/png" 	))
	{
		$result = true;
		
	}
	
	elseif($file)
	{
	
		$etitulo .= "<li>La imagen no puede ser de tipo ".$file['type'] ." debe ser image/jpeg</li>";
		$fok = false;
	}

	
	return $result;
	
}


function sinocontrol($nombre,$valor)
{
	$si = ($valor=="Si")?"checked":"";
	$no = ($valor=="No")?"checked":"";
	$ot = ($valor=="N/A")?"checked":"";
	echo "<span>
			<label><input {$si} type='radio' name='{$nombre}' value='Si'/>Si</label>
			<label><input {$no} type='radio' name='{$nombre}' value='No'/>No</label>
			<input {$ot} type='radio' style='visibility:hidden' name='{$nombre}' value='N/A'/>
		</span>";
}

function hcombo($nombre, $hora="")
{
	
}


function fcombo($nombre, $fecha="")
{
	$f = strtotime($fecha);
	$ya = date("Y",time());
	$vd = "";
	$vm = "";
	$vy = $ya;
	if($fecha > 0)
	{
		$vd = date("d",$f);
		$vm = date("m",$f);
		$vy = date("Y",$f);
	}
	
	

	$ddata = array("val"=>array(),"txt"=>array());
	for($x=1; $x<32; $x++){ $ddata["val"][] = $x;$ddata["txt"][] = $x;}
	$td = new comboBox("{$nombre}D", new dataTable($ddata));
	$td->class = "requerido";
	$td->setValue($vd);
	$td->display();
	$d = array("val"=>array(),"txt"=>array());
	global $enumMeses;
	foreach($enumMeses as $k=>$v)
	{
		$d["val"][]=$k;
		$d["txt"][]=$v;
	}
	$m =  new comboBox("{$nombre}M", new dataTable($d));
	$m->class = "requerido";
	$m->setValue($vm);
	$m->display();
	
	
	$d = array("val"=>array(),"txt"=>array());
	for($x=$ya; $x < END_YEAR; $x++)
	{
		$d["val"][]=$x;
		$d["txt"][]=$x;
	}
	
	$y =  new comboBox("{$nombre}Y", new dataTable($d));
	$y->class = "requerido";
	$y->setValue($vy);
	$y->display();
}

function login($usuario,$clave)
{
	

	$resultado = false;
	
	$sql = "select * from sq_usuarios where usuario='{$usuario}' and clave=md5('{$clave}')";
	$rs = asgMng::query($sql);
	

	if(mysqli_num_rows($rs) > 0)
	{
		$resultado = true;
		
		$row = mysqli_fetch_assoc($rs);
		
		$computadora = $_SERVER["HTTP_HOST"];
		$ip = $_SERVER["REMOTE_ADDR"];
		
		$sql = "INSERT sq_sesion 
			(usuario, inicio, fin, computadora, ip)
			VALUES('{$usuario}', current_timestamp(), current_timestamp(), '{$computadora}', '{$ip}')";
		asgMng::query($sql);
		$cod = mysqli_insert_id(asgMng::getCon());
		$_SESSION[USER_SESSION] = $row;
		$_SESSION[USER_SESSION]["roles"]  = array();
		$rols = asgMng::query("select rol from sq_roles_user where usuario = '$usuario'");
		$_SESSION[USER_SESSION]['sid'] = $cod;
		while($rowf=mysqli_fetch_array($rols))
		{
			$_SESSION[USER_SESSION]["roles"][] = $rowf[0];
		}
		
		$sql = "update sq_usuarios set sesion='{$cod}' where usuario = '{$usuario}'";
		asgMng::query($sql);
	}

	return $resultado;
}

function cerrarSesion()
{
	//Si existe la sesion  lo registraos en la base de datos como salida
	if(isset($_SESSION[USER_SESSION]))
	{
		$fin = time();
		$sid = $_SESSION[USER_SESSION]["sid"];
		$sql = "update sq_sesion set fin=current_timestamp() where cod = '{$sid}'";
		asgMng::query($sql);
	}
}

function sesionEsDiferente($sid)
{
	define('SESION_ID_MN',$sid);
	$diferente = true;
	$usuario = $_SESSION[USER_SESSION]["usuario"];
	
	$sql = "select sesion from sq_usuarios where usuario = '{$usuario}'";
	
	$rs = asgMng::query($sql);

	
	$row = mysqli_fetch_row($rs);
	
	$did = $row[0];
	
	if($sid == $did)
	{
		$diferente = false;
	}
	return $diferente;
}


function verificarUsuario()
{
	if(!isset($_SESSION['KajeetUser']))
	{
		echo "<script language='javascript'>window.location=\"login.php\"; </script>";
		exit();
	}
}

