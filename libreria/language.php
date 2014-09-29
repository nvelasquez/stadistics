<?php





$langEN = array(
	'Inicio' => 'Home',	
	'Acceso' => 'Login',
	'Usuario' => 'Username',
	'Clave' => 'Password',
	'Ingresar' => 'Login',
	'Usuario o Clave no validos'=>'Bad Username or Password'
				
);

if(isset($_GET['lang'])){
	$_SESSION['usrLanguage'] = $_GET['lang'];
}


function __e($m, $retorno = false)
{
	global $langEN;
	if(isset($_SESSION['usrLanguage']) && $_SESSION['usrLanguage']=='EN')
	{
		if(array_key_exists($m,$langEN))
		{
			$m = $langEN[$m];
		}
		
	}
	if($retorno)
	{
		return $m;
	}
	echo $m;
}

function __BarraIdioma()
{
	$url = $_SERVER['REQUEST_URI'];
	
	$selES = 'langSys';
	$selEN = '';
	if(isset($_SESSION['usrLanguage']) && $_SESSION['usrLanguage']=='EN')
	{
		$selES = '';
		$selEN = 'langSys';
	}
	$simbolo = ($_GET)?'&':'?';
	
	echo "
		<style>
			.langSys{ border:solid 1px red; background:black; }
		</style>
		
		<a  href='{$url}{$simbolo}lang=ES'><img class='{$selES}' height='20' src='images/Spain.ico'/></a>
		
		<a href='{$url}{$simbolo}lang=EN'><img class='{$selEN}' height='20' src='images/United-States.ico'/</a>
	";	
	

	

}


