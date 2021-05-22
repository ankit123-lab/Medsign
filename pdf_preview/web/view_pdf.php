<?php
if($_REQUEST['file_url']) {
	$file = urldecode(base64_decode($_REQUEST['file_url']));
	$filename = 'filename.pdf';
	header('Content-type: application/pdf');
	header('Content-Disposition: inline; filename="' . $filename . '"');
	header('Content-Transfer-Encoding: binary');
	header('Accept-Ranges: bytes');
	@readfile($file);
}
?>