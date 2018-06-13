<?php

namespace App\v1\src\models\pressure;

use App\v1\src\libs\Response;

class Pressure_company_model extends Pressure_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function GetAll(){
		try{
			$result = array();

			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['company'] . " WHERE state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function Get($id){
		try{
			$result = array();

			// consult db
			$stmt = $this->db->prepare("SELECT * FROM ". $this->tables['company'] . " WHERE id = $id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function GetAdminCompanies($admin_id){
		try{
			$result = array();

			// Consult db
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['company'] . " WHERE admin_id = $admin_id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function InsertOrUpdate($data){

		try{
			if(isset($data['id'])){ // Update

				$sql = "UPDATE " . $this->tables['company'] . " SET 
						name = ?,
						nit = ?,
						phone = ?
						WHERE id = ?";

				$udata = array(
					$data['name'],
					$data['nit'],
					$data['phone'],
					$data['id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['company'] . " (admin_id, name, nit, phone) VALUES (?,?,?,?)";
				$idata = array(
					$data['admin_id'],
					$data['name'],
					$data['nit'],
					$data['phone']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$affected = $stmt->rowCount();

			}

			$this->response->setResponse(true);
			$this->response->result = $affected;

			return $this->response;

		}catch(Exception $e){
			$this->response->setResponse(false);
			return $this->response;
		}
	}

	public function Delete($id){
		try{
			$result = array();

			// db consult
			$stmt = $this->db->prepare("UPDATE " . $this->tables['company'] . " SET state = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			$this->response->setResponse(true);
			$this->response->result = $affected;

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}
}
?>