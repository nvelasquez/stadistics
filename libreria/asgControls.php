<?php

interface control
{
	public function display();
	
	public function getName();
}

class dataTable
{
    var $data;
    var $numFields;
    var $campos;
    var $numRows;
	var $dtipo;
	
	var $rowActivo; //Son los registros marcados como activos
   
    public function __construct($obj)
    {
		$this->rowActivo = array();
		
		$this->dtipo="mysql";
        if(is_string($obj))
        {
			if($this->isMysql())
			{
				$dt = $this->executeMyQuery($obj);
				
				$this->parseMysqlData($dt);
			}
			else
			{
				$dt = $this->executeMsQuery($obj);
				$this->parseMssqlData($dt);
			}
        }
		else if(is_array($obj))
		{
			//Esta opcion parsea un array.
			/*
			$data["id"][0]=1;
			$data["id"][1]=2;
			$data["name"][0]='nombre';
			$data["name"][1]='nombre';
			*/
			$this->parseArrayData($obj);
		}
        else
        {
            $this->parseMysqlData($obj);
        }
        $this->rowAction = Array();
    }
	
	private function isMysql()
	{
		return ($this->dtipo == "mysql");
	}
   
    private function executeMyQuery($sql)
    {
        $dt = mysqli_query(asgMng::getCon(), $sql);
		echo mysqli_error(asgMng::getCon());
		
        return $dt;
    }
	
	private function executeMsQuery($sql)
    {
        $dt = mssql_query($sql);
        return $dt;
    }
	
	
	private function parseArrayData($dt)
	{
		$this->numFields = count($dt);
		$this->campos = Array();
        $this->data = Array();
		foreach($dt as $campo=>$valores)
		{
			
			$this->numRows = count($valores);
			$this->campos[] = $campo;
			foreach($valores as $valor)
			{
				$this->rowActivo[] = 1;
				$this->data[$campo][] = $valor;
			}
		}
	}
	
	
   
    private function parseMysqlData($dt)
    {
       
        $this->numFields = mysqli_num_fields($dt);
        $this->campos = Array();
        $this->data = Array();
        $this->numRows = mysqli_num_rows($dt);
		
		$fields = mysqli_fetch_fields($dt);
		$this->tipos = Array();
		foreach($fields as $fi => $campo)
		{
			$this->campos[] =$campo->name;
			$this->tipos = $campo->type;
			
		}
       
        while($row = mysqli_fetch_array($dt))
        {
			$this->rowActivo[] = 1;
            for($x=0; $x < $this->numFields; $x++)
            {
                $campo = "";
                switch($this->tipos[$x])
                {
                    case 10:
						$ttime = strtotime($row[$x]);
                        $campo = ($ttime=="")?"--":date("d/m/Y",strtotime($row[$x]));
                    break;
                   
                    default:
                        $campo = $row[$x];
                    break;
                }
                $this->data[$this->campos[$x]][] = $campo;
            }
        }
    }
    
	
	private function parseMssqlData($dt)
    {
       
        $this->numFields = mssql_num_fields($dt);
        $this->campos = Array();
        $this->data = Array();
        $this->numRows = mssql_num_rows($dt);
        for($x=0; $x < $this->numFields; $x++)
        {
            $this->campos[] = mssql_field_name($dt,$x);
        }
       
        while($row = mssql_fetch_array($dt))
        {
            for($x=0; $x < $this->numFields; $x++)
            {
                $campo = "";
                switch(mssql_field_type($dt, $x))
                {
                    case "date":
                        $campo = ($row[$x]=="")?"":date("d/m/Y",strtotime($row[$x]));
                    break;
                   
                    default:
                        $campo = $row[$x];
                    break;
                }
                $this->data[$this->campos[$x]][] = $campo;
            }
        }
    }
   
    function getData()
    {
        return $this->data;
    }
   
    function getNumRows()
    {
        return $this->numRows;
    }
   
    function getNumFields()
    {
        return $this->numFields;
    }
   
    function getRow($number, $indiceNumerico = true)
    {
        $result = Array();
        for($x=0; $x < $this->numFields; $x++)
        {
			
            $result[$this->campos[$x]] = $this->data[$this->campos[$x]][$number];
            if($indiceNumerico)
			{
				$result[$x] = $this->data[$this->campos[$x]][$number];
			}
        }
        return $result;
    }
	
	function delRow($number)
	{
		for($x=0; $x < $this->numFields; $x++)
        {
            unset($this->data[$this->campos[$x]][$number]);
            
        }
		$this->numRows -= 1;
	}
}


class dataGrid implements control
{
    var $dataTable;
    var $rowAction = Array();//Accion a agregar a un row;
	var $eventFields = Array();//Cuantos campos seran usado en funciones
	var $width;
	var $pdfHeader;
	var $titulo;
	var $labels;
	var $anchos;
	
	
	var $noVisibles;
	var $paginar;
	
	
	public function getName()
	{
		return "";
		
	}
	
    public function __construct($dt)
    {
        $this->dataTable = $dt ;
		$this->width = "100%";
		$this->pdfHeader = "";
		$this->titulo = "Resultado de la consulta";
		$this->labels = array();
		$this->anchos = array();
		$this->noVisibles = array();
		$this->paginar = false;
		foreach($dt->campos as $campo)
		{
			$this->labels[$campo] = $campo;
			$this->anchos[$campo] = "auto";
		}
    }
   
   	public function cambiarLabel($campo, $valor)
	{
		$this->labels[$campo] = $valor;
	}
   
   
    public function setRowAction($event, $action,$parametro)
    {
        $x = count($this->rowAction);
        $this->rowAction[$x]["event"] = $event;
        $this->rowAction[$x]["action"] = $action;
		$this->rowAction[$x]["parametro"] = $parametro;
    }
   
   
    public function setFieldAction($event,$posicion,$action ,$parametros, $clase)
    {
        $p = implode(", ", $parametros);
        $x = count($this->rowAction);
		$xe = count($this->eventFields);
		$this->eventFields[$xe] = $parametros;
        $this->rowAction[$x]["destino"] = $posicion;
        $this->rowAction[$x]["event"] = $event;
        $this->rowAction[$x]["action"] = $action;
        $this->rowAction[$x]["class"] = $clase;
		$this->rowAction[$x]["parametro"] = $parametros;
    }
	function word()
	{
		header('Content-type: application/vnd.ms-word');
		header("Content-Disposition: attachment; filename=archivo.doc");
		header("Pragma: no-cache");
		header("Expires: 0");
		$this->display();
	}
	
	function excel()
	{
		
			header('Content-type: application/vnd.ms-excel');
			header("Content-Disposition: attachment; filename=Reporte.xls");
			header("Pragma: no-cache");
			header("Expires: 0");
			
		
			$this->display();
		
	}
		//Pdf en BETA
	function pdf()
	{
		require_once("dompdf/dompdf_config.inc.php");
		$fecha = date("d/m/Y");
		$html = "<html>
					<head>
						<style>
							body
							{
								margin:10px;	
							}
						</style>
					</head>
					<body>
					<div align='right'>
						$fecha
					</div>
					$this->pdfHeader
					<center>
						<h3>Datos de la Consulta.</h3>
					</center>
					";
		$html .=	$this->display(true);
				$html .=	"</body></html>";
		
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
			
		$dompdf->stream("Reporte.pdf");

			
		
			//$this->display();
	}
    function display($retorno = false)
    {
		$salida = "
		<style>
			
			tr.gridASG
			{
				cursor:pointer;
			}
			
				.luminicos:hover
				{
					background:none;
					background:#D5D9FD;
					background-color:#D5D9FD;
					
				}
				
				.alternoTR
				{
					background: none repeat scroll 0 0 #F1F5FA;	
				}
			
		</style>
		";
        $salida .= "<div class='divGrid'>";
        $salida .= "<table id='gridDT' class='' style='width:$this->width;	font-family: Verdana, Arial, Helvetica, sans-serif;
	border-collapse: collapse;
	border-left: 1px solid #ccc;
	border-top: 1px solid #ccc; 
	color: #333;
	background:#FFF;
'><thead>";
	
        $salida .= "<tr style='	text-transform: capitalize;
	background: #C6CFFD;
	color: #000;
	font-weight: bold;


'>";
        for($x=0; $x < $this->dataTable->getNumFields(); $x++)
        {
            $campo = $this->dataTable->campos[$x];
			$valor = $this->labels[$campo];
			
			$ancho = $this->anchos[$campo];
			
			//Si el campo no esta en los marcados como invisibles se mostrara
			if(!in_array($campo,$this->noVisibles))
			{
           		$salida .= "<th style='border-right:white 1px solid; width:$ancho'>$valor</th>";
        	}
		}
       $salida .= "
	   <td style='width:16px;' >
		<img style='width:16px;cursor:pointer' alt='E' title='Exportar Datos a Excel' src='images/excel.jpg' onClick='exportarGridASG(this.parentNode.parentNode.parentNode.parentNode.parentNode);'/>
	   </td>
	   </tr></thead><tbody style=''>";
		$color = true;
        for($x=0; $x<$this->dataTable->getNumRows(); $x++)
        {
			$activo = $this->dataTable->rowActivo[$x];
			
            $row = $this->dataTable->getRow($x);
			$act = $this->rowAct($row);
			$alt = ($color)?"alternoTR":"";
			
			//Para marcar las filas con un color de que no estan activas
			$alt = ($activo > 0)?$alt:"background:#FFE1E1;";
			
            $salida .= "<tr class='gridASG luminicos $alt' $act style='border-right: 1px solid #ccc;
				border-bottom: 1px solid #ccc;
				padding: 5px;
				line-height: 1.8em;
				font-size: 0.8em;
				vertical-align: top; $alt' 
			>";
			$color = !$color;
			
            for($y=0; $y < $this->dataTable->getNumFields(); $y++)
            {
				$campo = $this->dataTable->campos[$y];
				if(!in_array($campo,$this->noVisibles))
				{
					$salida .= "<td style='border-left: 1px dotted #369;'>";
						$salida .= $row[$y];
					$salida .= "</td>";
				}
            }
            $salida .= "</tr>";
        }
        $salida .= "</tbody></table>";
		
		if($this->paginar)
		{
			$salida .= "
			
				<script type='text/javascript'>
					$('#gridDT').dataTable({
						'bStateSave': true
					});
				</script>
			
			";
		}
		
        if($this->dataTable->getNumRows() == 0)
        {
            $salida .= "<div>No hay datos que mostrar</div>";
        }
        $salida .= "</div>";
		if($retorno)
		{
			return $salida;	
		}
		echo $salida;
    }
	
	function rowAct($row)
	{
		$acciones = Array();
        for($x=0; $x<count($this->rowAction); $x++)
        { 
			if(is_array($this->rowAction[$x]["parametro"]))
			{
				for($y = 0; $y < count($this->rowAction[$x]["parametro"]); $y++)
				{
					$parametros[] = "'".$row[$this->rowAction[$x]["parametro"][$y]]."'";
				}
				$p = implode(", ", $parametros);
				
				$act = $this->rowAction[$x]["action"]."(".$p.")";;
				$acciones[] = $this->rowAction[$x]["event"] . "=\"". $act.";\""; 
			}
			else
			{
				$acciones[] = $this->rowAction[$x]["event"] . "=\"". $this->rowAction[$x]["action"]."\""; 
			}
		}
        $act = implode(" ", $acciones);
		
		return $act;
	}
}

class label implements control 
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	
	function __construct($text = "")
	{
		
		$this->left = 20;
		$this->top = 30;
		$this->width = 60;
		$this->height = 30;
		$this->text = $text;
		$this->fontSize = 14;
	}
	 
	function display()
	{
		$style = "position:absolute; top:$this->top; left:$this->left; width:$this->width; height:$this->height; font-size:$this->fontSize";
		echo "<label id='$this->id' style='$style' type='text'>$this->text</label>";
	}
	
	
	function getName()
	{
		return $this->id;
		
		
	}
	
}

class checkBox implements control
{
	var $id;
	var $valor;
	var $type;

	function getName()
	{
		
	
	}
	
	function setValue($valor)
	{
		$this->valor = $valor;
	}
	
	
	function getValue()
	{
		return $this->valor;
		
	}
	
	function display()
	{
		
		
		echo $this->__toString();
	}
	
	function __construct($id)
	{
		$this->id = $id;
		$this->valor = 0;
		$this->type = "checkbox";
		
	}
	
	
	function __toString()
	{
	
		$check = ($this->valor > 0)?" checked ": "";
	
		$rs = "<input $check value='1' type='checkbox' name='$this->id' id='$this->id'/>";
		return $rs;
		
		
		
	
	}

}




class comboBox implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $dataSource;
	var $accion;
	var $selectValue;
	var $class;
	var $isnull;
	var $type;
	var $visibility;
	
	function setValue($valor)
	{
		$this->selectValue = $valor;
	}
	
	
	function getValue()
	{
		return $this->selectValue;
		
	}
	
	function __construct($id="",$data=null)
	{
		$this->id = $id;
		$this->dataSource = $data;
		$this->selectValue = "";
		$this->text = "";
		$this->accion = "";
		$this->class ="";
		$this->isnull=false;
		$this->type = "combo";
		$this->visibility = "visible";
	}
	
	function display()
	{
		
		
		echo $this->__toString();
	}
	
	
	function __toString()
	{
		$control = "";
		 
		$this->selectValue = (isset($_POST[$this->id]) && $this->selectValue == "")?$_POST[$this->id]:$this->selectValue;
		$nuleable = ($this->isnull)?"":"visibility:hidden; height:1px;";
		$control .= "<select class='$this->class' $this->accion name='$this->id' id='$this->id' style='visibility:$this->visibility; width:{$this->width}px;' >";
		$control .= "<option style='$nuleable' value=''>{$this->text}";
			
			$control .= "</option>";
		for($x=0; $x < $this->dataSource->getNumRows(); $x++)
		{
			$row = $this->dataSource->getRow($x);
			$title = '';
			
			if(isset($row[2]))
			{
				$title = $row[2];
				
			}
			
			$selected = ($row[0] == $this->selectValue)?" selected ":"";
			
			$control .= "<option title='{$title}' $selected  value='$row[0]'>";
			$control .= $row[1];
			$control .= "</option>";
		}
		
		$control .= "</select>";
		return $control;
	}
	
	function getName()
	{
		return $this->id;	
	}
}

/*$arrOptGroup = array(
					"Efectivo" => new dataTable("select id, nombre from rcl_contactos_disposiciones where activo > 0 and efectivo = 1"),
					"No Efectivo" => new dataTable("select id, nombre from rcl_contactos_disposiciones where activo > 0 and efectivo = 0") );
*/
class comboBoxOptGroup implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $dataSource;
	var $accion;
	var $selectValue;
	var $class;
	var $isnull;
	var $type;
	var $visibility;
	var $data;
	
	function setValue($valor)
	{
		$this->selectValue = $valor;
	}
	
	
	function getValue()
	{
		return $this->selectValue;
		
	}
	
	function __construct($id="",$arr = array())
	{
		$this->id = $id;
		$this->selectValue = "";
		$this->text = "";
		$this->accion = "";
		$this->class ="";
		$this->isnull=false;
		$this->type = "combo";
		$this->visibility = "visible";
		$this->data = $arr;
	}
	
	function display()
	{
		
		
		echo $this->__toString();
	}
	
	
	function __toString()
	{
		$control = "";
		 
		$this->selectValue = (isset($_POST[$this->id]) && $this->selectValue == "")?$_POST[$this->id]:$this->selectValue;
		$nuleable = ($this->isnull)?"":"visibility:hidden; height:1px;";
		$control .= "<select class='$this->class' $this->accion name='$this->id' id='$this->id' style='visibility:$this->visibility; width:{$this->width}px;' >";
		$control .= "<option style='$nuleable' value=''>{$this->text}";
		$control .= "</option>";
		foreach ($this->data as $key => $value) {
				
			$control .= "<optgroup label = '$key'>";
			for($x=0; $x < $value->getNumRows(); $x++)
			{
				$row = $value->getRow($x);
				$title = '';
				
				if(isset($row[2]))
				{
					$title = $row[2];
					
				}
				
				$selected = ($row[0] == $this->selectValue)?" selected ":"";
				
				$control .= "<option title='{$title}' $selected  value='$row[0]'>";
				$control .= $row[1];
				$control .= "</option>";
			}
			$control .= "</optgroup>";
		}


		
		$control .= "</select>";

		return $control;
	}
	
	function getName()
	{
		return $this->id;	
	}
}

class EditorBox implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $readonly;
	var $class;
	var $alFinal;
	
	function __construct($id="")
	{
		$this->id = $id;
		$this->left = 20;
		$this->top = 30;
		$this->width = "600px";
		$this->height = 25;
		$this->fontSize = 14;
		$this->text = "";
		$this->type = "text";
		$this->readonly = "";
		$this->class = "";
		$this->alFinal = "";
	}
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		return "
			
			<textarea id='$this->id' name='$this->id' cols='70' >$this->text</textarea>
		
				<script language='javascript'>
					
						// O2k7 skin (silver)
						tinyMCE.init({
							// General options
							mode : 'exact',
							elements : '$this->id',
							theme : 'advanced',
							skin : 'o2k7',
							skin_variant : 'silver',
							plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups',

							// Theme options
							theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
							theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
							theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
							theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
							theme_advanced_toolbar_location : 'top',
							theme_advanced_toolbar_align : 'left',
							theme_advanced_statusbar_location : 'bottom',
							theme_advanced_resizing : true,

							// Example content CSS (should be your site CSS)
							content_css : 'css/content.css',

							// Drop lists for link/image/media/template dialogs
							template_external_list_url : 'lists/template_list.js',
							external_link_list_url : 'lists/link_list.js',
							external_image_list_url : 'lists/image_list.js',
							media_external_list_url : 'lists/media_list.js',

							// Replace values for the template plugin
							template_replace_values : {
								username : 'Some User',
								staffid : '991234'
							}
						});
					
					
				</script>
		";
	}
	
	function getName()
	{
		return $this->id;	
	}

}

class textBox implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $readonly;
	var $class;
	var $alFinal;
	var $placeholder;
	var $title;
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function __construct($id="")
	{
		$this->id = $id;
		$this->left = 20;
		$this->top = 30;
		$this->width = "250";
		$this->height = 25;
		$this->fontSize = 14;
		$this->text = "";
		$this->type = "text";
		$this->readonly = "";
		$this->class = "";
		$this->alFinal = "";
		$this->title = "";
	}
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		$this->text = (isset($_POST[$this->id]) && $this->text == "")?$_POST[$this->id]:$this->text;
		$style = "width:{$this->width}px";
		return  "<input class='$this->class' title='$this->title' placeholder='{$this->placeholder}' $this->readonly name='$this->id' id='$this->id' style='$style' value='$this->text' type='$this->type'>$this->alFinal";
		
	}
		
	function getName()
	{
		return $this->id;	
	}
	
}


class textImage implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $readonly;
	var $class;
	var $alFinal;
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function __construct($id="")
	{
		$this->id = $id;
		$this->left = 20;
		$this->top = 30;
		$this->width = "250";
		$this->height = 25;
		$this->fontSize = 14;
		$this->text = "";
		$this->type = "text";
		$this->readonly = "";
		$this->class = "";
		$this->alFinal = "";
	}
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		$this->text = (isset($_POST[$this->id]) && $this->text == "")?$_POST[$this->id]:$this->text;
		$style = "width:{$this->width}px";
		return  "<input class='$this->class' onfocus='buscarImg(this);' $this->readonly name='$this->id' id='$this->id' style='$style' value='$this->text' type='text'>
		<button type='button' onclick=\"buscarImg(document.getElementById('$this->id'));\"><--></button></br>
		<img id='spparaimagen{$this->id}' width='50'/>
		$this->alFinal
			<script language='javascript'>
				function elegirImagenDbl(cod)
				{
					
					obj.value = cod;
					document.getElementById('spparaimagen{$this->id}').src = 'files/'+cod+'.jpg';
					$('#divSiteFoto1').dialog('close');
				}
				
				function elegirImagenASG()
			{
				abrirOpcion('divSiteFoto1','Selecciona una imagen','modulos/admin/siteimgs.php',true,800,600);	
			}
	
			var obj = null		
			
			function buscarImg(control)
			{
				obj = control;
				elegirImagenASG();
			}
			
			if(document.getElementById('{$this->id}').value > 0)
			{
				cod = document.getElementById('{$this->id}').value;
				document.getElementById('spparaimagen{$this->id}').src = 'files/'+cod+'.jpg';	
			}
			else
			{
				document.getElementById('spparaimagen{$this->id}').src = 'images/nofoto.jpg';
			}
			
			</script>
		";
		
	}
		
	function getName()
	{
		return $this->id;	
	}
	
}



class CoordBox implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $readonly;
	var $class;
	var $alFinal;
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function __construct($id="")
	{
		$this->id = $id;
		$this->left = 20;
		$this->top = 30;
		$this->width = "";
		$this->height = 25;
		$this->fontSize = 14;
		$this->text = "";
		$this->type = "text";
		$this->readonly = "";
		$this->class = "";
		$this->alFinal = "";
	}
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		$this->text = (isset($_POST[$this->id]) && $this->text == "")?$_POST[$this->id]:$this->text;
		$style = "width:{$this->width}px";
		$this->alFinal = "<button type='button' class='btnGeo' onclick='abrirLoc(document.getElementById(\"{$this->id}\"));'></button>";
		return  "<input class='$this->class' $this->readonly name='$this->id' id='$this->id' style='$style' value='$this->text' type='$this->type'>$this->alFinal
		<script language='javascript'>
			var objCoords;
			function abrirLoc(obj)
			{
				objCoords = obj;
				abrirOpcion('divWinSec5555','Definir Sector', 'modulos/georef/marcarSector.php',true,800, 600);
				
			}
			
			function registrarCorrds(cod)
			{
					\$('#divWinSec5555').dialog('close');
					
					objCoords.value = cod;
			}
		</script>
		";
		
	}
		
	function getName()
	{
		return $this->id;	
	}
	
}



class textArea implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $readonly;
	var $class;
	var $alFinal;
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function __construct($id="")
	{
		$this->id = $id;
		$this->left = 20;
		$this->top = 30;
		$this->width = "250";
		$this->height = 100;
		$this->fontSize = 14;
		$this->text = "";
		$this->type = "text";
		$this->readonly = "";
		$this->class = "";
		$this->alFinal = "";
	}
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		$this->text = (isset($_POST[$this->id]) && $this->text == "")?$_POST[$this->id]:$this->text;
		$style = "width:{$this->width}px; height:{$this->height}px";
		return  "<textarea class='$this->class' $this->readonly name='$this->id' id='$this->id' style='$style'>$this->text</textarea>$this->alFinal";
		
	}
		
	function getName()
	{
		return $this->id;	
	}
	
}




class image implements control
{
	var $id;
	var $text;
	var $left;
	var $top;
	var $width;
	var $height;
	var $eventos;
	var $url;
	var $zindex;
	
	function __construct($ruta)
	{
		$this->url = $ruta;
		$this->id = "";
		$this->text = "";
		$this->left = 20;
		$this->top = 30;
		$this->width = 60;
		$this->height = 30;
		$this->eventos = "";
		$this->zindex = "";
		
		//
	}
	
	function display()
	{
		$style = "position:absolute; $this->zindex top:$this->top; left:$this->left; width:$this->width; height:$this->height";
		echo "<img style='$style' alt='' src='../$this->url'>";
		
	}
	
	function getName()
	{
		return $this->id;
		
	}
}

class button implements control 
{
	var $id;
	var $texto;
	var $left;
	var $top;
	var $width;
	var $height;
	var $eventos;
	var $zindex;
	
	var $type;
	function __construct($id="")
	{
		$this->id = $id;
		$this->texto = "";
		$this->type = "submit";
		$this->left = 20;
		$this->top = 30;
		$this->width = 60;
		$this->height = 30;
		$this->eventos = "";
		$this->zindex = "";
		
		//
	}
	
	
	function display()
	{
		$style = "position:absolute; $this->zindex  top:$this->top; left:$this->left; width:$this->width; height:$this->height";
		echo "<button style='$style' $this->eventos type='$this->type' name='$this->id'>$this->texto</button>";
	}
	
	function getName()
	{
		return $this->id;
		
	}
	
}

class manejadorArchivos implements control 
{
	var $type;
	var $tabla;
	var $primario;
	function __construct($tabla, $primario)
	{
		$this->type = "archivo";
		$this->tabla = $tabla;
		$this->primario = $primario;
	}
	
	function getName()
	{
		
	}
	
	function setValue()
	{
		
		
	}
	
	function getValue()
	{
		return "";
		
	}
	
	function __toString()
	{
		$rs = "<div id='divFiles$this->tabla'>";
			
		
		
		$rs .= "</div>";
		
//		$rs .= "<input type='image' src='images/agregardoc.png' onClick="agregarFile();" title = "Agregar Archivo" >
					
		$rs .= "<button onClick='agregarFile()' type='button'>Agregar</button>
			
			<script language='javascript'>
			function agregarFile()
			{
				idv = document.getElementById('txt$this->primario').value;
				
				if(idv > 0)
				{
					abrirOpcion('manejador$this->tabla','Agregar archivos', 'libreria/asgFiles.php?tabla=$this->tabla&registro='+idv, true);
				}
				else
				{
					alert('Debe crear el registro antes de agregar archivos');
				}
			}
			
			idv = document.getElementById('txt$this->primario').value;
			cargarEn('divFiles$this->tabla', 'libreria/asgFiles.php', 'tabla=$this->tabla&displayAsgFiles=sfdsf&registro='+idv);
			
			
			
			function cerrarFileManager()
			{
				$('#manejador$this->tabla').dialog('close');
				cargarEn('divFiles$this->tabla', 'libreria/asgFiles.php', 'tabla=$this->tabla&displayAsgFiles=sfdsf&registro='+idv);
			
			}
			
			function descargarFile(cod)
			{
				window.location = 'libreria/asgFiles.php?descargar='+cod;
			}
			
			</script>
			
		";
		
		return $rs;
		
	}
	
	function display()
	{
		
		echo $this->__toString();
	}
	
}

class panel implements control
{
	var $controles;
	var $left;
	var $top;
	var $width;
	var $height;
	var $id;
	var $zindex = "";
	
	function setPosition($left, $top)
	{
		$this->left = $left;
		$this->top = $top;
	}
	
	function display()
	{
		$class = "";//"border:1px solid red;";

			$class.= "z-index:1000; 
       margin-left: auto;
       margin-right: auto;
       left: 0px;
       right: 0px;
       width: 800px;
	   top: {$this->top}px;
       height: {$this->height}px;
       background-color: white;
       border: none";

		echo "<div style='$class'>";
		foreach($this->controles as $control)
		{
			$control->display();
		}
		echo "</div>";
	}
	
	function __construct()
	{
		$this->controles = Array();
		$this->left = 20;
		$this->top = 30;
		$this->width = 60;
		$this->height = 30;
	}
	
	function addControl($control)
	{
		$this->controles[count($this->controles)] = $control;
	}
	
	function getName()
	{
		return $this->id;
		
	}

}



class form implements control 
{
	var $controles;
	var $left;
	var $top;
	var $width;
	var $height;
	var $id;
	function display()
	{
		$style = "position:absolute;  margin-left: auto ; margin-right: auto ; width:800px; height:$this->height;";
		echo "<form method='POST' style='$style' autocomplete ='off'>";
		foreach($this->controles as $control)
		{
		
			if(isset($_POST[$control->getName()]))
			{
				$control->text = $_POST[$control->getName()];
			}
			$control->display();
		}
		echo "</form>";
	}
	
	function __construct()
	{
		$this->top = 20;
		$this->left = 100;
		$this->height = 100;
		$this->width = 300;
		$this->controles = array();
		
	}
	
	function addControl($control)
	{
		$this->controles[count($this->controles)] = $control;
	}
	
	function getName()
	{
		return $this->id;
		
	}
}

class pagina implements control 
{
	var $id;
	var $controles;
	var $text;
	var $javascript;
	var $height;
	function getName()
	{
		return $this->id;
		
	}
	
	function __construct()
	{

		$this->controles = array();
		$javascript="";
	}
	
	function addControl($control)
	{
		$this->controles[count($this->controles)] = $control;
	}
	
	function display()
	{
		echo "
		<html>
		<head>
		<title>Administration Panel - Picshots v1.0</title>
		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />

		<link rel='stylesheet' type='text/css' href='http://www.tramil.net/fototeca/css/admin_style.css'>

		
		<script language=Javascript>
		<!--
		function rtclk(e) 
		{
			if (document.all && !document.getElementById) {if (event.button == 2) return false;} else if (document.layers) {if (e.which == 2 || e.which == 3) {return false;}}} if (document.layers) document.captureEvents(Event.MOUSEDOWN);document.onmousedown = rtclk;document.oncontextmenu = function() {return false;}
			
			$this->javascript
			
		//-->
		</script>
		<style type='text/css'>
		
		<!--
		body {
			background-color: #333333;
		}
		-->
		</style>
		</head>
		<body>
		<table width='830' border='0' align='center' cellpadding='0' cellspacing='0' >
		  <tr>
			<td colspan='3'><img src='http://www.tramil.net/fototeca/images/border_top.jpg' width='830' height='79' /></td>
		  </tr>
		  <tr>
			<td width='14' background='http://www.tramil.net/fototeca/images/border_left.jpg'>&nbsp;</td>
			<td width='767' bgcolor='#FFFFFF' valign='top' height='500'><br><br><br><br><br><br><br>";
		  foreach($this->controles as $control)
		{
			$control->display();
		}
		echo " </td>
			<td width='14' background='http://www.tramil.net/fototeca/images/border_right.jpg'>&nbsp;</td>
		  </tr>
		  <tr>
			<td colspan='3'><img src='http://www.tramil.net/fototeca/images/border_bottom.jpg' width='830' height='44'  />
			
			</td>
		  </tr>
		</table>
		<center>
		";
		
		echo "</center></body>\n";
		echo "</html>";
	}
	
	
}

class mesageBox
{
	var $mensaje;
	function display()
	{
	
		echo "
			<script languaje ='javascript'>
				alert(\"$this->mensaje\");
			</script>
				";
	}
}

function getRow($dt, $n =0)
{
	return $dt->getRow($n);
}


class bar implements control
{
	var $id;
	var $titulo;
	var $width;
	var $height;
	var $valor;
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function getName()
	{
	
	}
	
	function __construct($id, $titulo, $valor)
	{
		$this->id = $id;
		$this->width = 150;
		$this->height = 10;
		$this->titulo = $titulo;
		$this->elementos = Array();
		$this->valor = $valor;
	}
	
	function display()
	{
		echo "<table>";
		echo $this->__toString();
		echo "</table>";
	}
	
	
	function __toString()
	{
		 $rs = "
			<tr>
				<td>
					{$this->titulo}
						
				</td>
				<td><div id='{$this->id}' style='width:{$this->width}px; height:15px'></div>
				
				<div style='width: {$this->width}px; height: 15px;'  class='ui-progressbar ui-widget ui-widget-content ui-corner-all' role='progressbar' aria-valuemin='0' aria-valuemax='100' aria-valuenow='100'><div class='ui-progressbar-value ui-widget-header ui-corner-left ui-corner-right' style='width: {$this->valor}%;'/></div>
				
				<td/>
				<td>{$this->valor}%<td/>
			</tr>
			
			";
		return $rs;
	}
}

class ninguno implements control
{
	var $texto;
	var $type;
	
	function __construct($valor="")
	{
		$this->texto = $valor;
		$this->type = "especial";
	}
	
	function display()
	{
		echo $this->texto;
	}
	
	function __toString()
	{
		return $this->texto;
	}
	
	function getValue()
	{
		return "";
	}
	
	function setValue()
	{
	
	}
	
	function getName()
	{
	
	}
}
class groupBar implements control
{
	var $id;
	var $titulo;
	var $width;
	var $height;
	var $fontSize;
	var $type;
	var $elementos;
	
	
	function setValue($valor)
	{
		$this->text = $valor;
	}
	
	function getValue()
	{
		return $this->text;
		
	}
	
	function __construct($id, $titulo)
	{
		$this->id = $id;
		$this->width = 200;
		$this->height = 10;
		$this->titulo = $titulo;
		$this->elementos = Array();

	}
	
	function addBar($nombre, $valor)
	{
		$n = count($this->elementos);
		$this->elementos[]= new bar("{$this->id}$n", $nombre, $valor);
	
	}
	
	
	function display()
	{
		echo $this->__toString();
	
	}
	
	function __toString()
	{
		$rs = "<fieldset class='ui-tabs ui-widget ui-widget-content ui-corner-all' style='width:{$this->width}px'>
				<legend class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>{$this->titulo}</legend>
			<table>
		
		";	

		$script = "";
		
		foreach($this->elementos as $posicion=>$elemento)
		{
			
			$rs .= $elemento;

			
				
			
		}
			$rs .="</table></fieldset>";
			return $rs;
	}
		
	function getName()
	{
		return $this->id;	
	}

	
}

function cambiarControl(&$control, $tipo, $datos=null)
{
	switch($tipo)
	{
		case "textArea":
		$control = new textArea($control->getName());
		break;
	}
}

function obtenerEnumeradores($tabla, $campo)
{
	$db = DB_NAME;
	
	$sql = "SELECT column_type FROM information_schema.`COLUMNS` WHERE `DATA_TYPE` = 'enum' AND table_schema = '{$db}' 
AND `TABLE_NAME` = '{$tabla}' AND `COLUMN_NAME` = '{$campo}'";	

	$rs = asgMng::query($sql);


	$dato = mysqli_fetch_row($rs);
	
	mysqli_free_result($rs);
	
	$dato = $dato[0];
	$dato = str_replace("enum('",'',$dato);
	$dato = str_replace("')",'',$dato);
	
	$datos = explode("','",$dato);
	

	$data = array('id'=>$datos,'name'=>$datos );

	
	$dt = new dataTable($data);
	
	return $dt;
	
	
}