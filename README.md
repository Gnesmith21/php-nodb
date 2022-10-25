# PHP-NODB
A json style configuration no-db for use with PHP applications.

Some things to note about this application. 
- It works like an array with key->value pairs 
- It currently does not support encryption
- Currently under active development 
- It is not fancy at all and was designed to solve a specific problem
### Future todo list 
- encryption 
- checksum validation 
- memcache support
## To contribute 
- Submit a pull request or bugfix report.
## How to use 
- Include the auto-loader 
`require(__dir__ . "\\php-nodb\\autoload.php`
- Create an instance of the class `$nodb = new phpnodb("test.nodb", false);`
- To perform writes `$nodb->enableWrites()->set("Test", "This is a good string");`
- To perform reads `echo $nodb->enableReads()->get("Test");`
- Don\`t forget to commit `$nodb->commit();`
## Look in the examples folder for more information

## 

