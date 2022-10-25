<?php
/**
 * Copyright 2022 Gavin Nesmith

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
 */

/**
 * Autoloader for php-nodb this file is required.
 * @author Gavin R. Nesmith
 * @since v1.2
 *
 */
spl_autoload_register(function ($class_name) {
    $current_inc = __dir__ . "\\phpnodb\\"; #change this if you move the files around.

        require ($current_inc . $class_name . ".class.php");

});