<?php
	
	class conexion
	{
		private $conect;
		
		public function __construct()
		{	
			$this->conect = mssql_connect(DB_HOST,DB_USER,DB_PASS);
			
			mssql_select_db(DB_NAME,$this->conect);
		}
		
		public function __destruct()
		{
			mssql_close($this->conect);
		}
	}
	
?>