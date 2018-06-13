<?php

namespace App\v2\src\models\pressure;

use App\v2\src\libs\Response;

class Pressure_test_model extends Pressure_model{
	
	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAll(){
		try{
			$result = array();
			
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['test']);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}
}
?>