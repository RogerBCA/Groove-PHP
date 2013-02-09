<?php
session_start();
$json = json_decode($json);


class randomColorGenerator {
	private $hexColor = array (
		"0",
		"1",
		"2",
		"3",
		"4",
		"5",
		"6",
		"7",
		"8",
		"9",
		"A",
		"B",
		"C",
		"D",
		"E",
		"F"
	);
	private $newColor = "";
	private $colorBag = array ();
	function getColor() {
		$this->newColor = $this->hexColor[$this->genRandom()] .
		$this->hexColor[$this->genRandom()] .
		$this->hexColor[$this->genRandom()] .
		$this->hexColor[$this->genRandom()] .
		$this->hexColor[$this->genRandom()] .
		$this->hexColor[$this->genRandom()];

		if (!in_array($this->newColor, $this->colorBag)) {
			$this->colorBag[] = $this->newColor;
			return $this->newColor;
		}
	}
	function genRandom() {
		srand((float) microtime() * 10000000);
		$random_col_keys = array_rand($this->hexColor, 2);
		return $random_col_keys[0];
	}
}
function prep_token($method, $token, $with) {
	$obj = new randomColorGenerator();
	$hex2 = $obj->getColor();
	return $hex2 . sha1($method . ":" . $token . $with . $hex2);
}

function gzdecode($data)
{
   return gzinflate(substr($data,10,-8));
} 

$groovefix = json_decode(file_get_contents("../GrooveFix.json"));
if ($_SESSION AND $_GET["query"]){

$h["session"] = $_SESSION["id"];
$h["uuid"] = $_SESSION["uuid"];
$h["privacy"] = 0;
$h["Country"]["CC1"] = "0";
$h["Country"]["CC2"] = "0";
$h["Country"]["CC3"] = "0";
$h["Country"]["CC4"] = "0";
$h["Country"]["ID"] = "1";
$ps["header"] = $h;
$ps["header"]["client"] = "htmlshark";
$ps["header"]["clientRevision"] = $groovefix->htmlshark->GrooveClientRevision;
$ps["header"]["token"] = prep_token("getAutocomplete", $_SESSION["token"], $groovefix->htmlshark->GrooveStaticRandomizer);
$ps["method"] = "getAutocomplete";
$ps["parameters"]["query"] = $_GET["query"];
$ps["parameters"]["type"] = "song";

$opt = array (
		'http' => array (
			'method' => "POST",
			'header' => "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1\r\n" .
			"Accept-Encoding: gzip\r\n".
			"Referer: http://grooveshark.com/JSQueue.swf?20110216.04\r\n" .
			"Cookie: PHPSESSID=".$_SESSION["id"]."\r\n" .
			"Content-Type: application/json\r\n",
			'content' => "" . json_encode($ps) . ""
		)
	);
$contexts = stream_context_create($opt);
	$file4 = gzdecode(file_get_contents('http://grooveshark.com/more.php?getAutocomplete', false, $contexts));
	$file4 = json_decode($file4);
	
	$p["query"] = $_GET["query"];
		
	while($i < count($file4))
	{
	$i++;
	$p["suggestions"][$i-1] = $file4->result[$i-1]->SongName;
	}
	
	echo json_encode($p);
}

?>
