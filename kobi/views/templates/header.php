<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title; ?></title>
	<?php
	
		// Include files in head based on it's extension
		echo $this->html_utility->str_js_css($included_files);
	?>
</head>
<body>
