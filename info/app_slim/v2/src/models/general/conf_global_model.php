<?php

namespace App\v2\src\models\general;

class Global_model{

	const LOG = "Global model - ";

	public $tables = [
						  'users' => 'global_users',
						  'patients' => 'global_petients'
						 ];

	private $response;

	private $sql_tbl_users = 'CREATE TABLE IF NOT EXISTS $tables["users"](
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(50) NOT NULL,
							email varchar(40) NOT NULL,
							pwd varchar(30) NOT NULL,
							token varchar(50) NOT NULL,
							profile varchar(12) NOT NULL,
							status int(1) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;';

	private $sql_tbl_patients = 'CREATE TABLE IF NOT EXISTS $tables["patients"](
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(20) NOT NULL,
							last_name varchar(20) NOT NULL,
							age int(5) NOT NULL,
							gender varchar(1) NOT NULL,
							birthday date NOT NULL,
							doc_type varchar(4) NOT NULL,
							document varchar(14) NOT NULL,
							status int(1) DEFAULT 1,
							PRIMARY KEY (id)) ENGINNE=InnoDB;';

	public $sql_tables;

	public function __CONSTRUCT(){

	}

	public function create_tables($db){

		$this->sql_tables = [
					$this->sql_tbl_users,
					$this->sql_tbl_patients
					];

		// Create service tables
		foreach ($this->sql_tables as $table) {
			$db->prepare($table)
		             ->execute();
		}
	}
}

?>