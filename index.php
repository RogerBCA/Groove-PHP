<?php
session_start();
header("Content-Type: text/html; charset=utf-8"); 
?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title>Groove-PHP</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
</head> 
<body>
<div data-role="page">
<?php
/*
Ported Python Version to PHP by Check
<i.like.webm@gmail.com>
Licenced under GPL v 2
Orginal:
A Grooveshark song downloader in python
by George Stephanos <gaf.stephanos@gmail.com>
groove-dl/github


PICTURE Beat Box Howard Dickins CC Licence :P find him at flickr 
PICTURE 404 Anna Hirsch also CC Licence :P
*/

// Tools which needed

$groovefix = json_decode(file_get_contents("GrooveFix.json"));

function url_exists($url) { 
    $hdrs = @get_headers($url); 
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false; 
} 


function gzdecode($data)
{
   return gzinflate(substr($data,10,-8));
} 

// need more Colors :P
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

function gen_uuid() {
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	// 32 bits for "time_low"
	mt_rand(0, 0xffff), mt_rand(0, 0xffff),

	// 16 bits for "time_mid"
	mt_rand(0, 0xffff),

	// 16 bits for "time_hi_and_version",
	// four most significant bits holds version number 4
	mt_rand(0, 0x0fff) | 0x4000,

	// 16 bits, 8 bits for "clk_seq_hi_res",
	// 8 bits for "clk_seq_low",
	// two most significant bits holds zero and one for variant DCE1.1
	mt_rand(0, 0x3fff) | 0x8000,

	// 48 bits for "node"
	mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

// Prep token
function prep_token($method, $token, $with) {
	$obj = new randomColorGenerator();
	$hex2 = $obj->getColor();
	return $hex2 . sha1($method . ":" . $token . $with . $hex2);
}


// Strings 
$Useragent = "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1";
$Referer = "http://grooveshark.com/JSQueue.swf?20110216.04";



// GEN TOKEN 


function gen_token()
{

if ($_SESSION["uuid"]==FALSE){$_SESSION["uuid"] = gen_uuid(); } 

if ($_SESSION["id"] == FALSE)
	{
$opts = array (
	'http' => array (
		'max_redirects' => 3,
		'ignore_errors' => 1
	)
);
stream_context_get_default($opts);
$header = get_headers("http://html5.grooveshark.com");
$id = substr($header[5], 22, 32);
$_SESSION["id"] = $id;
$secretKey = md5($id);
$_SESSION["secretKey"] = $secretKey;
	}

// Header !
$h["session"] = $_SESSION["id"];
$h["uuid"] = $_SESSION["uuid"];
$h["privacy"] = 0;
$h["Country"]["CC1"] = "0";
$h["Country"]["CC2"] = "0";
$h["Country"]["CC3"] = "0";
$h["Country"]["CC4"] = "0";
$h["Country"]["ID"] = "1";


if ($_SESSION["token"]==FALSE)
	{
global $groovefix;
// Get a Token	/* Started here */
$p["method"] = "getCommunicationToken";
$p["parameters"]["secretKey"] = $_SESSION["secretKey"];
$p["header"] = $h;
$p["header"]["client"] = "htmlshark";
$p["header"]["clientRevision"] = $groovefix->htmlshark->GrooveClientRevision;;

$opts = array (
	'http' => array (
		'method' => "POST",
		'header' => "User-Agent: $Useragent\r\n" .
		"Accept-Encoding: gzip\r\n".
		"Referer: $Referer\r\n" .
		"Cookie: PHPSESSID=".$_SESSION["id"]."\r\n" .
		"Content-Type: application/json\r\n",
		'content' => "" . json_encode($p) . ""
	)
);

$context = stream_context_create($opts);
$file = file_get_contents('http://grooveshark.com/more.php', false, $context);
$decode = json_decode(gzdecode($file));
// Got TOKEN
$token = $decode->result;
$_SESSION["token"] = $token;
	}
}
// END GEN Token 
gen_token();


$h["session"] = $_SESSION["id"];
$h["uuid"] = $_SESSION["uuid"];
$h["privacy"] = 0;
$h["Country"]["CC1"] = "0";
$h["Country"]["CC2"] = "0";
$h["Country"]["CC3"] = "0";
$h["Country"]["CC4"] = "0";
$h["Country"]["ID"] = "1";

function artistGetSongsEx($id)
	{
        global $h, $groovefix;
	$s["header"] = $h;
	$s["header"]["clientRevision"] = $groovefix->htmlshark->GrooveClientRevision;
	$s["header"]["token"] = prep_token("artistGetSongsEx", $_SESSION["token"], $groovefix->htmlshark->GrooveStaticRandomizer);
	$s["header"]["client"] = "htmlshark";
	$s["method"] = "artistGetSongsEx";
	$s["parameters"]["artistID"] = $id;
        $s["parameters"]["isVerifiedOrPopular"] = "isVerified";

	$option = array (
		'http' => array (
			'method' => "POST",
			'header' => "User-Agent: $Useragent\r\n" .
			"Referer: http://grooveshark.com/\r\n" .
			"Accept-Encoding: gzip\r\n" .
			"Cookie: PHPSESSID=".$_SESSION["id"]."\r\n" .
			"Content-Type: application/json\r\n",
			'content' => "" . json_encode($s) . ""
		)
	);

	$contexts = stream_context_create($option);
	$file2 = file_get_contents('http://grooveshark.com/more.php?artistGetSongsEx', false, $contexts);
	$result = json_decode(gzdecode($file2));
	if ($result->fault->code == "256") {$_SESSION["uuid"] = FALSE; $_SESSION["id"]=FALSE; $_SESSION["token"] = FALSE; gen_token(); artistGetSongsEx($id); }
	elseif ($result->result[0]->Name){
	$_SESSION["artist"] == FALSE;
	echo '<h4 style="position:relative; margin-bottom:3px;">More Songs from this Artist</h4>
	       <ul data-role="listview" data-inset="true" id="artist">';
	while ($i < 10)
	{
	$i++;
	if ($result->result[$i-1]->Name)
	{
	echo '	<li><a data-ajax="false" href="index.php?page=download&SongID='.$result->result[$i-1]->SongID.'&filename='.$result->result->result[$i-1]->ArtistName.' - '.$result->result->result[$i-1]->SongName.'.mp3">';
				if ($result->result[$i-1]->CoverArtFilename)
				{
				echo '<img src="http://images.grooveshark.com/static/albums/'.$result->result[$i-1]->CoverArtFilename.'" />'; }
				echo '
				<h3>'.$result->result[$i-1]->Name.'</h3>
				<p>'.$result->result[$i-1]->ArtistName.'</p>
				<p>'.$result->result[$i-1]->AlbumName.'</p>
			</a></li>';
	}
	}

	echo '</ul>';
        }}

function getSearchResultsEx($song,$type)
	{
	global $h, $groovefix;
	$s["header"] = $h;
	$s["header"]["clientRevision"] = $groovefix->htmlshark->GrooveClientRevision;
	$s["header"]["token"] = prep_token("getSearchResultsEx", $_SESSION["token"], $groovefix->htmlshark->GrooveStaticRandomizer);
	$s["header"]["client"] = "htmlshark";
	$s["method"] = "getSearchResultsEx";
	$s["parameters"]["type"] = $type;
	$s["parameters"]["query"] = $song;

	$option = array (
		'http' => array (
			'method' => "POST",
			'header' => "User-Agent: $Useragent\r\n" .
			"Referer: http://grooveshark.com/\r\n" .
			"Accept-Encoding: gzip\r\n" .
			"Cookie: PHPSESSID=".$_SESSION["id"]."\r\n" .
			"Content-Type: application/json\r\n",
			'content' => "" . json_encode($s) . ""
		)
	);

	$contexts = stream_context_create($option);
	$file2 = file_get_contents('http://grooveshark.com/more.php?getSearchResultsEx', false, $contexts);
	$result = json_decode(gzdecode($file2));
	if ($result->fault->code == "256") {$_SESSION["uuid"] = FALSE; $_SESSION["id"]=FALSE; $_SESSION["token"] = FALSE; gen_token(); getSearchResultsEx($song,$type); }
	else
	{
	echo '<ul data-role="listview">';
	$_SESSION["artist"] = true;
	while ($i < 20)
	{
	$i++;
	
	

	if (count($result->result->result[$i-1]->SongName) >0)
	{
	echo '	<li><a data-ajax="false" href="index.php?page=download&SongID='.$result->result->result[$i-1]->SongID.'&ArtistID='.$result->result->result[$i-1]->ArtistID.'&filename='.$result->result->result[$i-1]->ArtistName.' - '.$result->result->result[$i-1]->SongName.'.mp3">';
				if ($result->result->result[$i-1]->CoverArtFilename != "")
				{
				echo '<img src="http://images.grooveshark.com/static/albums/'.$result->result->result[$i-1]->CoverArtFilename.'" />'; }
				echo '
				<h3>'.$result->result->result[$i-1]->SongName.'</h3>
				<p>'.$result->result->result[$i-1]->ArtistName.'</p>
				<p>'.$result->result->result[$i-1]->AlbumName.'</p>
			</a></li>';
	}
	elseif (count($result->result->result->Songs[$i-1]->SongName) >1){
	echo '	<li><a data-ajax="false" href="index.php?page=download&SongID='.$result->result->result->Songs[$i-1]->SongID.'&ArtistID='.$result->result->result->Songs[$i-1]->ArtistID.'&filename='.$result->result->result->Songs[$i-1]->ArtistName.' - '.$result->result->result->Songs[$i-1]->SongName.'.mp3">';
				if ($result->result->result->Songs[$i-1]->CoverArtFilename != "")
				{
				echo '<img src="http://images.grooveshark.com/static/albums/'.$result->result->result->Songs[$i-1]->CoverArtFilename.'" />'; }
				echo '
				<h3>'.$result->result->result->Songs[$i-1]->SongName.'</h3>
				<p>'.$result->result->result->Songs[$i-1]->ArtistName.'</p>
				<p>'.$result->result->result->Songs[$i-1]->AlbumName.'</p>
			</a></li>';
	}


	}

	echo '</ul>';
        }
	}






if ($_GET["page"] == "home" OR $_GET["page"] == "")
{
echo '<script type="text/javascript" src="jquery-autocomplete/jquery.autocomplete-min.js"></script>';
echo "
<link rel='stylesheet' href='jquery-autocomplete/styles.css'>
<script type='text/javascript'>
jQuery(function(){
  var a = $('#search').autocomplete({ 
    serviceUrl:'computing/autocomplete.php',
    minChars:3, 
    delimiter: /(,|;)\s*/, 
    maxHeight:400,
    width:300,
    zIndex: 9999,
    deferRequestBy: 0, //miliseconds
    noCache: false,
    onSelect: function(value){ self.location.href='index.php?page=search&query='+ value; },

  }); });
function autocomplete_off(){
$('#search').html('');
}
</script>";
echo '	<script type="text/javascript">
	</script>
	<div data-role="header">
		<h1>GroovePHP - Search</h1>
	</div>
	<div data-role="content">

        <center><div style="margin:40px 30%; width:300px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; height: 170px; background: url(images/groove.jpg) no-repeat; "></div></center>

	<div data-role="fieldcontain">
	<form type="GET">
	<input type="hidden" name="page" value="search" />
        <center><input type="search" name="query" id="search" onclick="autocomplete_off()" value="" /></center>
	</form>
        </div>
		
	</div>';
}
elseif($_GET["page"] == "search" AND !empty($_GET["query"]) )
{
if (empty($type)){$type="Songs";}

echo '<div data-role="header">
      <a href="index.php" data-icon="back">Home</a>
		<h1>GroovePHP - '.htmlentities($type." (".$_GET['query'].")").'</h1>
	</div>';
echo '<div data-role="content">';
getSearchResultsEx($_GET["query"],"Songs");
echo '</div>';
}
elseif($_GET["page"] == "download")
{
echo '
<script>
function set_time(){
document.getElementById("audio2").currentTime = 70;
}
function play_ended(){
$(\'#player\').html("<audio id=\'audio2\' onplay=\'set_time();\' autoplay=\'autoplay\'style=\'padding-top:2%;\' controls=\'controls\' src=\'music/'.str_replace(' ','%20',$_GET["filename"]).'\'></audio> <br /> <a data-ajax=\'false\'href=\'music/'.str_replace(' ','%20',$_GET["filename"]).'\'><img src=\'images/download_small.gif\'>Download</a>");
}
</script>';
echo '<div data-role="header">
      <a href="index.php" data-icon="back">Home</a>
		<h1>GroovePHP - Download</h1>
	</div>';

if ( strlen($_GET["SongID"]) >= 5 AND is_numeric($_GET["SongID"]) AND strlen($_GET["filename"]) >= '5')
{
echo '<div data-role="content">';

echo "<center> <div id='player'></div> </center>";
if ($_SESSION["artist"] AND is_numeric($_GET["ArtistID"]) )
{
artistGetSongsEx($_GET["ArtistID"]);
}

$_SESSION["filename"] = $_GET["filename"];
$_SESSION["SongID"] = $_GET["SongID"];
echo "<script type='text/javascript' src='computing/player.php'></script>";
echo "<script type='text/javascript' src='computing/download.php?type=js'></script>";
echo "</div>";
$_SESSION["uuid"] = FALSE; $_SESSION["id"]=FALSE; $_SESSION["token"] = FALSE;
gen_token();
}
}
else 
{
echo '<div data-role="header">
      <a href="index.php" data-icon="back">Home</a>
		<h1>GroovePHP - 404</h1>
	</div>
      <div data-role="content">
      <center><div style="margin:40px 30%; width:640px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; height: 480px; background: url(images/404.jpg) no-repeat;"></div></center>
      </div>
';
}

?>
</div>
</body>
</html>

