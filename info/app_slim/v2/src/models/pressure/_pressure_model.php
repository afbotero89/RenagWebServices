<?php

namespace App\v2\src\models\pressure;

class Pressure_model{

	const LOG = "Pressure route - ";

	public $tables = [
						  'admin' => 'pressure_admins',
						  'patient' => 'pressure_patients',
						  'test' => 'pressure_tests',
						  'meassure' => 'pressure_meassures'
						 ];

	private $response;

	private $sql_tbl_patient = "CREATE TABLE IF NOT EXISTS pressure_patients(
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(60) NOT NULL,
							document int NOT NULL,
							email varchar(80) NOT NULL,
							device_address varchar(30) NOT NULL,
							sync_status int(1) NOT NULL,
							remote_status int(1) NOT NULL,
							remote_id int(11) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_admin = "CREATE TABLE IF NOT EXISTS pressure_admins(
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(60) NOT NULL,
							email varchar(80) NOT NULL,
							password varchar(120) NOT NULL,
							remote_status int(1) NOT NULL,
							remote_id int(11) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_test = "CREATE TABLE IF NOT EXISTS pressure_tests(
							id int(11) NOT NULL AUTO_INCREMENT,
							date_created varchar(10) NOT NULL,
							date_sync varchar(10) NOT NULL,
							measures varchar(10) NOT NULL,
							delay varchar(10) NOT NULL,
							sync_status int(1) NOT NULL,
							patient_id int(11) NOT NULL,
							remote_status int(1) NOT NULL,
							remote_id int(11) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_measure = "CREATE TABLE IF NOT EXISTS pressure_meassures(
							id int(11) NOT NULL AUTO_INCREMENT,
							data_ac text NOT NULL,
							data_dc text NOT NULL,
							press_avg varchar(6) NOT NULL,
							press_sys varchar(6) NOT NULL,
							press_dia varchar(6) NOT NULL,
							heart_rate varchar(6) NOT NULL,
							meas_time varchar(10) NOT NULL,
							meas_date varchar(10) NOT NULL,
							test_id int(11) NOT NULL,
							remote_status int(1) NOT NULL,
							remote_id int(11) NOT NULL,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	public $sql_tables;

	public function __CONSTRUCT(){

	}

	public function create_tables($db){

		$this->sql_tables = [
					$this->sql_tbl_patient,
					$this->sql_tbl_admin,
					$this->sql_tbl_test,
					$this->sql_tbl_measure
					];

		// Create service tables
		foreach ($this->sql_tables as $table) {
			$db->prepare($table)
		             ->execute();
		}
	}
}

?>