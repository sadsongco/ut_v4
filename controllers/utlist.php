<?php

$modules = ["emailList"];
if (ENV === 'dev') $env = 'dev';

echo $this->renderer->render("utlist", [
    'stylesheets'=>['utlist'],
    "modules"=>$modules,
    "env"=>$env,
    "v" => $v
]);