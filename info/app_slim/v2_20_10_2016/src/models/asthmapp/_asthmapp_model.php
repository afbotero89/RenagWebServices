<?php

namespace App\v1\src\models\asthmapp;

class Asthmapp_model{

	const LOG = "Asthmapp model - ";

	public $api_version = 2;

	public $tables = [
						  'groups' => 'asthmapp_groups',
						  'test' => 'asthmapp_tests',
						  'pwd' => 'asthmapp_group_pwd'
						 ];

	private $response;

	private $sql_tbl_groups = "CREATE TABLE IF NOT EXISTS asthmapp_groups (
							id int(11) NOT NULL AUTO_INCREMENT,
							teacher_name varchar(60) NOT NULL,
							email varchar(50) NOT NULL,
							label varchar(50) NOT NULL,	
							date_start datetime NOT NULL,
							date_finish datetime NOT NULL,
							active int(1) default 1,
							status int(1) DEFAULT 1,
							api_version int(2) DEFAULT 1,
							PRIMARY KEY (id))ENGINE=InnoDB;";

	private $sql_tbl_tests = "CREATE TABLE IF NOT EXISTS asthmapp_tests(
							id int(11) NOT NULL AUTO_INCREMENT,
							name varchar(30) NOT NULL,
							document varchar(14) NOT NULL,
							email varchar(30) NOT NULL,
							result text NOT NULL,
							date_test datetime NOT NULL,
							device_id int(11) NOT NULL,
							remote_id int(11) NOT NULL,
							group_id int(11) NOT NULL,
							test_status varchar(12) NOT NULL,
							status int(1) DEFAULT 1,
							api_version int(2) DEFAULT 1,
							PRIMARY KEY(id),
							FOREIGN KEY(group_id) REFERENCES asthmapp_groups(id)) ENGINE=InnoDB;";


	private $sql_tbl_group_pwd = "CREATE TABLE IF NOT EXISTS asthmapp_group_pwd (
								id int(11) NOT NULL AUTO_INCREMENT,
								pwd varchar(30) NOT NULL,
								group_id int(11) NOT NULL,
								api_version int(2) DEFAULT 1,
								PRIMARY KEY (id),
								FOREIGN KEY(group_id) REFERENCES asthmapp_groups(id)) ENGINE=InnoDB;";

	public $sql_tables;

	public function __CONSTRUCT(){

	}

	public function create_tables($db){

		$this->sql_tables = [
					$this->sql_tbl_groups,
					$this->sql_tbl_tests,
					$this->sql_tbl_group_pwd
					];

		// Create tables
		foreach ($this->sql_tables as $table) {
			$db->prepare($table)
		             ->execute();
		}
	}
}

?>