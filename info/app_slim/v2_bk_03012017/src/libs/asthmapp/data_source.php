<?php

namespace App\v2\src\libs\asthmapp;

class DataSource{

	private $title, $gender, $age, $weight, $height;
	private $symptoms;
	private $assessment;
	private $antecedents;
	private $questions;
	private $answers;
	private $justifications;
	private $test_table;
	private $score;
	private $approvedMessage;

	private $test_tableA = array(
		"FVC;3.73 L  80.00%;3.87 L 83.00%",
		"FEV1;2.25 L  61.00%;2.98 L  81.00%",
		"FEV1%;0.60;0.77",
		"PEF;5.24 L/s  70.00%;5.61 L/s  75.00%",
		"FEF 25-75;2.29 L/s  55.00%;3.75 L/s  90.00%"
		);

	private $test_tableB = array(
		"FVC;3.03 L  65.00%;3.17 L 68.00%",
		"FEV1;2.98 L  81.00%;3.06 L  83.00%",
		"FEV1%;0.99;0.96",
		"PEF;5.24 L/s  70.00%;5.61 L/s  75.00%",
		"FEF 25-75;3.13 L/s  75.00%;3.54 L/s  85.00%"
		);

	private $test_tableC = array(
		"FVC;3.73 L  80.00%;3.87 L 83.00%",
		"FEV1;1.88 L  51.00%;2.80 L  76.00%",
		"FEV1%;0.50;0.72",
		"PEF;5.24 L/s  70.00%;5.61 L/s  75.00%",
		"FEF 25-75;2.29 L/s  55.00%;3.79 L/s  91.00%"
		);


	public function __CONSTRUCT(){
		$this->title = array(
			"CaseA" => "Caso 1",
			"CaseB" => "Caso 2",
			"CaseC" => "Caso 3"
			);

		$this->gender = array(
			"CaseA" => "Femenino",
			"CaseB" => "Masculino",
			"CaseC" => "Femenino"
			);

		$this->age = array(
			"CaseA" => "20",
			"CaseB" => "25",
			"CaseC" => "29"
			);

		$this->weight = array(
			"CaseA" => "65",
			"CaseB" => "70",
			"CaseC" => "65"
			);

		$this->height = array(
			"CaseA" => "170",
			"CaseB" => "170",
			"CaseC" => "160"
			);

		$this->symptoms = array(
			"CaseA" => "Paciente con cuadro clínico de tres meses de evolución de rinorrea hialina matutina, estornudos frecuentes, tos seca luego de actividad física recreativa y competitiva. Concomitantemente refiere tos nocturna especialmente al amanecer. El día de ayer luego de un examen en la universidad presentó una crisis de disnea que la autolimitó luego de una hora de reposo.",
			"CaseB" => "Paciente quien consulta por cuadro clínico de dificultad respiratoria progresiva de pequeños y medianos esfuerzos asociada a tos diurna seca.",
			"CaseC" => "Paciente asmática en tratamiento con fluticasona 250 mcg c/12 , quien refiere tos luego de actividad física, usa una a dos veces por semana salbutamol y presenta tos nocturna una vez por semana durante los últimos tres meses."
			);

		$this->assessment = array(
			"CaseA" => "Tórax simétrico,  no clubbing, 25 respiraciones por minuto y 90 latidos por minuto.",
			"CaseB" => "Tórax con pectus excavatum,  no clubbing, 20 respiraciones por minuto y 70 latidos por minuto.",
			"CaseC" => "Tórax simétrico, no clubbing, 25 respiraciones por minuto y 120 latidos por minuto."
			);

		$this->antecedents = array(
			"CaseA" => "Madre asmática. Dermatitis alérgica desde la infancia controlada con corticoides tópicos. Fumadora ocasional.",
			"CaseB" => "Enfermedad de Duchenne. Dermatitis alérgica desde la infancia controlada con corticoides tópicos. Fumador ocasional.",
			"CaseC" => "Rinitis alérgica moderada, no controlada y sin tratamiento actual. Fumadora ocasional."
			);

		$this->questions = array(
			"QuestionA1" => "¿Cuáles son los síntomas cardinales que indican que el paciente tiene un asma no controlada?",
			"QuestionA2" => "Respecto a la auscultación del paciente usted puede decir que se trata de:",
			"QuestionA3" => "El diagnóstico espirométrico del paciente es:",
			"QuestionA4" => "Respecto al grado de severidad de la alteración espirométrica, esta se puede clasificar como:",
			"QuestionA5" => "El compromiso en la pequeña vía aérea del paciente es:",
			"QuestionB1" => "¿Cuáles son los hallazgos en la anamnesis y examen físico que orientan a la etiología del problema funcional respiratorio?",
			"QuestionB2" => "Respecto a la auscultación del paciente usted puede decir que se trata de:",
			"QuestionB3" => "El diagnóstico espirométrico del paciente es:",
			"QuestionB4" => "Respecto al grado de severidad de la alteración espirométrica, esta se puede clasificar como:",
			"QuestionB5" => "Existió respuesta a broncodilatadores en la espirometría a nivel de:",
			"QuestionC1" => "La respuesta a broncodilatadores fue:",
			"QuestionC2" => "Respecto a la auscultación del paciente usted puede decir que se trata de:",
			"QuestionC3" => "El diagnóstico espirométrico del paciente es:",
			"QuestionC4" => "Respecto al grado de severidad de la alteración espirométrica, esta se puede clasificar como:",
			"QuestionC5" => "El tratamiento a seguir es:"
			);

		$this->answers = array(
			"AnswerA10" => "Estornudos",
			"AnswerA11" => "Tos nocturna",
			"AnswerA12" => "Rinorrea",
			"AnswerA20" => "Roncus",
			"AnswerA21" => "Estertor fino",
			"AnswerA22" => "Sibilancia",
			"AnswerA30" => "Incapacidad ventilatoria restrictiva",
			"AnswerA31" => "Incapacidad ventilatoria mixta",
			"AnswerA32" => "Incapacidad ventilatoria obstructiva",
			"AnswerA40" => "Leve",
			"AnswerA41" => "Moderado",
			"AnswerA42" => "Severo",
			"AnswerA50" => "Está presente",
			"AnswerA51" => "No está presente",
			"AnswerA52" => "No se puede determinar",
			"AnswerB10" => "Antecedente de enfermedad de Duchenne",
			"AnswerB11" => "Tos",
			"AnswerB12" => "Disnea",
			"AnswerB20" => "Roncus",
			"AnswerB21" => "Estertor fino",
			"AnswerB22" => "Sibilancia",
			"AnswerB30" => "Incapacidad ventilatoria restrictiva",
			"AnswerB31" => "Incapacidad ventilatoria mixta",
			"AnswerB32" => "Incapacidad ventilatoria obstructiva",
			"AnswerB40" => "Leve",
			"AnswerB41" => "Moderado",
			"AnswerB42" => "Severo",
			"AnswerB50" => "Gran vía aérea",
			"AnswerB51" => "Pequeña vía aérea",
			"AnswerB52" => "No hay respuesta a broncodilatadores",
			"AnswerC10" => "Positiva",
			"AnswerC11" => "Negativa",
			"AnswerC12" => "No valorable",
			"AnswerC20" => "Roncus",
			"AnswerC21" => "Estertor fino",
			"AnswerC22" => "Sibilancia",
			"AnswerC30" => "Incapacidad ventilatoria restrictiva",
			"AnswerC31" => "Incapacidad ventilatoria mixta",
			"AnswerC32" => "Incapacidad ventilatoria obstructiva",
			"AnswerC40" => "Leve",
			"AnswerC41" => "Moderado",
			"AnswerC42" => "Severo",
			"AnswerC50" => "Iniciar B2 de acción prolongada",
			"AnswerC51" => "Aumentar dosis de corticoide inhalado",
			"AnswerC52" => "Administrar corticoide oral por un mes"
			);

		$this->justifications = array(
			"QuestionA1" => "Tos nocturna y disnea con actividad física son síntomas cardinales de asma no controlada",
			"QuestionA5" => "El paciente tiene alteración de los flujos medios forzados",
			"QuestionB5" => "El paciente tiene alteración de los flujos medios forzados",
			"QuestionC1" => "FEV1 aumentó más de 12%",
			"QuestionC5" => "El siguiente paso según GINA 2015 es agregar B2 agonista de acción prolongada",
			"QuestionB1" => "El antecedente de enfermedad neuromuscular orienta hacia etiología restrictiva pulmonar"
			);

		$this->test_table = array(
			"CaseA" => $this->test_tableA,
			"CaseB" => $this->test_tableB,
			"CaseC" => $this->test_tableC
			);

		$this->score = array(
			"" => "Sin responder",
			"false" => "Falso",
			"true" => "Correcto"
			);

		$this->approvedMessage = array(
			"false" => "Has reprobado el exámen",
			"true" => "¡Has aprobado la evaluación!"
			);

		$this->status = array(
			"Submitted" => "Exámen terminado con éxito.",
			"TimeEnded" => "El tiempo ha terminado y no se completó el exámen.",
			"Crash" => "Ocurrió un error durante el exámen.",
			"Dismissed" => "El usuario cerró la aplicación durante el exámen.",
			"Unknown" => "Ocurrió un error desconodico."
			);

	}

	public function getCaseContent($case){
		$content = array();
		$content["title"] = $this->title[$case];
		$content["gender"] = $this->gender[$case];
		$content["age"] = $this->age[$case];
		$content["weight"] = $this->weight[$case];
		$content["height"] = $this->height[$case];
		$content["symptoms"] = $this->symptoms[$case];
		$content["assessment"] = $this->assessment[$case];
		$content["antecedents"] = $this->antecedents[$case];
		$content["table"] = $this->test_table[$case];

		return $content;
	}

	public function getQuestion($question){
		return $this->questions[$question];
	}

	public function getAnswer($answer){
		return $this->answers[$answer];
	}

	public function getScore($score){
		return $this->score[$score];
	}

	public function getJustification($question){
		if(array_key_exists($question, $this->justifications)){
			return $this->justifications[$question];
		}else{
			return NULL;
		}
	}

	public function getApprovedMessage($result){
		return $this->approvedMessage[$result];
	}

	public function getStatus($status){
		return $this->status[$status];
	}
}