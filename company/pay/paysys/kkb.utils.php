<?php
/*
-----===++[Additional procedures by Pavel Nedelin (soius@soius.kz;tecc@mail.kz)		]++===-----
-----===++[05.10.2006									]++===-----
-----===++[SOIUS Ltd. 2006 (soius@soius.kz) http://www.soius.kz				]++===-----

-----===++[�������������� ��������� Pavel Nedelin (soius@soius.kz;tecc@mail.kz)		]++===-----
-----===++[05.10.2006									]++===-----
-----===++[SOIUS Ltd. 2006 (soius@soius.kz) http://www.soius.kz				]++===-----
*/		

	/* --------------------------------------------
		������ ��� ��������/�������� �������
		���������/��������� ������.
	    	KKBSign class
		-------------
		by Kirsanov Anton (webcompass@list.ru)
		01.06.2006	
		^^^^^^^^^^^^^^^
		������ �������:
		// ---------------------------------------
		// �������� ���������� ����� � PEM ������� 
			load_private_key($file, $password); 
		// ---------------------------------------
		// �������� ������
			invert(); 			
		// ---------------------------------------
		// ������� ����������� ������ ������ $str
			sign($str);			
		// ---------------------------------------
		// ������� ����������� ������ ������ $str
		// � ����������� � Base64
			sign64($str); 
		// ---------------------------------------
		// �������� ��������� ������ $file, 
		// �������� �� ������ $str ����������� 
		// ��������� ������ ������� $data.
			check_sign($data, $str, $file);
		// ---------------------------------------
		// �������� ��������� ������ $file, 
		// �������� �� ������ $str � Base64
		// ����������� ��������� ������ ������� $data.
			check_sign64($data, $str, $file);
		// ---------------------------------------
		// ��������� ������ Open SSL
		// $error - ������ ������ ���������� openssl_error_string()
		// ��������� ������ ������:
		// ecode - ���������� ����� ������ 
		// estatus - ��������� �������� ������
			parse_errors($error);

	   ------------------------------------------*/

class KKBsign {
	// -----------------------------------------------------------------------------------------------
	function load_private_key($filename, $password = NULL){
		$this->ecode=0;
		if(!is_file($filename)){ $this->ecode=4; $this->estatus = "[KEY_FILE_NOT_FOUND]"; return false;};
		$c = file_get_contents($filename);
		if(strlen(trim($password))>0){$prvkey = openssl_get_privatekey($c, $password); $this->parse_errors(openssl_error_string());
		} else {$prvkey = openssl_get_privatekey($c); $this->parse_errors(openssl_error_string());};
		if(is_resource($prvkey)){ $this->private_key = $prvkey; return $c;}
		return false;
	}
	// -----------------------------------------------------------------------------------------------
	// ��������� ����� ��������
	function invert(){ $this->invert = 1;}
	// -----------------------------------------------------------------------------------------------
	// ������� �������� ������
	function reverse($str){	return strrev($str);}
	// -----------------------------------------------------------------------------------------------
	function sign($str){
		if($this->private_key){
			openssl_sign($str, $out, $this->private_key);
			if($this->invert == 1) $out = $this->reverse($out);
			//openssl_free_key($this->private_key);
			return $out;
		};
	}
	// -----------------------------------------------------------------------------------------------
	function sign64($str){	return base64_encode($this->sign($str));}
	// -----------------------------------------------------------------------------------------------
	function check_sign($data, $str, $filename){
		if($this->invert == 1)  $str = $this->reverse($str);
		if(!is_file($filename)){ $this->ecode=4; $this->estatus = "[KEY_FILE_NOT_FOUND]"; return 2;};
		$this->pubkey = file_get_contents($filename);
		$pubkeyid = openssl_get_publickey($this->pubkey);
		$this->parse_errors(openssl_error_string());
		if (is_resource($pubkeyid)){
			$result = openssl_verify($data, $str, $pubkeyid);
			$this->parse_errors(openssl_error_string());
			openssl_free_key($pubkeyid);
			return $result;
		};
		return 3;
	}
	// -----------------------------------------------------------------------------------------------
	function check_sign64($data, $str, $filename){
		return $this->check_sign($data, base64_decode($str), $filename);
	}
	// -----------------------------------------------------------------------------------------------
	function parse_errors($error){
		// -----===++[Parses error to errorcode and message]++===-----
		/*error:0906D06C - Error reading Certificate. Verify Cert type.
		error:06065064 - Bad decrypt. Verify your Cert password or Cert type.
		error:0906A068 - Bad password read. Maybe empty password.*/
		if (strlen($error)>0){
			if (strpos($error,"error:0906D06C")>0){$this->ecode = 1; $this->estatus = "Error reading Certificate. Verify Cert type.";};
			if (strpos($error,"error:06065064")>0){$this->ecode = 2; $this->estatus = "Bad decrypt. Verify your Cert password or Cert type.";};
			if (strpos($error,"error:0906A068")>0){$this->ecode = 3; $this->estatus = "Bad password read. Maybe empty password.";};
			if ($this->ecode = 0){$this->ecode = 255; $this->estatus = $error;};
		};
	}
};

/*
-----===++[Additional procedures by Pavel Nedelin (soius@soius.kz;tecc@mail.kz)		]++===-----
-----===++[03.10.2006 - 05.10.2006							]++===-----
-----===++[(p) SOIUS Ltd. 2006 (soius@soius.kz)						]++===-----

-----===++[�������������� ��������� Pavel Nedelin (soius@soius.kz;tecc@mail.kz)		]++===-----
-----===++[03.10.2006 - 05.10.2006							]++===-----
-----===++[(p) SOIUS Ltd. 2006 (soius@soius.kz)						]++===-----
*/		

// -----===++[Additional procedures start/�������������� ��������� ������]++===-----
class xml {
	// -----===++[Parse XML to ARRAY]++===-----
	// methods:
	// parse($data) - return array in format listed below
	// variables:
	// $data - string: incoming XML
	// 
	// Array format:
	// array index:"TAG_"+tagNAME = value: tagNAME 
	// example:$array['TAG_BANK'] = "BANK" 
	// array index:NAME+"_"+ATTRIBUTE_NAME = value: ATTRIBUTE_VALUE 
	// example:$array['BANK_NAME'] = "Kazkommertsbank JSC"
	// 
	// -----===++[����� XML � ������]++===-----
	// ������:
	// parse($data) - ���������� ������ � ������� ��������� ����
	// ����������:
	// $data - ������: �������� XML
	// 
	// ������ �������:
	// ������ � �������:"TAG_"+������� = ��������: ������� 
	// ������:$array['TAG_BANK'] = "BANK"
	// ������ � �������:�������+"_"+������������ = ��������: ����������������� 
	// ������:$array['BANK_NAME'] = "Kazkommertsbank JSC"

    var $parser;
	var $xarray = array();
	var $lasttag;
	
    function xml()
    {   $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, true);
        xml_set_element_handler($this->parser, "tag_open", "tag_close");
        xml_set_character_data_handler($this->parser, "cdata");
	}

    function parse($data)
    { 
        xml_parse($this->parser, $data);
		ksort($this->xarray,SORT_STRING);
		return $this->xarray;
	}

    function tag_open($parser, $tag, $attributes)
    {
		$this->lasttag = $tag;
		$this->xarray['TAG_'.$tag] = $tag;
			if (is_array($attributes)){
				foreach ($attributes as $key => $value) {
					$this->xarray[$tag.'_'.$key] = $value;
				};
			};
    }

    function cdata($parser, $cdata)
    {	$tag = $this->lasttag;
        $this->xarray[$tag.'_CHARDATA'] = $cdata;
    }

    function tag_close($parser, $tag)
    {}
}
// -----------------------------------------------------------------------------------------------
function process_XML($filename,$reparray) {
	// -----===++[Process XML template - replaces tags in file to array values]++===-----
	// variables:
	// $filename - string: name of XML template
	// $reparray - array: data to replace
	//
	// XML template tag format:[tag name] example: [MERCHANT_CERTIFICATE_ID]
	//
	// Functionality:Searches file for array index and replaces to value
	// example: in array > $reparray['MERCHANT_CERTIFICATE_ID'] = "12345"
	// before replace: cert_id="[MERCHANT_CERTIFICATE_ID]"
	// after replace: cert_id="12345" 
	// if operation successful returns file contents with replaced values
	// if template not found returns "[ERROR]"
	//
	// -----===++[��������� XML ������� - ������ ����� � ����� �� �������� �� �������]++===-----
	// ����������:
	// $filename - ������: ��� XML �������
	// $reparray - ������: ������ ��� ������
	//
	// ������ ����� � XML �������:[tag name] ������: [MERCHANT_CERTIFICATE_ID]
	//
	// ����������������: ���� � ������� ������� ������� � �������� �� �� ��������
	// ������: � ������� > $reparray['MERCHANT_CERTIFICATE_ID'] = "12345"
	// ����� �������: cert_id="[MERCHANT_CERTIFICATE_ID]"
	// ����� ������: cert_id="12345" 
	// ���� �������� ������ ������� ���������� ����� ����� � ����������� ����������
	// ���� ���� ������� �� ����� ���������� "[ERROR]"

	if(is_file($filename)){
		$content = file_get_contents($filename);
		foreach ($reparray as $key => $value) {$content = str_replace("[".$key."]",$value,$content);};
		return $content;
	} else {return "[ERROR]";};
};
// -----------------------------------------------------------------------------------------------
function split_sign($xml,$tag){
	// -----===++[Process XML string to array of values]++===-----
	// variables:
	// $xml - string: xml string
	// $tag - string: split tag name
	// $array["LETTER"] = an XML section enclosed in <$tag></$tag>
	// $array["SIGN"] = an XML sign section enclosed in <$tag+"_sign"></$tag+"_sign">
	// $array["RAWSIGN"] = an XML sign section with stripped <$tag+"_sign"></$tag+"_sign"> tags
	// example: 
	// income data:
	// $xml = "<order order_id="12345"><department amount="10"/></order><order_sign type="SHA/RSA">ljkhsdfmnuuewrhkj</order_sign>"
	// $tag = "ORDER"
	// result:
	// $array["LETTER"] = "<order order_id="12345"><department amount="10"/></order>"
	// $array["SIGN"] = "<order_sign type="SHA/RSA">ljkhsdfmnuuewrhkj</order_sign>"
	// $array["RAWSIGN"] = "ljkhsdfmnuuewrhkj"
	//
	// -----===++[��������� XML ������ � ��������������]++===-----
	// ����������:
	// $xml - ������: xml ������
	// $tag - ������: ��� ���� �����������
	// $array["LETTER"] = XML ������ ����������� � <$tag></$tag>
	// $array["SIGN"] = XML ������ ������� ����������� � <$tag+"_sign"></$tag+"_sign">
	// $array["RAWSIGN"] = XML ������ ������� � ����������� <$tag+"_sign"></$tag+"_sign"> ������
	// ������: 
	// ������� ������:
	// $xml = "<order order_id="12345"><department amount="10"/></order><order_sign type="SHA/RSA">ljkhsdfmnuuewrhkj</order_sign>"
	// $tag = "ORDER"
	// ���������:
	// $array["LETTER"] = "<order order_id="12345"><department amount="10"/></order>"
	// $array["SIGN"] = "<order_sign type="SHA/RSA">ljkhsdfmnuuewrhkj</order_sign>"
	// $array["RAWSIGN"] = "ljkhsdfmnuuewrhkj"


	$array = array();
	$letterst = stristr($xml,"<".$tag);
	$signst = stristr($xml,"<".$tag."_SIGN");
	$signed = stristr($xml,"</".$tag."_SIGN");
	$doced = stristr($signed,">");
	$array['LETTER'] = substr($letterst,0,-strlen($signst));
	$array['SIGN'] = substr($signst,0,-strlen($doced)+1);
	$rawsignst = stristr($array['SIGN'],">");
	$rawsigned = stristr($rawsignst,"</");
	$array['RAWSIGN'] = substr($rawsignst,1,-strlen($rawsigned));
	return $array;
}
// -----------------------------------------------------------------------------------------------
function process_request($order_id,$currency_code,$amount,$config_file,$b64=true) {
	// -----===++[Process incoming data to full bank request]++===-----
	// variables:
	// $order_id - integer: order index - recoded to 6 digit format with leaded zero
	// $currency_code - string: preferred currency codes 840-USD, 398-Tenge
	// $amount - integer: total payment amount
	// $config_file - string: full path to config file
	// $b64 - boolean: flag to encode result in base64 default = true
	// example: 
	// income data: process_request(1,"398",10,"config.txt")
	// result:
	// string = "<document><merchant cert_id="123" name="test"><order order_id="000001" amount="10" currency="398">
	// <department merchant_id="12345" amount="10"/></order></merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"
	//
	// -----===++[��������� ������� ������ � ������ ���������� ������]++===-----
	// ����������:
	// $order_id - �����: ����� ������ - �������������� � 6 ������� ������ � �������� ������
	// $currency_code - ������: �������� ����� ����� 840-USD, 398-Tenge
	// $amount - �����: ����� ����� �������
	// $config_file - ������: ������ ���� � ����� ������������
	// $b64 - �������: ���� ��� ����������� ���������� � base64 �� ��������� = true
	// ������: 
	// ������� ������: process_request(1,"398",10,"config.txt")
	// ���������:
	// ������ = "<document><merchant cert_id="123" name="test"><order order_id="000001" amount="10" currency="398">
	// <department merchant_id="12345" amount="10"/></order></merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"

	
	if(is_file($config_file)){
		$config=parse_ini_file($config_file,0);
	} else { return "Config not exist";};
	
	if (strlen($order_id)>0){
		if (is_numeric($order_id)){
			if ($order_id>0){
				$order_id = sprintf ("%06d",$order_id);
			} else { return "Null Order ID";};
		} else { return "Order ID must be number";};	
	} else { return "Empty Order ID";};
	
	if (strlen($currency_code)==0){return "Empty Currency code";};
	if ($amount==0){return "Nothing to charge";};
	if (strlen($config['PRIVATE_KEY_FN'])==0){return "Path for Private key not found";};
	if (strlen($config['XML_TEMPLATE_FN'])==0){return "Path for Private key not found";};

	$request = array();
	$request['MERCHANT_CERTIFICATE_ID'] = $config['MERCHANT_CERTIFICATE_ID'];
	$request['MERCHANT_NAME'] = $config['MERCHANT_NAME'];
	$request['ORDER_ID'] = $order_id;
	$request['CURRENCY'] = $currency_code;
	$request['MERCHANT_ID'] = $config['MERCHANT_ID'];
	$request['AMOUNT'] = $amount;
	
	$kkb = new KKBSign();
	$kkb->invert();
	if (!$kkb->load_private_key($config['PRIVATE_KEY_FN'],$config['PRIVATE_KEY_PASS'])){
		if ($kkb->ecode>0){return $kkb->estatus;};
	};
	
	$result = process_XML($config['XML_TEMPLATE_FN'],$request);
	if (strpos($result,"[RERROR]")>0){ return "Error reading XML template.";};
	$result_sign = '<merchant_sign type="RSA">'.$kkb->sign64($result).'</merchant_sign>';
	$xml = "<document>".$result.$result_sign."</document>";
	if ($b64){return base64_encode($xml);} else {return $xml;};
};
// -----------------------------------------------------------------------------------------------
function process_response($response,$config_file) {
	// -----===++[Process incoming XML to array of values with verifying electronic sign]++===-----
	// variables:
	// $response - string: XML response from bank
	// $config_file - string: full path to config file
	// returns:
	// array with parced XML and sign verifying result
	// if array has in values "DOCUMENT" following values available
	// $data['CHECKRESULT'] = "[SIGN_GOOD]" - sign verify successful
	// $data['CHECKRESULT'] = "[SIGN_BAD]" - sign verify unsuccessful
	// $data['CHECKRESULT'] = "[SIGN_CHECK_ERROR]" - an error has occured while sign processing full error in that string after ":"
	// if array has in values "ERROR" following values available
	// $data["ERROR_TYPE"] = "ERROR" - internal error occured
	// $data["ERROR"] = "Config not exist" - the configuration file not found
	// $data["ERROR_TYPE"] = "system" - external error in bank process
	// $data["ERROR_TYPE"] = "auth" - external autentication error in bank process
	// example: 
	// income data: 
	// $response = "<document><bank><customer name="123"><merchant name="test merch">
	// <order order_id="000001" amount="10" currency="398"><department amount="10"/></order></merchant>
	// <merchant_sign type="RSA"/></customer><customer_sign type="RSA"/><results timestamp="2001-01-01 00:00:00">
	// <payment amount="10" response_code="00"/></results></bank>
	// <bank_sign type="SHA/RSA">;skljfasldimn,samdbfyJHGkmbsa;fliHJ:OIUHkjbn</bank_sign ></document>"
	// $config_file = "config.txt"
	// result:
	// $data['BANK_SIGN_CHARDATA'] = ";skljfasldimn,samdbfyJHGkmbsa;fliHJ:OIUHkjbn"
	// $data['BANK_SIGN_TYPE'] = "SHA/RSA"
	// $data['CUSTOMER_NAME'] = "123"
	// $data['CUSTOMER_SIGN_TYPE'] = "RSA"
	// $data['DEPARTMENT_AMOUNT'] = "10"
	// $data['MERCHANT_NAME'] = "test merch"
	// $data['MERCHANT_SIGN_TYPE'] = "RSA"
	// $data['ORDER_AMOUNT'] = "10"
	// $data['ORDER_CURRENCY'] = "398"
	// $data['ORDER_ORDER_ID'] = "000001"
	// $data['PAYMENT_AMOUNT'] = "10"
	// $data['PAYMENT_RESPONSE_CODE'] = "00"
	// $data['RESULTS_TIMESTAMP'] = "2001-01-01 00:00:00"
	// $data['TAG_BANK'] = "BANK"
	// $data['TAG_BANK_SIGN'] = "BANK_SIGN"
	// $data['TAG_CUSTOMER'] = "CUSTOMER"
	// $data['TAG_CUSTOMER_SIGN'] = "CUSTOMER_SIGN"
	// $data['TAG_DEPARTMENT'] = "DEPARTMENT"
	// $data['TAG_DOCUMENT'] = "DOCUMENT"
	// $data['TAG_MERCHANT'] = "MERCHANT"
	// $data['TAG_MERCHANT_SIGN'] = "MERCHANT_SIGN"
	// $data['TAG_ORDER'] = "ORDER"
	// $data['TAG_PAYMENT'] = "PAYMENT"
	// $data['TAG_RESULTS'] = "RESULTS"
	// $data['CHECKRESULT'] = "[SIGN_GOOD]"
	//
	// -----===++[������������������ XML � ������ �������� � ��������� ����������� �������]++===-----
	// ����������:
	// $response - ������: XML ����� �� �����
	// $config_file - ������: ������ ���� � ����� ������������
	// ����������:
	// ������ � ���������� XML � ����������� �������� �������
	// ���� � ������� ���� �������� "DOCUMENT" �������� ��������� ��������
	// $data['CHECKRESULT'] = "[SIGN_GOOD]" - �������� ������� �������
	// $data['CHECKRESULT'] = "[SIGN_BAD]" - �������� ������� ���������
	// $data['CHECKRESULT'] = "[SIGN_CHECK_ERROR]" - ��������� ������ �� ����� ��������� �������, ������ �������� ������ � ���� �� ������ ����� ":"
	// ���� � ������� ���� �������� "ERROR" �������� ��������� ��������
	// $data["ERROR_TYPE"] = "ERROR" - ��������� ���������� ������
	// $data["ERROR"] = "Config not exist" - �� ������ ���� ������������
	// $data["ERROR_TYPE"] = "system" - ������� ������ ��� ��������� ������ � �����
	// $data["ERROR_TYPE"] = "auth" - ������� ������ ����������� ��� ��������� ������ � �����
	// ������: 
	// ������� ������: 
	// $response = "<document><bank><customer name="123"><merchant name="test merch">
	// <order order_id="000001" amount="10" currency="398"><department amount="10"/></order></merchant>
	// <merchant_sign type="RSA"/></customer><customer_sign type="RSA"/><results timestamp="2001-01-01 00:00:00">
	// <payment amount="10" response_code="00"/></results></bank>
	// <bank_sign type="SHA/RSA">;skljfasldimn,samdbfyJHGkmbsa;fliHJ:OIUHkjbn</bank_sign ></document>"
	// $config_file = "config.txt"
	// ���������:
	// $data['BANK_SIGN_CHARDATA'] = ";skljfasldimn,samdbfyJHGkmbsa;fliHJ:OIUHkjbn"
	// $data['BANK_SIGN_TYPE'] = "SHA/RSA"
	// $data['CUSTOMER_NAME'] = "123"
	// $data['CUSTOMER_SIGN_TYPE'] = "RSA"
	// $data['DEPARTMENT_AMOUNT'] = "10"
	// $data['MERCHANT_NAME'] = "test merch"
	// $data['MERCHANT_SIGN_TYPE'] = "RSA"
	// $data['ORDER_AMOUNT'] = "10"
	// $data['ORDER_CURRENCY'] = "398"
	// $data['ORDER_ORDER_ID'] = "000001"
	// $data['PAYMENT_AMOUNT'] = "10"
	// $data['PAYMENT_RESPONSE_CODE'] = "00"
	// $data['RESULTS_TIMESTAMP'] = "2001-01-01 00:00:00"
	// $data['TAG_BANK'] = "BANK"
	// $data['TAG_BANK_SIGN'] = "BANK_SIGN"
	// $data['TAG_CUSTOMER'] = "CUSTOMER"
	// $data['TAG_CUSTOMER_SIGN'] = "CUSTOMER_SIGN"
	// $data['TAG_DEPARTMENT'] = "DEPARTMENT"
	// $data['TAG_DOCUMENT'] = "DOCUMENT"
	// $data['TAG_MERCHANT'] = "MERCHANT"
	// $data['TAG_MERCHANT_SIGN'] = "MERCHANT_SIGN"
	// $data['TAG_ORDER'] = "ORDER"
	// $data['TAG_PAYMENT'] = "PAYMENT"
	// $data['TAG_RESULTS'] = "RESULTS"
	// $data['CHECKRESULT'] = "[SIGN_GOOD]"


	if(is_file($config_file)){
		$config=parse_ini_file($config_file,0);
	} else {$data["ERROR"] = "Config not exist";$data["ERROR_TYPE"] = "ERROR"; return $data;};
	
	$xml_parser = new xml();
	$result = $xml_parser->parse($response);
	if (in_array("ERROR",$result)){
		return $result;
	};
	if (in_array("DOCUMENT",$result)){
		$kkb = new KKBSign();
		$kkb->invert();
		$data = split_sign($response,"BANK");
		$check = $kkb->check_sign64($data['LETTER'], $data['RAWSIGN'], $config['PUBLIC_KEY_FN']);
		if ($check == 1)
			$data['CHECKRESULT'] = "[SIGN_GOOD]";
		elseif ($check == 0)
			$data['CHECKRESULT'] = "[SIGN_BAD]";
		else
    		$data['CHECKRESULT'] = "[SIGN_CHECK_ERROR]: ".$kkb->estatus;
		return array_merge($result,$data);
	};
	return "[XML_DOCUMENT_UNKNOWN_TYPE]";
};
// -----------------------------------------------------------------------------------------------
function process_refund($reference, $approval_code, $order_id, $currency_code, $amount, $reason, $config_file) {
	// -----===++[Process refund for processed transaction]++===-----
	// variables:
	// $reference - integer: transaction ID
	// $approval_code - string: transaction approval code
	// $order_id - integer: order index - recoded to 6 digit format with leaded zero
	// $currency_code - string: preferred currency codes 840-USD, 398-Tenge
	// $amount - integer: total payment amount
	// $reason - string: reason of the refund
	// $config_file - string: full path to config file
	// example: 
	// income data: process_request(016604285111, 12345, 1, "398", 10, "Order cancelled", "config.txt")
	// result:
	// string = "<document><merchant cert_id="123" name="test"><command type="reverse"/>
	// <payment reference="016604285111" orderid="000001" amount="10" currency="398" />
	// <reason>Order cancelled</reason>
	// </merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"
	//
	// -----===++[������� ������� �� ��� ���������� ����������]++===-----
	// ����������:
	// $reference - integer: ID ����������
	// $approval_code - string: ��� ������������� ����������
	// $order_id - �����: ����� ������ - �������������� � 6 ������� ������ � �������� ������
	// $currency_code - ������: �������� ����� ����� 840-USD, 398-Tenge
	// $amount - �����: ����� ����� �������
	// $reason - ������: ������� �������� �������
	// $config_file - ������: ������ ���� � ����� ������������
	// ������: 
	// ������� ������: process_request(016604285111, 12345, 1, "398", 10, "Order cancelled", "config.txt")
	// ���������:
	// ������ = "<document><merchant cert_id="123" name="test"><command type="reverse"/>
	// <payment reference="016604285111" orderid="000001" amount="10" currency="398" />
	// <reason>Order cancelled</reason>
	// </merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"

	if(!$reference) return "Empty Transaction ID";
	
	if(is_file($config_file)){
		$config=parse_ini_file($config_file,0);
	} else { return "Config not exist";};
	
	if (strlen($order_id)>0){
		if (is_numeric($order_id)){
			if ($order_id>0){
				$order_id = sprintf ("%06d",$order_id);
			} else { return "Null Order ID";};
		} else { return "Order ID must be number";};	
	} else { return "Empty Order ID";};
	
	if(!$reason) $reason = "Transaction revert";
	
	if (strlen($currency_code)==0){return "Empty Currency code";};
	if ($amount==0){return "Nothing to charge";};
	if (strlen($config['PRIVATE_KEY_FN'])==0){return "Path for Private key not found";};
	if (strlen($config['XML_COMMAND_TEMPLATE_FN'])==0){return "Path to xml command template not found";};

	$request = array();
	$request['MERCHANT_ID'] = $config['MERCHANT_ID'];
	$request['MERCHANT_NAME'] = $config['MERCHANT_NAME'];
	$request['COMMAND'] = 'reverse';
	$request['REFERENCE_ID'] = $reference;
	$request['APPROVAL_CODE'] = $approval_code;
	$request['ORDER_ID'] = $order_id;
	$request['CURRENCY'] = $currency_code;
	$request['MERCHANT_ID'] = $config['MERCHANT_ID'];
	$request['AMOUNT'] = $amount;
	$request['REASON'] = $reason;
	
	$kkb = new KKBSign();
	$kkb->invert();
	if (!$kkb->load_private_key($config['PRIVATE_KEY_FN'],$config['PRIVATE_KEY_PASS'])){
		if ($kkb->ecode>0){return $kkb->estatus;};
	};
	
	$result = process_XML($config['XML_COMMAND_TEMPLATE_FN'],$request);
	if (strpos($result,"[RERROR]")>0){ return "Error reading XML template.";};
	$result_sign = '<merchant_sign type="RSA" cert_id="' . $config['MERCHANT_CERTIFICATE_ID'] . '">'.$kkb->sign64($result).'</merchant_sign>';
	$xml = "<document>".$result.$result_sign."</document>";
	return $xml;
};
// -----------------------------------------------------------------------------------------------
function process_complete($reference, $approval_code, $order_id, $currency_code, $amount, $config_file) {
	// -----===++[Process complete for processed transaction]++===-----
	// variables:
	// $reference - integer: transaction ID
	// $approval_code - string: transaction approval code
	// $order_id - integer: order index - recoded to 6 digit format with leaded zero
	// $currency_code - string: preferred currency codes 840-USD, 398-Tenge
	// $amount - integer: total payment amount
	// $config_file - string: full path to config file
	// example: 
	// income data: process_request(016604285111, 12345, 1, "398", 10, "config.txt")
	// result:
	// string = "<document><merchant cert_id="123" name="test"><command type="complete"/>
	// <payment reference="016604285111" orderid="000001" amount="10" currency="398" />
	// <reason></reason>
	// </merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"
	//
	// -----===++[������� ������� �� ��� ���������� ����������]++===-----
	// ����������:
	// $reference - integer: ID ����������
	// $approval_code - string: ��� ������������� ����������
	// $order_id - �����: ����� ������ - �������������� � 6 ������� ������ � �������� ������
	// $currency_code - ������: �������� ����� ����� 840-USD, 398-Tenge
	// $amount - �����: ����� ����� �������
	// $config_file - ������: ������ ���� � ����� ������������
	// ������: 
	// ������� ������: process_request(016604285111, 12345, 1, "398", 10, "config.txt")
	// ���������:
	// ������ = "<document><merchant cert_id="123" name="test"><command type="complete"/>
	// <payment reference="016604285111" orderid="000001" amount="10" currency="398" />
	// <reason>Order cancelled</reason>
	// </merchant><merchant_sign type="RSA">LJlkjkLHUgkjhgmnYI</merchant_sign>
	// </document>"

	if(!$reference) return "Empty Transaction ID";
	
	if(is_file($config_file)){
		$config=parse_ini_file($config_file,0);
	} else { return "Config not exist";};
	
	if (strlen($order_id)>0){
		if (is_numeric($order_id)){
			if ($order_id>0){
				$order_id = sprintf ("%06d",$order_id);
			} else { return "Null Order ID";};
		} else { return "Order ID must be number";};	
	} else { return "Empty Order ID";};
	
	if (strlen($currency_code)==0){return "Empty Currency code";};
	if ($amount==0){return "Nothing to charge";};
	if (strlen($config['PRIVATE_KEY_FN'])==0){return "Path for Private key not found";};
	if (strlen($config['XML_COMMAND_TEMPLATE_FN'])==0){return "Path for xml command template not found";};

	$request = array();
	$request['MERCHANT_ID'] = $config['MERCHANT_ID'];
	$request['MERCHANT_NAME'] = $config['MERCHANT_NAME'];
	$request['COMMAND'] = 'complete';
	$request['REFERENCE_ID'] = $reference;
	$request['APPROVAL_CODE'] = $approval_code;
	$request['ORDER_ID'] = $order_id;
	$request['CURRENCY'] = $currency_code;
	$request['MERCHANT_ID'] = $config['MERCHANT_ID'];
	$request['AMOUNT'] = $amount;
	$request['REASON'] = '';
	
	$kkb = new KKBSign();
	$kkb->invert();
	if (!$kkb->load_private_key($config['PRIVATE_KEY_FN'],$config['PRIVATE_KEY_PASS'])){
		if ($kkb->ecode>0){return $kkb->estatus;};
	};
	
	$result = process_XML($config['XML_COMMAND_TEMPLATE_FN'],$request);
	if (strpos($result,"[RERROR]")>0){ return "Error reading XML template.";};
	$result_sign = '<merchant_sign type="RSA" cert_id="' . $config['MERCHANT_CERTIFICATE_ID'] . '">'.$kkb->sign64($result).'</merchant_sign>';
	$xml = "<document>".$result.$result_sign."</document>";
	return $xml;
};
// -----===++[Additional procedures end/�������������� ��������� �����]++===-----
?>