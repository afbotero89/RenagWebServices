<?php
namespace App\v1\src\libs\asthmapp;

use App\v1\src\libs\Response;
use App\v1\src\libs\asthmapp\DataSource;

class ComposerQuizPdf{

	private $pdf;
	private $response;
	private $dataSource;

	public function __CONSTRUCT($pdf){
		$this->pdf = $pdf;
		$this->response = new Response();
		$this->dataSource = new DataSource();
	}

	public function render($data, $path){
		try{
		    $isStudentFile = false;
			$isFilePass = false;

			    // file properties
			$title_color = array(50, 160, 200);
			$body_color = array(0, 0, 0);
			$title_height_tx = 14;
			$body_height_tx = 12;
			$body_ln = 18;

			    // content resource
			$title_test = "Test asthmapp";
			$title_name = "Nombre";
			$title_id = "Cedula";
			$title_result = "Resumen de resultados";
			$title_ch = "Historia clínica";
			$title_gender = "Género: ";
			$title_age = "Edad: ";
			$title_weight = "Peso: ";
			$title_height = "Talla: ";
			$title_symptoms = "Síntomas: ";
			$title_assessment = "Valoración: ";
			$title_antecedents = "Antecedentes: ";
			$title_percent = "Porcentaje de acierto: ";
			$title_score = "Nota: ";
			

			    // Table test titles
			$title_table_test = "Espirometría y test broncodilatador";
			$test_c1 = "Variables espirométricas";
			$test_c2 = "Pre-test";
			$test_c3 = "Post-test";

			    // Table_result_titles
			$result_c1 = "Preguntas y respuestas";
			$result_c2 = "Resultado";

			$title_status_test = "Estado del exámen: ";

		    // Get data
		    if(isset($data["student"]))
		        $isStudentFile = true;


		    $content = $this->dataSource->getCaseContent($data["case"]);
		    $test_table = $content["table"];
		    $approvedMessage = $this->dataSource->getApprovedMessage($data["approvedMessage"]);
		    $status = $this->dataSource->getStatus($data["status"]);

		    $this->pdf->SetAutoPageBreak(TRUE);
		    $this->pdf->AliasNbPages();
		    $this->pdf->AddPage(); // add summary test page

		    $this->pdf->SetFont("Arial", 'B', $title_height_tx);
		    // Set title color
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);

		    // Print test title
		    $this->pdf->Cell(0, 14, $title_test . " " . $content["title"], 0, 0, '');
		    $this->pdf->Ln(20);

		    // if is student print info
		    if(isset($data["student"])){
		    	// Set file protection
		    	$this->pdf->SetProtection(array('print'), $data["student"]["pwd"]);
		        // title font
		        $this->pdf->SetFont("Arial", '', $title_height_tx);

		        // print student name
		        $this->pdf->Cell(60, 14, $title_name . ": ", 0, 0);
		        // set font and color body content
		        $this->pdf->SetTextColor($body_color[0], $body_color[1], $body_color[2]);
		        $this->pdf->SetFont("Arial", '', $body_height_tx);

		        $this->pdf->Cell(0, 14, $data["student"]["name"], 0, 0);
		        $this->pdf->Ln(20);

		        // print student id
		        // Set title color
		        $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);
		        $this->pdf->Cell(60, 14, $title_id . ": ", 0, 0);

		            // set font and color body content
		        $this->pdf->SetTextColor($body_color[0], $body_color[1], $body_color[2]);
		        $this->pdf->SetFont("Arial", '', $body_height_tx);

		        $this->pdf->Cell(0, 14, $data["student"]["document"], 0, 0);
		        $this->pdf->Ln(20);
		        // Set title color
		        $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);
		    }

		    // title font
		    $this->pdf->SetFont("Arial", '', $title_height_tx);

		    // Print CH title
		    $this->pdf->Cell(0, 14, $title_ch, 0, 0, '');
		    $this->pdf->Ln(20);

		    // set font and color body content
		    $this->pdf->SetTextColor($body_color[0], $body_color[1], $body_color[2]);
		    $this->pdf->SetFont("Arial", '', $body_height_tx);
		    // print HC body
		    $this->pdf->Cell(0, $body_height_tx, $title_gender . $content["gender"], 0, 0, '');
		    $this->pdf->Ln($body_ln);
		    $this->pdf->Cell(0, $body_height_tx, $title_age . $content["age"] . " years");
		    $this->pdf->Ln($body_ln);
		    $this->pdf->Cell(0, $body_height_tx, $title_weight . $content["weight"] . " kgs");
		    $this->pdf->Ln($body_ln);
		    $this->pdf->Cell(0, $body_height_tx, $title_height . $content["height"] . " cms");
		    $this->pdf->Ln($body_ln + 8);

		    // print symptoms
		    $this->pdf->MultiCell(0, $body_height_tx, $title_symptoms . $content["symptoms"]);
		    $this->pdf->Ln($body_ln);

		    // print assessment
		    $this->pdf->MultiCell(0, $body_height_tx, $title_assessment . $content["assessment"]);
		    $this->pdf->Ln($body_ln);

		    // print antecedents
		    $this->pdf->MultiCell(0, $body_height_tx, $title_antecedents . $content["antecedents"]);
		    $this->pdf->Ln($body_ln);

			// Set title color
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);
		    // print title table test
		    $this->pdf->MultiCell(0, $body_height_tx, $title_table_test);
		    $this->pdf->Ln($body_ln);

		    $this->pdf->AddCol($test_c1, '33%', 'C');
		    $this->pdf->AddCol($test_c2, '33%', 'C');
		    $this->pdf->AddCol($test_c3, '33%', 'C');

		    //get data rows for test table
		    $rows = array();

		    // Compute row content
		    foreach ($test_table as $data_row) {
		        $new_row = split(";", $data_row);
		        array_push($rows, $new_row);
		    }

		    // set table properties
		    $prop = array(
		        'border' => 0,
		        'headerTextColor' => array(50, 160, 200),
		        'rowTextColor' => array(0,0,0));

		    // Print test table
		    $this->pdf->Table($rows, $prop);

		    // Add summary result page
		    $this->pdf->AddPage();

		    // Set title font and color
		    $this->pdf->SetFont("Arial", 'B', $title_height_tx);
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);

		    // Print test title
		    $this->pdf->Cell(0, 14, $title_result, 0, 0, '');
		    $this->pdf->Ln(20);

		    $this->pdf->ResetCols();
		    $this->pdf->AddCol($result_c1, '76%', 'L');
		    $this->pdf->AddCol($result_c2, '24%', 'C');

		    // get data rows for result table
		    $data_rows = split(";", $data["answers"]);
		    $rows = array();

		    // Compute row content
		    $count_row = 1;
		    foreach ($data_rows as $data_row) {
		        $tmp_row = split(",", $data_row);

		        //Get datarow
		        $question = $this->dataSource->getQuestion($tmp_row[0]);
		        $answer = $this->dataSource->getAnswer($tmp_row[1]);
				$score = $this->dataSource->getScore($tmp_row[2]);

				//Get justification
				$just = $this->dataSource->getJustification($tmp_row[0]);

				if(isset($just)){
					$new_row = array("$count_row) $question\nR// $answer\nJ// $just\n\n",$score);
				}else{
					$new_row = array("$count_row) $question\nR// $answer\n\n",$score);
				}

		        
		        array_push($rows, $new_row);
		        $count_row++;
		    }

		    // Set table properties
		    $prop = array(
		        'border' => 0,
		        'headerTextColor' => array(50, 160, 200),
		        'rowTextColor' => array(0,0,0));

		    // Print result table
		    $this->pdf->Table($rows, $prop);

		    $this->pdf->Ln(50);

		    // Set title font and color
		    $this->pdf->SetFont("Arial", '', $title_height_tx);
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);


		    // print result
		    $this->pdf->Cell(0, 14, $approvedMessage, 0, 2, '');
		    $this->pdf->Ln(4);

		    // set font and color body content
		    $this->pdf->SetTextColor($body_color[0], $body_color[1], $body_color[2]);
		    $this->pdf->SetFont("Arial", '', $body_height_tx);
		    // Print percent value
		    $this->pdf->Cell(0, $body_height_tx, $title_percent . $data["score"] . " %", 0, 2, '');
		    $this->pdf->Ln(4);

		    // Print score value
		    $score_quiz = (5 * intval($data["score"]))/100;
		    $this->pdf->Cell(0, $body_height_tx, $title_score . $score_quiz, 0, 2, '');
		    $this->pdf->Ln($body_ln);

		    //Print test status
		    $this->pdf->Cell(0, $body_height_tx, $title_status_test . $status, 0, 2, '');
		    $this->pdf->Ln($body_ln);

		    $this->pdf->Output('F', $path, FALSE);

		    $this->response->setResponse(true);
		    $this->response->result = "File successfully generated";
		    return $this->response;
		}catch(Exception $e){
			$this->response->setResponse(false, "Error creating file");
			return $this->response;
		}
	}
}