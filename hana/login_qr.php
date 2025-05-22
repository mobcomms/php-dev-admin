<?php
if(!empty($_GET['images'])){
	header("Content-Type: image/jpeg");
	$fp = file_get_contents($_GET['images']);
	print_r($fp);
}
?>