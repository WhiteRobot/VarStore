<html>
<head>
<title>VarStore test page</title>
</head>
<body>
<ol>
<?php

error_reporting(E_ALL);
ini_set('display_errors',1);


include_once("../../VarStore.php");



$storage = new VarStore("./data/");


//drop the variable in case it exists. The example is really to show how VarStore behaves when the variable hasn't been set yet.
$storage->dropVar("test Variable");


//try to get a value that hasn't been set yet (returns the default).
$testVar = $storage->getVar("test Variable", "not yet set.");



//output #1
print "<li>testVar is: ".$testVar."</li>";




//set a value
$storage->setVar("test Variable", "All set!");


//try to get the value that we set previous (returns the value unless something goes wrong. If something goes wrong returns the default).
$testVar = $storage->getVar("test Variable", "not yet set.");


//output #2
print "<li>testVar is: ".$testVar."</li>";


?>
</ol>
</body>
</html>
