<?php

namespace App\v2\src\models\asthmapp;

use App\v2\src\libs\Response;

class Asthmapp_group_model extends Asthmapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	// Get all groups Where status = 1
	public function getAll(){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE status = 1 AND api_version =  " . $this->api_version);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	// Get all groups where active and status = 1
	public function getAllActive(){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE active = 1 AND status = 1 AND api_version = " . $this->api_version);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();
		}
		catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}
		return $this->response;
	}

	public function getById($id){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE id = $id AND active = 1 AND status = 1 AND api_version = " . $this->api_version);
			$stmt->execute();

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function getAllByEmail($email){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE email = ? AND status = 1 AND api_version = " . $this->api_version);

			$gdata = array(
				$email
			);

			$stmt->execute($gdata);

			$this->response->setResponse(true);
			$this->response->result = $stmt->fetchAll();

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function getGroupToSave($id, $date){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE date_start <= '$date' AND date_finish >= '$date' AND id = $id AND active = 1 AND status = 1 AND api_version = " . $this->api_version);

			$stmt->execute();

			$rows = $stmt->fetchAll();

			if(count($rows) > 0){
				$data_result_group_to_save = array(
					"success" => "1"
					);
				$this->response->setResponse(true);
				$this->response->result = $data_result_group_to_save;
			}else{
				$this->response->setResponse(false, "Date outside the group permitted range");
			}

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function insertOrUpdate($data){
		try{
			if(isset($data['id'])){ // Update

				$sql = "UPDATE " . $this->tables['groups'] . " SET
						date_finish = ?
						WHERE id = ?";

				$udata = array(
					$data['date_finish'],
					$data['id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

				if($affected > 0){
					$this->response->setResponse(true);
					$this->response->result = array("success" => "1");
				}else{
					$this->response->setResponse(false, "Error updating record.");
				}

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['groups'] . " (teacher_name, email, label, date_start, date_finish, api_version) values (?,?,?,?,?, " . $this->api_version . ")";

				$idata = array(
					$data['teacher_name'],
					$data['email'],
					$data['label'],
					$data['date_start'],
					$data['date_finish']
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

	// close a group
	public function close($data){
		try{

			$sql = "UPDATE " . $this->tables['groups'] . " SET
					date_finish = ?,
					active = 0
					WHERE id = ?";

			$udata = array(
				$data['date_finish'],
				$data['id']
				);

			$stmt = $this->db->prepare($sql);
			$stmt->execute($udata);
			$affected = $stmt->rowCount();

			if($affected > 0){
				$this->response->setResponse(true);
				$this->response->result = array("success" => "1");
			}else{
				$this->response->setResponse(false, "Could not close the group or had already closed");
			}
		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}

	public function delete($id){
		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tables['groups'] . " SET active = 0, status = 0 WHERE id = $id");
			$stmt->execute();
			$affected = $stmt->rowCount();

			if($affected > 0){
				$this->response->setResponse(true);
				$this->response->result = array("success" => "1");
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