<style type="text/css">

    .resource {
        border-radius: 4px;
        border: 1px dashed #ccc;
        padding: 12px;
    }

    .resource h1,
    .resource h2 {
        margin: 0;
    }

    .resource p {
        margin: 0;
    }

    .resource > .resources {
        padding: 20px;
    }

    .resource > .resources > .resource {
        margin-bottom: 24px;
    }

    .resource > .resources > .resource > h1 {
        font-size: 1.5em;
    }


</style>


<h1>Documentaci&oacute;n</h1>

<?php
foreach ($data as $module) {
    printResource($module);
}




function printResource($resource)
{
?>

    <div class="resource">
        <h1><?= isset($resource->title) ? $resource->title : 'Sin titulo' ?></h1>
        <p><?= isset($resource->description) ? $resource->description : '' ?></p>

        <?php 
        if (isset($resource->methods)) {
            echo '<div class="methods">';
            foreach ($resource->methods as $methodName => $method) {
                printMethod($methodName, $method, $resource);
            }
            echo '</div>';
        } 
        ?>

        <?php 
        if (isset($resource->resources)) {
            echo '<div class="resources">';
            foreach ($resource->resources as $subResource) {
                printResource($subResource);
            }
            echo '</div>';
        } 
        ?>

    </div>

<?php
}

function printMethod($methodName, $method, $resource)
{
?>

    <div class="method <?=$methodName?>">
        <h2><?= $methodName ?> <?= $resource->path ?></h2>
    </div>

<?php
    dump($method);

}

?>