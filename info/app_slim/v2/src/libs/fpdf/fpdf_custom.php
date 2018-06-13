<?php

namespace App\v2\src\libs\fpdf;

use App\v2\src\libs\fpdf\FPDF;

// for protection propertie
if(function_exists('mcrypt_encrypt'))
{
    function RC4($key, $data)
    {
        return mcrypt_encrypt(MCRYPT_ARCFOUR, $key, $data, MCRYPT_MODE_STREAM, '');
    }
}
else
{
    function RC4($key, $data)
    {
        static $last_key, $last_state;

        if($key != $last_key)
        {
            $k = str_repeat($key, 256/strlen($key)+1);
            $state = range(0, 255);
            $j = 0;
            for ($i=0; $i<256; $i++){
                $t = $state[$i];
                $j = ($j + $t + ord($k[$i])) % 256;
                $state[$i] = $state[$j];
                $state[$j] = $t;
            }
            $last_key = $key;
            $last_state = $state;
        }
        else
            $state = $last_state;

        $len = strlen($data);
        $a = 0;
        $b = 0;
        $out = '';
        for ($i=0; $i<$len; $i++){
            $a = ($a+1) % 256;
            $t = $state[$a];
            $b = ($b+$t) % 256;
            $state[$a] = $state[$b];
            $state[$b] = $t;
            $k = $state[($state[$a]+$state[$b]) % 256];
            $out .= chr(ord($data[$i]) ^ $k);
        }
        return $out;
    }
}

class PDF extends FPDF{
	// Table
	var $processingTagble = false;
	var $arrCols = array();
	var $headerTextColor;
	var $rowTextColor;
	var $rowColors;
	var $borderCell;
	var $headerPoint = array();
	var $rowPoint = array();
	var $rowY;
	var $tableX;

	// Protection
    var $encrypted = false;  //whether document is protected
    var $Uvalue;             //U entry in pdf document
    var $Ovalue;             //O entry in pdf document
    var $Pvalue;             //P entry in pdf document
    var $enc_obj_id;         //encryption object id

	function Header(){

	}

	function Footer()
	{
	    // Go to 1.5 cm from bottom
	    $this->SetY(-15);
	    // Select Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Print current and total page numbers
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

	function TableHeader(){
		$this->SetFont('Arial','B',12);
		$colortText = !empty($this->headerTextColor);
		if($colortText)
			$this->SetTextColor($this->headerTextColor[0], $this->headerTextColor[1], $this->headerTextColor[2]);
		foreach ($this->arrCols as $col){ 
			$this->SetXY($this->headerPoint[0], $this->headerPoint[1]);
			$this->MultiCell($col['w'],16,$col['c'],$this->borderCell, 'C');
			$this->headerPoint[0] += $col['w'];
		}
	}

	function Row($dataRow){
		$colortText = !empty($this->headerTextColor);
		if($colortText)
			$this->SetTextColor($this->rowTextColor[0], $this->rowTextColor[1], $this->rowTextColor[2]);
		foreach ($this->arrCols as $i => $col){
			$this->SetXY($this->rowPoint[0], $this->rowPoint[1]);
			$this->MultiCell($col['w'],14,$dataRow[$i],$this->borderCell,$col['a']);
			$this->rowPoint[0] += $col['w'];

			if($this->GetY() >= $this->rowY)
				$this->rowY = $this->GetY();
		}
	}

	function CalcWidths($width,$align)
	{
	    $tableWidth=0;
	    foreach($this->arrCols as $i=>$col)
	    {
	        $w=$col['w'];
	        if($w==-1)
	            $w=$width/count($this->arrCols);
	        elseif(substr($w,-1)=='%')
	            $w=$w/100*$width;
	        $this->arrCols[$i]['w']=$w;
	        $tableWidth+=$w;
	    }
	    //Compute the abscissa of the table
	    if($align=='C')
	        $this->tableX=max(($this->w-$tableWidth)/2,0);
	    elseif($align=='R')
	        $this->tableX=max($this->w-$this->rMargin-$tableWidth,0);
	    else
	        $this->tableX=$this->lMargin;
	}

	function AddCol($caption = '', $width = -1, $align = 'L'){
		$this->arrCols[] = array('c' => $caption, 'w' => $width, 'a' => $align);
	}

	function ResetCols(){
		$this->arrCols = array();
		$this->rowY = $this->GetY();
	}


	function Table($rows, $prop = array()){
        $this->rowY = $this->GetY();
		// Properties
		if(!isset($prop['width']))
        $prop['width']=0;
    	if($prop['width']==0)
        $prop['width']=$this->w-$this->lMargin-$this->rMargin;
    	if(!isset($prop['align']))
        	$prop['align']='C';
		if(!isset($prop['headerTextColor']))
			$prop['headerTextColor'] = array();
		$this->headerTextColor = $prop['headerTextColor'];
		if(!isset($prop['rowTextColor']))
			$prop['rowTextColor'] = array();
		$this->rowTextColor = $prop['rowTextColor'];
		if(!isset($prop['border']))
			$prop['border'] = 1;
		$this->borderCell = $prop['border'];

		$this->CalcWidths($prop['width'],$prop['align']);
		$this->headerPoint = array($this->lMargin, $this->GetY());
		$this->TableHeader();
		$this->processingTagble = true;
		$this->SetFont('Arial','',11);
		$this->rowPoint = array($this->GetX(), $this->GetY());
		foreach ($rows as $row) {
			$this->Row($row);
			$this->rowPoint = array($this->lMargin, $this->rowY);
		}
		$this->processingTagble = false;
	}

	//**************************
	// function to set file password
    function SetProtection($permissions=array(), $user_pass='', $owner_pass=null)
    {
        $options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
        $protection = 192;
        foreach($permissions as $permission)
        {
            if (!isset($options[$permission]))
                $this->Error('Incorrect permission: '.$permission);
            $protection += $options[$permission];
        }
        if ($owner_pass === null)
            $owner_pass = uniqid(rand());
        $this->encrypted = true;
        $this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
                        "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
        $this->_generateencryptionkey($user_pass, $owner_pass, $protection);
    }

    // private protection methods
    function _putstream($s)
    {
        if ($this->encrypted)
            $s = RC4($this->_objectkey($this->n), $s);
        parent::_putstream($s);
    }

    function _textstring($s)
    {
        if (!$this->_isascii($s))
            $s = $this->_UTF8toUTF16($s);
        if ($this->encrypted)
            $s = RC4($this->_objectkey($this->n), $s);
        return '('.$this->_escape($s).')';
    }

    /**
    * Compute key depending on object number where the encrypted data is stored
    */
    function _objectkey($n)
    {
        return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
    }

    function _putresources()
    {
        parent::_putresources();
        if ($this->encrypted) {
            $this->_newobj();
            $this->enc_obj_id = $this->n;
            $this->_put('<<');
            $this->_putencryption();
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putencryption()
    {
        $this->_put('/Filter /Standard');
        $this->_put('/V 1');
        $this->_put('/R 2');
        $this->_put('/O ('.$this->_escape($this->Ovalue).')');
        $this->_put('/U ('.$this->_escape($this->Uvalue).')');
        $this->_put('/P '.$this->Pvalue);
    }

    function _puttrailer()
    {
        parent::_puttrailer();
        if ($this->encrypted) {
            $this->_put('/Encrypt '.$this->enc_obj_id.' 0 R');
            $this->_put('/ID [()()]');
        }
    }

    /**
    * Get MD5 as binary string
    */
    function _md5_16($string)
    {
        return pack('H*',md5($string));
    }

    /**
    * Compute O value
    */
    function _Ovalue($user_pass, $owner_pass)
    {
        $tmp = $this->_md5_16($owner_pass);
        $owner_RC4_key = substr($tmp,0,5);
        return RC4($owner_RC4_key, $user_pass);
    }

    /**
    * Compute U value
    */
    function _Uvalue()
    {
        return RC4($this->encryption_key, $this->padding);
    }

    /**
    * Compute encryption key
    */
    function _generateencryptionkey($user_pass, $owner_pass, $protection)
    {
        // Pad passwords
        $user_pass = substr($user_pass.$this->padding,0,32);
        $owner_pass = substr($owner_pass.$this->padding,0,32);
        // Compute O value
        $this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
        // Compute encyption key
        $tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
        $this->encryption_key = substr($tmp,0,5);
        // Compute U value
        $this->Uvalue = $this->_Uvalue();
        // Compute P value
        $this->Pvalue = -(($protection^255)+1);
    }
}
?>