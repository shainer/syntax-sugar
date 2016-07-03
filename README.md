*****************************************************
* SYNTAX SUGAR: Syntax highlighter written in PHP   *
* Current version: 1.0				    *
*****************************************************

1. Installation
----------------
You just need to copy everything into a directory in your webserver.

2. Usage
----------------
Assume you have a PHP file where you have all the code in the variable $code
and you know the language it's written in (in the variable $lang).
Then, all you need to do is:

 > include("/path/to/syntaxsugar.php");
 >
 > $obj = new SyntaxSugar($code, $lang);
 > $obj->Show();

This will just work, provided that you have referenced the "syntaxsugar.css"
cascading style sheet in the <head> section of your page.

At the moment the supported languages are C, C++, Java and HTML, but we're
planning new extensions for other languages too.
If you try to request a language that isn't supported, an exception is thrown.
In the "SyntaxTest" subdirectory you can find an example of usage in C and Python.

3. Credits
----------------
Authors: shainer and jmc
Homepage: http://giudoku.sourceforge.net

Email any feedback, bugs or wishes at:
	<syn.shainer@gmail.com>

