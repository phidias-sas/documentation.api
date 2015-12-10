<?php 
$templateFolder = realpath(__DIR__."/../templates");

return [

    "/documentation" => [
        "get" => [
            "controller" => "Phidias\Documentation\Controller->main()",
            "template" => [
               "text/html" => "$templateFolder/html/index.php"
            ]
        ]
    ]  

];