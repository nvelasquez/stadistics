<?php


class usuario extends genclas
{
	function __construct($cod=0)
	{
		parent::__construct("usuarios",$cod);
	}
	
	function setClave($clave)
	{
		$this->clave = md5($clave);
		$this->guardar();
	}
}


class candidato extends genclas
{
	
	public $nombrecompleto;
	
	function __construct($cod=0)
	{
		parent::__construct("rcl_curriculum",$cod);
	}	
	
	function cargar(){
		
		parent::cargar();
		$this->nombrecompleto = $this->nombre .' '.$this->apellido;
	}
	
}


class grupo extends genclas
{
	
	
	private $candidatos;
	
	function __construct($cod=0)
	{
		$this->candidatos =array();
		parent::__construct("rcl_listas_candidatos",$cod);
	}	
	
	function agregarCandidato($cod)
	{
		$det = new genclas('rcl_listas_candidatos_detalle');
		$det->lista_id = $this->cod;
		$det->candidato = cod;
		$this->candidatos[] = $candidato;
	}
	
	function guardar($mostrarError = false){
		
		parent::guardar($mostrarError);
		foreach($this->candidatos as $det){
			$det->lista_id = $this->cod;
			$det->guardar();	
		}
	}
	
	function cargar(){
		parent::cargar();
		$sql = "select * from rcl_listas_candidatos_detalle where lista_id={$this->id}";
		
	}
	
}


class tck_mensajes extends genclas{
	
	public $asuntodb;
	private $Mdetalles;
	
	function __construct($cod=0)
	{
		$this->asuntodb = '';
		$this->Mdetalles = array();
		parent::__construct("tck_mensajes",$cod);
	}	
	
	function getComentarios()
	{
		$sql = "SELECT u.nombre as autor, FROM_UNIXTIME(md.fecha) as fecha, md.comentario, md.estado, md.prioridad 
		FROM `tck_mensajes_desarrollo` md left join sq_usuarios u on u.cod = md.usuario_id	
		WHERE mensaje_id = '{$this->id}'";	
		$dt = new dataTable($sql);
		return $dt;
	}
	
	function agregarDetalle($comentario, $estado, $prioridad)
	{
		$detalle = new genclas('tck_mensajes_desarrollo');
		$detalle->mensaje_id = $this->id;
		$detalle->usuario_id = $_SESSION['VIX_USER']['cod'];
		$detalle->fecha = time();
		$detalle->comentario = $comentario;
		$detalle->estado = "{$this->estado} -> $estado";
		$detalle->prioridad = "{$this->prioridad} -> $prioridad";
		$detalle->guardar();
		$this->Mdetalles[] = $detalle;
		$this->fecha_ultimaactividad = $detalle->fecha;
		$this->estado = $estado;
		$this->prioridad = $prioridad;
		
		if($this->fecha_primerarespuesta <= 0 )
		{
			$this->fecha_primerarespuesta = $detalle->fecha;
		}
		if($estado == "cerrado")
		{
			$this->fecha_cierre = $detalle->fecha;
		}
		$this->guardar();
		
	}
	
}

class entrevista extends genclas{

	function __construct($cod=0)
	{
	
		parent::__construct("rcl_entrevistas",$cod);
	}		
	
	
}

class sq_usuarios extends genclas{
	
	private $claveOriginal;
	
	public $usrroles;
	public $usrmodulos;
	public $usrproyectos;
	
	
	function __construct($cod=0)
	{
		$this->usrroles = array();
		$this->usrmodulos = array();
		$this->usrproyectos = array();
		parent::__construct("sq_usuarios",$cod);
	}	
	
	function agregarRol($rol)
	{
		
		$this->usrroles[] = $rol;
	}
	
	function agregarModulo($modulo){
		$this->usrmodulos[] = $modulo;
	}
	
	function agregarProyecto($proyecto){
		$this->usrproyectos[] = $proyecto;
	}
	
	function cargar()
	{
		parent::cargar();
		$this->claveOriginal = $this->clave;
		$this->clave = "";
		//Cargar los roles
		$sql ="SELECT * FROM `sq_roles_user` WHERE usuario = '{$this->cod}'";
		$rs = asgMng::query($sql);
		while($row = mysqli_fetch_assoc($rs))
		{
			$this->usrroles[] = $row['rol'];
		}
		mysqli_free_result($rs);
		
		//cargarLosModulos
		$sql ="SELECT * FROM `sq_modulos_user` WHERE usuario = '{$this->cod}'";
		$rs = asgMng::query($sql);
		while($row = mysqli_fetch_assoc($rs))
		{
			$this->usrmodulos[] = $row['modulo'];
		}
		mysqli_free_result($rs);
		
		
		//cargar los proyectos
		$sql ="SELECT * FROM `proyectos_usuario` WHERE usuario = '{$this->cod}'";
		$rs = asgMng::query($sql);
		while($row = mysqli_fetch_assoc($rs))
		{
			$this->usrproyectos[] = $row['project_id'];
		}
		mysqli_free_result($rs);
		
		
	}
	
	function guardar($mostrarError = true){
		$md5 = md5($this->clave);
	
		if($this->clave == "" ||  $md5 == $this->claveOriginal)
		{
			$this->clave = $this->claveOriginal;	
			
		}
		else
		{
			$this->clave = $md5;	
			echo mensajeDeAlerta(' ------------ Clave actualizada --------------');
		}
		
		
		parent::guardar($mostrarError);
		
		
		//Para guardar los modulos
		$sql = "delete from sq_modulos_user WHERE usuario = '{$this->cod}'";
		asgMng::query($sql); 
		$m = new genclas('sq_modulos_user');	
		foreach($this->usrmodulos as $modulo){
			
			$m->id = 0;
			$m->usuario = $this->cod;
			$m->modulo = $modulo;
			$m->guardar();
			
		}
		
		
		//Para guardar los roles
		$r = new genclas('sq_roles_user');
		$sql = "delete from sq_roles_user WHERE usuario = '{$this->cod}'";
		asgMng::query($sql);
		
		
		foreach($this->usrroles as $rol){
			
			$r->id = 0;
			$r->usuario = $this->cod;
			$r->rol = $rol;
			$r->guardar();
			
		}
		
		//Para guardar los proyectos
		
		$sql = "delete from proyectos_usuario WHERE usuario = '{$this->cod}'";
		
		asgMng::query($sql);
		
		$p = new genclas('proyectos_usuario');
		foreach($this->usrproyectos as $proyecto){
			$p->id = 0;
			$p->usuario = $this->cod;
			$p->project_id = $proyecto;
			$p->guardar();
			
		}
		
		
	}
	
	function setClave($clave)
	{
		$clave = md5($clave);
		$sql = "update sq_usuarios set clave = '{$clave}' where cod = {$this->cod}";
		asgMng::query($sql);
	}
	
	static function proyectosDe($miid)
	{
		$sql = "SELECT p.id, p.nombre FROM `proyectos_usuario`  pu
LEFT JOIN `proyectos` p ON p.id = pu.project_id
WHERE usuario = '{$miid}'";
		$dt = new dataTable($sql);
		return $dt;
	}
	
}
