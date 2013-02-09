<?php
session_start();
if ( count($_SESSION) >= 1 AND $_GET["type"] == "js")
{

echo "
$(function(){
	$(window).bind(\"load\", function(){
	$.ajax({
  	url: 'computing/download.php?type=download'
	}).success(function() { $('#player').html(\"<audio autoplay=\'autoplay\'style=\'padding-top:2%;\' controls=\'controls\' src=\'music/".str_replace(' ','%20',$_SESSION["filename"])."\'></audio> <br /> <a data-ajax=\'false\'href=\'music/".str_replace(' ','%20',$_SESSION["filename"])."\'><img src=\'images/download_small.gif\'>Download</a>\");});
	});
}) 

";


}
if ($_GET["type"] == "download" AND count($_SESSION) >= 1 AND $_SESSION["filename"] != FALSE)
{

if (!file_exists('../music/' . $_SESSION["filename"]))
{

if (is_dir("../music") == false) {
		mkdir("../music", 0777);
	}

$groovefix = json_decode(file_get_contents("../GrooveFix.json"));

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

$h["session"] = $_SESSION["id"];
$h["uuid"] = $_SESSION["uuid"];
$h["privacy"] = 0;
$h["Country"]["CC1"] = "0";
$h["Country"]["CC2"] = "0";
$h["Country"]["CC3"] = "0";
$h["Country"]["CC4"] = "0";
$h["Country"]["ID"] = "1";
$ps["parameters"]["mobile"] = "false";
$ps["parameters"]["prefetch"] = "false";
$ps["parameters"]["songID"] = $_SESSION['SongID'];
$ps["parameters"]["country"] = $h["Country"];
$ps["header"] = $h;
$ps["header"]["client"] = "jsqueue";
$ps["header"]["clientRevision"] = $groovefix->jsqueue->GrooveClientRevision;
$ps["header"]["token"] = prep_token("getStreamKeyFromSongIDEx", $_SESSION["token"], $groovefix->jsqueue->GrooveStaticRandomizer);
$ps["method"] = "getStreamKeyFromSongIDEx";

$optst = array (
		'http' => array (
			'method' => "POST",
			'header' => "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20120101 Firefox/17.0.1\r\n" .
			"Referer: http://grooveshark.com/JSQueue.swf?20110216.04\r\n" .
			"Cookie: PHPSESSID=".$_SESSION["id"]."\r\n" .
			"Content-Type: application/json\r\n",
			'content' => "" . json_encode($ps) . ""
		)
	);
$contextss = stream_context_create($optst);
	$file4 = file_get_contents('http://grooveshark.com/more.php?getStreamKeyFromSongIDEx', false, $contextss);

$keys = json_decode($file4);

	$streamKey = $keys->result->streamKey;
	$ip = $keys->result->ip;

	$ch = curl_init();
	$fp = fopen('../music/' . $_SESSION["filename"], "w");
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_URL, 'http://' . $ip . '/stream.php');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "streamKey=$streamKey");
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);

}
   
echo "success";

}


?>
