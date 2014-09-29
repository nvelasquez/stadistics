<?php
class sfacil
{
	private $campos;
	public $tgID;
	public $tableName;
	private $tablaMaestra;
	
	public $tracknum;
	public $ani;
	public $dnis;
	public $agent_id;
	
	private $ordenData; //Arreglo con el orden de los campos
	
	
	function __construct($tabla, $campos)
	{
		$this->tablaMaestra = "sfacil"; //Donde se guardara todo.
		$this->campos = $campos;
		
		$this->tgID=0;
		$this->tableName = $tabla;
		foreach($this->campos as $campo)
		{
			$this->$campo = "";
		}
	}
	
	
	function __call($metodo, $params)
	{
		echo "el metodo {$metodo} no esta en esta clase";
	}
	
	function traduceSql($sql)
	{
		foreach($this->campos as $n=>$campo)
		{
			$sql = str_replace ( "x.{$campo}", "{$this->tablaMaestra}.valor{$n}" , $sql);
		}
		return $sql;
	}
	
	function guardar()
	{
		$diccionario = implode('|',$this->campos); 
	
		if($this->tgID > 0)
		{
			$values = array();
			foreach($this->campos as $n=>$campo)
			{
				$key = array_search($campo, $this->ordenData);
				if(!($key===false))
				{
					$nc = "valor{$key}";
					$values[] = "{$nc}='{$this->$campo}'";
				}
			}
			$values = implode(',',$values);
			$values = (strlen($values) < 2)?"":",{$values}";
			$sql = "update {$this->tablaMaestra} set diccionario='{$diccionario}'{$values} where tgID='{$this->tgID}'";
			asgMng::query($sql);
		}
		else
		{
			$nc = array();
			$values = array();
			
			$nc=array();
			foreach($this->campos as $n=>$campo)
			{
				
				$key = array_search($campo, $this->ordenData);
				if(!($key===false)){
					$nc[$n]="valor{$key}";
					$values[$nc[$n]] = "'{$this->$campo}'";
				}
			}
			$nc = implode(',',$nc);
			$values= implode(',',$values);
			$nc = (strlen($nc) < 2)?"":",{$nc}";
			$values = (strlen($values) < 2)?"":",{$values}";
			$sql = "INSERT INTO {$this->tablaMaestra} (tabla,diccionario,tracknum,ani,dnis,agent_id {$nc}) values('{$this->tableName}','{$diccionario}','{$this->tracknum}','{$this->ani}','{$this->dnis}','{$this->agent_id}'{$values});";
			
			asgMng::query($sql);
			$this->tgID = mysqli_insert_id(asgMng::getCon());
		}
	}
	
	
	function cargar($ordenData)
	{
		$this->ordenData = $ordenData;
		$sql = "select * from {$this->tablaMaestra} where tgID='{$this->tgID}'";
		$rs = asgMng::query($sql);
		if(mysqli_num_rows($rs) > 0)
		{
			$fila = mysqli_fetch_assoc($rs);
			
			foreach($ordenData as $k=>$campo)
			{
				$this->$campo = $fila["valor{$k}"];
			}
			
		}
		mysqli_free_result($rs);
	}
	
	function obtenerPost($ordenData)
	{
		
		$this->ordenData = $ordenData; 
		$this->tgID = (isset($_POST['tgID']))?$_POST['tgID']:'';
		
		$this->tracknum = (isset($_REQUEST["tracknum"]))?$_REQUEST["tracknum"]:"123456789";
		$this->ani = (isset($_REQUEST["ani"]))?$_REQUEST["ani"]:"3054332275";
		$this->dnis = (isset($_REQUEST["dnis"]))?$_REQUEST["dnis"]:"8001234455";
		$this->agent_id = (isset($_REQUEST["AGENT_ID"]))?$_REQUEST["AGENT_ID"]:"1010";
		//&#191;
		foreach($this->campos as $n=>$campo)
		{
			
			$this->$campo = (isset($_POST[$campo]))?$_POST[$campo]:'';
		}
		
	}

}