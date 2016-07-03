<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Syntax Sugar test page</title>
		<link rel="stylesheet" type="text/css" href="../syntaxsugar.css"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	</head>

	<body>
		<!--
			This is the Syntax Sugar test page, an example just to
			show you how it works.
		-->

	<?php
		include("../syntaxsugar.php");

		$code = file_get_contents("python-example.py", True);
		$p = new SyntaxSugar($code, "Python");
		$p->Show();
	?>

		<hr style="margin-top: 5px; margin-bottom: 5px;"/>

	<?php
		$code = file_get_contents("c-example.c");

		$p = new SyntaxSugar($code, "C");
		$p->Show();
	?>
	
		<hr style="margin-top: 5px; margin-bottom: 5px;" />
	
	<?php
		$code = file_get_contents("html-example.html", False);
		$p = new SyntaxSugar($code, "HTML");
		$p->Show();
	?>
	
		<hr style="margin-top: 5px; margin-bottom: 5px;" />

	</body>
</html>
