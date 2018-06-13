<?php

namespace App\v2\src\models\pressure;

use App\v2\src\libs\Response;

class Pressure_admin_model extends Pressure_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAll(){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['user'] . " WHERE profile = '". $this->user_type['admin'] ."' AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function get($id){
		try{
			// consult db
			$stmt = $this->db->prepare("SELECT * FROM ". $this->tables['user'] . " WHERE profile = '". $this->user_type['admin'] ."' AND id = $id AND state = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

			return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}

	public function insertOrUpdate($data){

		try{
			if(isset($data['id'])){ // Update

				$sql = "UPDATE " . $this->tables['user'] . " SET 
						branch_id = ?,
						name = ?,
						document = ?,
						age = ?,
						gender = ?,
						email = ?,
						pass = ?,
						token = ?
						WHERE id = ?";

				$udata = array(
					$data['branch_id'],
					$data['name'],
					$data['document'],
					$data['age'],
					$data['gender'],
					$data['email'],
					$data['pass'],
					$data['token'],
					$data['id']
				);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['user'] . " (branch_id, name, document, age, gender, email, pass, token, profile) VALUES (?,?,?,?,?,?,?,?,?)";

				$idata = array(
					$data['branch_id'],
					$data['name'],
					$data['document'],
					$data['age'],
					$data['gender'],
					$data['email'],
					$data['pass'],
					$data['token'],
					$this->user_type['admin']
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

	public function delete($id){
		try{
			// db consult
			$stmt = $this->db->prepare("UPDATE " . $this->tables['user'] . " SET state = 0 WHERE id = $id");
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

	public function validateUser($data){
		try{
			$stmt = $this->db->prepare("SELECT id, token FROM " . $this->tables['user'] . " WHERE email = ? AND pass = ? AND profile = ? AND state = 1");
			$qdata = array(
				$data['email'],
				$data['pass'],
				$data['profile']
				);
			$stmt->execute($qdata);

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