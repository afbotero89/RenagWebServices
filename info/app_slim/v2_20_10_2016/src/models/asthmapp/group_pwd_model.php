<?php

namespace App\v1\src\models\asthmapp;

use App\v1\src\libs\Response;

class Asthmapp_group_pwd_model extends Asthmapp_model{

	private $db;

	public function __CONSTRUCT($pdo){
		$this->db = $pdo;
		$this->create_tables($this->db);

		$this->response = new Response();
	}

	public function getPwdByGroup($group_id){
		try{
			$stmt = $this->db->prepare("SELECT pwd FROM " . $this->tables['pwd'] . " WHERE group_id = $group_id AND api_version = " . $this->api_version);

			$stmt->execute();

			$rows = $stmt->fetchAll();

			if(count($rows) > 0){
				$this->response->setResponse(true);
				$this->response->result = $rows[0]['pwd'];
			}else{
				$this->response->setResponse(false, "Password group not found");
			}

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
		}

		return $this->response;
	}


	public function insertOrUpdate($data){
		try{

			if(isset($data['id'])){ //Update record

				$sql = "UPDATE " . $this->tables['pwd'] . " SET 
						pwd = ?,
						WHERE id = ?";

				$udata = array(
					$data['pwd'],
					$data['id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($udata);
				$affected = $stmt->rowCount();

				if($affected > 0){
					$this->response->setResponse(true);
					$this->response->result = $affected;
				}else{
					$this->response->setResponse(false, "Error updating PWD for group " . $$data['id'] . ".");
				}

			}else{ //Insert record

				$sql = "INSERT INTO " . $this->tables['pwd'] . " (pwd, group_id, api_version) VALUES (?,?," . $this->api_version . ")";

				$idata = array(
					$data['pwd'],
					$data['group_id']
					);

				$stmt = $this->db->prepare($sql);
				$stmt->execute($idata);
				$affected = $stmt->rowCount();

				if($affected > 0){
					$this->response->setResponse(true);
					$this->response->result = $affected;
				}else{
					$this->response->setResponse(false, "Error inserting PWD for group " . $data['id'] .".");
				}

			}

			return $this->response;

		}catch(Exception $e){
			$this->response->setResponse(false, $e->getMessage());
			return $this->response;
		}
	}
}
?>