<?php

namespace App\v1\src\models\asthmapp;

use App\v1\src\libs\Response;

class Asthmapp_group_model extends Asthmapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getAllById($id){
		try{
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE id = $id AND status = 1");
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
			$stmt = $this->db->prepare("SELECT * FROM " . $this->tables['groups'] . " WHERE email = ? AND status = 1");

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

	public function getGroupByDate($date){
		try{
			$stmt = $this->db->prepare("SELECT id FROM " . $this->tables['groups'] . " WHERE date_start <= '$date' AND date_finish >= '$date' AND status = 1");

			$stmt->execute();

			$rows = $stmt->fetchAll();

			if(count($rows) > 0){
				$this->response->setResponse(true);
				$this->response->result = $rows[0]['id'];
			}else{
				$this->response->setResponse(false, "Group not found");
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
						date_finish = ?,
						WHERE = id = ?";

				$udata = array(
					$data['date_finish'],
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

			}else{ // Insert

				$sql = "INSERT INTO " . $this->tables['groups'] . " (email, date_start, date_finish) values (?,?,?)";

				$idata = array(
					$data['email'],
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

	public function delete($id){
		try{
			$stmt = $this->db->prepare("UPDATE " . $this->tables['groups'] . " SET status = 0 WHERE id = $id");
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