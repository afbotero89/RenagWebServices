<?php

namespace App\v1\src\models\asthmapp;

use App\v1\src\libs\Response;

class Asthmapp_test_model extends Asthmapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAllByGroup($group_id){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['test'] . " WHERE group_id = 1 AND status = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function getAllById($id){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['test'] . " WHERE id = $id AND status = 1");
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function insertOrUpdate($data){
		try{
			if(isset($data['id'])){ //update record
				// Create SQL query
				$sql = "UPDATE " . $this->tables['test'] . " SET 
						name = ?,
						document = ?,
						email = ?,
						result = ?,
						date_test = ?,
						device_id = ?,
						remote_id = ?,
						group_id = ?
						WHERE id = ?";

				$udata = array(
					$data['name'],
					$data['document'],
					$data['email'],
					$data['result'],
					$data['date_test'],
					$data['device_id'],
					$data['remote_id'],
					$data['group_id'],
					$data['id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

				if($affected > 0){
					$this->response->setResponse(true);
					$this->response->result = $affected;
				}else{
					$this->response->setResponse(false, "Error updating record.");
				}

			}else{ //Insert record

				//Create SQL query
				$sql = "INSERT INTO " . $this->tables['test'] . " (name, document, email, result, date_test, device_id, remote_id, group_id) VALUES (?,?,?,?,?,?,?,?)";

				$idata = array(
					$data['name'],
					$data['document'],
					$data['email'],
					$data['result'],
					$data['date_test'],
					$data['device_id'],
					$data['remote_id'],
					$data['group_id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$id = $this->db->lastInsertId();
				$affected = $stmt->rowCount();

				if($affected > 0){
					$this->response->setResponse(true);
					$this->response->result = $id;
				}else{
					$this->response->setResponse(false, "Error inserting record.");
				}
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function delete($id){
		try{
			//Update record to delete
			$stmt = $this->db->prepare("UPDATE " . $this->tables['test'] . " SET status = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			if($affected > 0){
				$this->response->setResponse(true);
				$this->response->result = $affected;
			}else{
				$this->response->setResponse(false, "Error deleting record.");
			}

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}
}

?>