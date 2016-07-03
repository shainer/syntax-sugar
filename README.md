*****************************************************
* SYNTAX SUGAR: Syntax highlighter written in PHP   *
* Current version: 1.0				    *
*****************************************************

1. Installation
----------------
Copy everything into a directory in your webserver.

2. Usage
----------------
First, make sure to reference the "syntaxsugar.css" style sheet in the
<head> section of your page.

Assuming $code contains the whole code snippet, and $lang the language
it is written in, this is how you display the syntax highlighted code:

 > include("/path/to/syntaxsugar.php");
 >
 > $obj = new SyntaxSugar($code, $lang);
 >
 > $obj->Show();

At the moment the supported languages are C, C++, Java and HTML, but we're
planning new extensions for other languages too.
If you try to request a language that isn't supported, an exception is thrown.
In the "SyntaxTest" subdirectory you can find an example of usage in all
supported languages.

3. Credits
----------------
Authors: shainer <syn.shainer@gmail.com> and jmc
Homepage: http://giudoku.sourceforge.net

