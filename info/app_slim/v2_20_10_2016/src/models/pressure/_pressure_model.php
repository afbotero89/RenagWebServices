<?php

namespace App\v1\src\models\pressure;

class Pressure_model{

	const LOG = "Pressure route - ";

	public $tables = [
						  'company' => 'pressure_companies',
						  'user' => 'pressure_users',
						  'branch' => 'pressure_branches',
						  'meassure' => 'pressure_meassures',
						  'host' => 'pressure_hosts'
						 ];

	public $user_type = [
						'admin' => 'admin',
						'patient' => 'patient'
						];

	private $response;

	private $sql_tbl_host = "CREATE TABLE IF NOT EXISTS pressure_hosts(
							id int(11) NOT NULL AUTO_INCREMENT,
							type varchar(10) NOT NULL,
							url varchar(60) NOT NULL,
							db varchar(60) NOT NULL,
							user varchar(30) NOT NULL,
							pass varchar(40) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_user = "CREATE TABLE IF NOT EXISTS pressure_users(
							id int(11) NOT NULL AUTO_INCREMENT,
							branch_id int(11) NOT NULL,
							name varchar(30) NOT NULL,
							document int NOT NULL,
							age int(3) NOT NULL,
							gender varchar(1) NOT NULL,
							email varchar(40) NOT NULL,
							pass varchar(50) NOT NULL,
							token varchar(80) NOT NULL,
							profile varchar(20) NOT NULL,						
							state int(1) DEFAULT 1,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_company = "CREATE TABLE IF NOT EXISTS pressure_companies(
							id int(11) NOT NULL AUTO_INCREMENT,
							admin_id int(11) NOT NULL,
							name varchar(40) NOT NULL,
							nit varchar(20) NOT NULL,
							phone varchar(20) NOT NULL,
							state int(1) DEFAULT 1,
							PRIMARY KEY (id),
							FOREIGN KEY (admin_id) REFERENCES pressure_users(id) ON DELETE CASCADE ON UPDATE CASCADE)ENGINE=InnoDB;";

	private $sql_tbl_branch = "CREATE TABLE IF NOT EXISTS pressure_branches(
							id int(11) NOT NULL AUTO_INCREMENT,
							company_id int(11) NOT NULL,
							host_id int(11) NOT NULL,
							name varchar(40) NOT NULL,
							nit varchar(20) NOT NULL,
							phone varchar(20) NOT NULL,
							state int(1) DEFAULT 1,
							PRIMARY KEY (id),
							FOREIGN KEY (company_id) REFERENCES pressure_companies(id) ON DELETE CASCADE ON UPDATE CASCADE,
							FOREIGN KEY (host_id) REFERENCES pressure_hosts(id) ON DELETE CASCADE ON UPDATE CASCADE)ENGINE=InnoDB;";

	private $sql_tbl_messure = "CREATE TABLE IF NOT EXISTS pressure_meassures(
							id int(11) NOT NULL AUTO_INCREMENT,
							patient_id int(11) NOT NULL,
							data_ac text NOT NULL,
							data_dc text NOT NULL,
							pressure_avg varchar(4) NOT NULL,
							pressure_sys varchar(4) NOT NULL,
							pressure_dia varchar(4) NOT NULL,
							meassure_time varchar(12) NOT NULL,
							meassure_date varchar(12) NOT NULL,							
							state int(1) DEFAULT 1,
							PRIMARY KEY (id),
							FOREIGN KEY (patient_id) REFERENCES pressure_users(id) ON DELETE CASCADE ON UPDATE CASCADE)ENGINE=InnoDB;";

	public $sql_tables;

	public function __CONSTRUCT(){

	}

	public function create_tables($db){

		$this->sql_tables = [
					$this->sql_tbl_host,
					$this->sql_tbl_user,
					$this->sql_tbl_company,
					$this->sql_tbl_branch,
					$this->sql_tbl_messure
					];

		// Create service tables
		foreach ($this->sql_tables as $table) {
			$db->prepare($table)
		             ->execute();
		}
	}
}

?>