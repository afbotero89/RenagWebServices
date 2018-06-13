<?php

namespace App\v2\src\models\vrapp;

class Vrapp_model{

	const LOG = "VRapp model - ";

	public $api_version = 1;

	public $tables = [
						  'doctors' => 'vrapp_doctors',
						  'patients' => 'vrapp_patients',
						  'tests' => 'vrapp_tests'						  
						 ];

	private $message;
	private $response;

	private $sql_tbl_doctors = "CREATE TABLE IF NOT EXISTS vrapp_doctors (
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(50) NOT NULL,
							lastname varchar(50) NOT NULL,
							email varchar(50) NOT NULL,
							password varchar(50) NOT NULL,
							status int(1) DEFAULT 1,
							api_version int(2) DEFAULT 1,
							PRIMARY KEY (id)) ENGINE=InnoDB;";

	private $sql_tbl_patients = "CREATE TABLE IF NOT EXISTS vrapp_patients (
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(50) NOT NULL,
							lastname varchar(50) NOT NULL,
							document varchar(16) NOT NULL,
							born_date varchar(12) NOT NULL,
							gender varchar(5) NOT NULL,
							medical_condition text NOT NULL,
							drugs text NOT NULL,
							sedentary varchar(2) NOT NULL,
							smoking varchar(2) NOT NULL,
							diabetes varchar (2) NOT NULL,
							doctor_id int(11) NOT NULL,
							device_id int(11) NOT NULL,
							remote_id int(11) NOT NULL,
							status int(1) DEFAULT 1,
							api_version int(2) DEFAULT 1,
							PRIMARY KEY (id),
							FOREIGN KEY(doctor_id) REFERENCES vrapp_doctors(id)) ENGINE=InnoDB;";

	private $sql_tbl_tests = "CREATE TABLE IF NOT EXISTS vrapp_tests(
							id int(11) NOT NULL AUTO_INCREMENT,
							occlusion_time varchar(10) NOT NULL,
							recovery_time varchar(10) NOT NULL,
							vr_index varchar(20) NOT NULL,
							stop_pressure varchar(10) NOT NULL,
							sys_pressure varchar(10) NOT NULL,
							dia_pressure varchar(10) NOT NULL,
							avg_pressure varchar(10) NOT NULL,
							pulse_pressure varchar(20) NOT NULL,
							init_amp varchar(30) NOT NULL,
							min_amp varchar(30) NOT NULL,
							max_amp varchar(30) NOT NULL,
							final_amp varchar(30) NOT NULL,
							rising_time varchar(40) NOT NULL,
							date_test datetime NOT NULL,
							abdom_circ varchar(5) NOT NULL,
							chart_data text NOT NULL,
							final_state varchar(20) NOT NULL,
							patient_id int(11) NOT NULL,
							device_id int(11) NOT NULL,
							remote_id int(11) NOT NULL,
							status int(1) DEFAULT 1,
							api_version int(2) DEFAULT 1,
							PRIMARY KEY(id),
							FOREIGN KEY(patient_id) REFERENCES vrapp_patients(id)) ENGINE=InnoDB;";


	public $sql_tables;

	public function __CONSTRUCT(){

	}

	public function create_tables($db){

		$this->sql_tables = [
					$this->sql_tbl_doctors,
					$this->sql_tbl_patients,
					$this->sql_tbl_tests
					];

		// Create tables
		foreach ($this->sql_tables as $table) {
			$db->prepare($table)
		             ->execute();
		}
	}
}

?>