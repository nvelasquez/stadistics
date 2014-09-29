<?php
	//Utilerias para diferentes fines (Funciones) - Inciada por Amadis Suarez
	
	//muestra un codigo javascript con su tag de escript
	function escribirJavaScript($contenido)
	{
		echo "<script language=\"javascript\">
		";
		echo "alert(4);";
		echo "</script>";
	
	}
	
	function nombreDelDia($n){
	
		$dias = array();
		$dia[0] =  "Domingo";
		$dia[1] =  "Lunes";
		$dia[2] =  "Martes";
		$dia[3] =  "Miercoles";
		$dia[4] =  "Jueves";
		$dia[5] =  "Viernes";
		$dia[6] =  "Sabado";
		return $dia[$n];
	}
	
	
	
	function nombreDelMes($n)
	{
		$sql = "select * from af_meses where cod=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[1];
	}
	
	function nombreDelFondo($n)
	{
		$sql = "select titulo from af_mbanco where fondo=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[0];
	}
	
	function nombreCatSuplidor($n)
	{
		$sql = "select det from am_csup where cod=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[0];
	}
	
	function nombreDelaNomina($n)
	{
		$sql = "select det from af_mcnom where cod=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[0];
	}
	
	function nombreDelDepto($n)
	{
		$sql = "select det from af_mdto where cod=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[0];
	}
	
	function fondoDelaNomina($n)
	{
		$sql = "select fdo from af_mcnom where cod=$n";
		$row = mysql_fetch_row(mysql_query($sql));
		return $row[0];
	}
	
	

	function enviarEmail($destinatario, $asunto, $cuerpo)
	{
		require_once 'phpMailer/class.phpmailer.php';

		$mail = new PHPMailer ();

		$mail -> From = "supportfwipi@gmail.com";
		$mail -> FromName = "Soporte Fwipi";
		$mail -> AddAddress($destinatario);
		$mail -> Subject = $asunto;
		$mail -> Body = $cuerpo;
		$mail -> IsHTML(true);

		$mail->IsSMTP();
		$mail->Host = 'ssl://smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->Username = 'supportfwipi@gmail.com';
		$mail->Password = 'fwipi2405';

		return $mail->Send();
		
		/*
		if(!$mail->Send()) {
			echo 'Error: ' . $mail->ErrorInfo;
			
		}
		else 
		{	
			echo 'Mail enviado!';
		}
		*/
	
	
	}
	
	function mensajeDeAlerta($mensaje)
	{
		$rs = "<div class='ui-state-highlight ui-corner-all quitameAlPaso' style='padding: 0pt 0.7em; '><p><span class='ui-icon ui-icon-info' style='float: left; margin-right: 0.3em;'/>$mensaje</p></div><script language='javascript'>
		
		setTimeout(function() {
      $('.quitameAlPaso').hide('slow');
}, 2000);
		
		
		</script>";
		return $rs;
	}
	
	function mensajeDeError($mensaje)
	{
		$rs = "<div class='ui-state-error  ui-corner-all quitameAlPaso' style='padding: 0pt 0.7em; '><p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: 0.3em;'/>$mensaje</p></div><script language='javascript'>
		
		setTimeout(function() {
      $('.quitameAlPaso').hide('slow');
}, 2000);
		
		
		</script>";
		return $rs;
	}
	
	function activarBoton($boton)
	{
	echo "	<script language='javascript'>
			document.getElementById('$boton').disabled=false;
		</script>";
	}
	
	function ultimo_dia($mes,$ano)
    {
        return strftime("%d", mktime(0, 0, 0, $mes+1, 0, $ano));
    }

   function fechaconc($mes,$ano)
   {
      
       $fecha = strtotime("$ano-$mes-".ultimo_dia($mes,$ano));
       $fecha = strtotime("+1 days",$fecha);
       return date("Y-m-d", $fecha);
   }
	
	
	function datoconcilia($mes,$ano)
	{
		
			$conc = $ano.'-'.$mes;
	
		return $conc;
	}
	
	function fechasql($fecha)
	{
		$fechasql = substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2);
		return $fechasql;
	}
	
	function fechareporte($fecha)
	{
	
		$fechareporte = substr($fecha,8,2).'-'.substr($fecha,5,2).'-'.substr($fecha,0,4);
		return $fechareporte;
	
	}
	
	

?>
