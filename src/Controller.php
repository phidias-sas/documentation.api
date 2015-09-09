<?php
namespace Phidias\Documentation;

use Phidias\Json\Document;
use Phidias\Json\Schema;
use Phidias\Api\Server\Module;

class Controller
{
    public function main()
    {
        /* Find all loaded modules */
        $documentedModules = [];

        foreach ( Module::getLoadedModules() as $module ) {
            if (is_file($module."/specification/index.json")) {
                $documentedModules[] = Document::parse($module."/specification/index.json");
            }
        }

        return $documentedModules;
    }
}