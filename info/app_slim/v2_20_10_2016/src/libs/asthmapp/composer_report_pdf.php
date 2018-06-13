<?php
namespace App\v1\src\libs\asthmapp;

use App\v1\src\libs\Response;
use App\v1\src\libs\asthmapp\DataSource;

class ComposerReportPdf{

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
			// file properties
			$title_color = array(50, 160, 200);
			$body_color = array(0, 0, 0);
			$title_height_tx = 14;
			$body_height_tx = 12;
			$body_ln = 18;

			$rows_first_page = 50;
			$rows_page = 54;
			$rows_added = 0;
			$rows_in_page = 0;
			$count_page = 0;

			    // content resource
			$title_report = "Informe de resultados quis Asthmapp";
			    // Table test titles
			$title_table_report = "Resultado quiz Asthmapp";
			$report_c1 = "Nombre registrado";
			$report_c2 = "Documento";
			$report_c3 = "Porcentaje acierto";
			$report_c4 = "Nota";

			//Get data
			$group = $data["group"];
			$tests = $data["tests"];

		    $this->pdf->SetAutoPageBreak(true);
		    $this->pdf->AliasNbPages();
		    $this->pdf->AddPage(); // add summary test page

		    $this->pdf->SetFont("Arial", 'B', $title_height_tx);
		    // Set title color
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);

		    // Print test title
		    $this->pdf->Cell(0, 14, $title_report, 0, 0, '');
		    $this->pdf->Ln(20);

		    // title font
		    $this->pdf->SetFont("Arial", '', $title_height_tx);

			// Set title color
		    $this->pdf->SetTextColor($title_color[0],$title_color[1],$title_color[2]);
		    // print title table test
		    $this->pdf->MultiCell(0, $body_height_tx, $title_table_report . " " .substr($group["date_start"], 0, 10));
		    $this->pdf->Ln($body_ln);

		    //Set cols table
		    $this->pdf->ResetCols();
		    $this->pdf->AddCol($report_c1, '40%', 'C');
    		$this->pdf->AddCol($report_c2, '25%', 'C');
    		$this->pdf->AddCol($report_c3, '25%', 'C');
    		$this->pdf->AddCol($report_c4, '10%', 'C');
			// set table properties
		    $prop = array(
		        'border' => 1,
		        'headerTextColor' => array(50, 160, 200),
		        'rowTextColor' => array(0,0,0));
		    //get data rows for test table
		    $rows = array();
		    $total_rows = count($tests);

		    // Compute row content
		    foreach ($tests as $test) {
		    	//Counbter total rows addes
		    	$rows_added++;
		    	//Counter rows per page
		    	$rows_in_page++;
		    	//Get result data from json
		    	$result = json_decode($test['result'], true);
		    	//Build table row
		        $new_row = array(
		        	$test["name"],
		        	$test["document"],
		        	$result["score"] . " %",
		        	(5 * intval($result["score"]))/100
		        	);
		        array_push($rows, $new_row);

		        if($count_page == 0){
		        	if($rows_in_page < $rows_first_page && $rows_added == $total_rows){
		        		$this->pdf->Table($rows, $prop);
						$this->pdf->Cell(0, 14, $rows_added . " - " . $total_rows, 0, 0, '');
		        	}
					else if($rows_in_page == $rows_first_page){
						$this->pdf->Table($rows, $prop);
						$this->pdf->Cell(0, 14, $rows_added . " - " . $total_rows, 0, 0, '');
						//Reset rows
				    	$rows = array();
				    	$rows_in_page = 0;

			    		if($rows_added < $total_rows){
				    		$this->pdf->AddPage();
				    		$count_page++;
			    		}
					}

		        }else{

					if($rows_in_page == $rows_page){
						$this->pdf->Table($rows, $prop);
						$this->pdf->Cell(0, 14, $rows_added . " - " . $total_rows, 0, 0, '');
		    
						//Reset rows
				    	$rows = array();
				    	$rows_in_page = 0;

			    		if($rows_added < $total_rows){
				    		$this->pdf->AddPage();
				    		$count_page++;
			    		}
					}else if($rows_in_page < $rows_page && $rows_added == $total_rows){
						$this->pdf->Table($rows, $prop);
						$this->pdf->Cell(0, 14, $rows_added . " - " . $total_rows, 0, 0, '');
		    
						//Reset rows
				    	$rows = array();
				    	$rows_in_page = 0;
					}
		        }				
		    }

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