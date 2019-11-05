<?php

header("Content-type: application/x-javascript");

$file = "include/config.inc.php";
include $file;

$timthumbUrl = "http://beamiller.com.br/wp-content/themes/tri/timthumb.php";
$galleryUrl = "http://beamiller.com.br/galeria";

if (!$link = mysqli_connect($CONFIG['dbserver'], $CONFIG['dbuser'], $CONFIG['dbpass'])) {
    echo 'Não foi possível conectar ao mysql';
    exit;
}

if (!mysqli_select_db($link, $CONFIG['dbname'])) {
    echo 'Não foi possível selecionar o banco de dados';
    exit;
}

$prefix1 = $CONFIG['TABLE_PREFIX']."pictures";
$prefix2 = $CONFIG['TABLE_PREFIX']."albums";

$n = $_GET["n"];
$w = $_GET["w"];
$h = $_GET["h"];
if ($n == ''): $n = 4; endif;

$sql = "SELECT aid, pid, filename, filepath from $prefix1
		where (aid, pid) in (
		select distinct aid, max(pid) from $prefix1 group by aid)
		ORDER BY pid DESC
		LIMIT $n";   

$result = mysqli_query($link, $sql);

if (!$result) {
    echo "Erro do banco de dados, não foi possível consultar o banco de dados\n";
    echo 'Erro MySQL: ' . mysql_error();
    exit;
}

echo 'document.write(\'';
while ($row = mysqli_fetch_assoc($result)) {
	echo $row["pid]"];

	$main = $row["aid"];
	$query = "SELECT * FROM $prefix1 WHERE aid=$main ORDER BY pid DESC LIMIT 1";
	$query = mysqli_query($link, $query);
	$dados = mysqli_fetch_array($query);

	$query2 = "SELECT * FROM $prefix2 WHERE aid=$main";
	$query2 = mysqli_query($link, $query2);
	$dados2 = mysqli_fetch_array($query2);
	
	$timthumb = $timthumbUrl."?src=";
	$uri = $galleryUrl. "/albums/" .$dados["filepath"]."thumb_".$dados["filename"];
	$uri2 = $galleryUrl. "/albums/" .$dados["filepath"]."normal_".$dados["filename"];

	if (($w == '') and ($h == '')): $image_uri = $uri;
	else: $image_uri = $timthumb.$uri2."&w=".$w."&h=".$h;
	endif;
	   

    $text = <<<EOT
<div class="album"><div class="album_image"><a href="${galleryUrl}/thumbnails.php?album={$row['aid']}" target="_blank"><img src="{$image_uri}"></a></div><h1 class="album_title">{$dados2["title"]}</h1></div>
EOT;
echo $text;
}
echo '\');';
mysqli_free_result($result);
?>
