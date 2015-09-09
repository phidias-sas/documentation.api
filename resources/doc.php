<?php 
$templateFolder = realpath(__DIR__."/../templates");

return [

    "/doc" => [
        "get" => [
            "controller" => "Phidias\Documentation\Controller->main()",
            "template" => [
                "text/html" => "$templateFolder/html/index.php"
            ]
        ]
    ]

];