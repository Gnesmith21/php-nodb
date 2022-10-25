<?php

require(__DIR__ . "/../autoload.php"); # add the lib Auto_Loader
printf("\r\nThis is testing the db functions \r\n");

$nodb = new phpnodb("test.nodb", false);

$nodb->enableWrites()->set("Test", "This is a good string");
$nodb->enableWrites()->set("Test1", "This is a good string1"); // no output
$nodb->enableWrites()->set("Test2", "This is a good string2"); // no output
$nodb->enableWrites()->set("Test3", "This is a good string3"); // no output
$nodb->enableWrites()->set("Test4", "This is a good string4"); // no output
var_dump($nodb->enableWrites()->set("Test15", "5This is a good string5")); // showing bool

$nodb->enableWrites()->set("Test6", "This is a good string6"); // this will die

$nodb->enableWrites()->delete("Test6"); # test delete

$nodb->commit(); #save the settings

echo $nodb->enableReads()->get("Test"); # get the setting for test
echo "\r\n" . $nodb->enableReads()->get("Test1"); # get the setting for test
echo "\r\n" . $nodb->enableReads()->get("Test6"); # get the setting for test

#test error handle.
try {
    $nodb->enableReads()->set("Test6", "");
} catch (dbexception $e) {
    echo sprintf("\r\n%s", $e->getMessage());
}
echo "\r\n All done with the demo"; # close

