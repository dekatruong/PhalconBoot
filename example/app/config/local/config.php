<?php

return [
    'application'   => [
        'controllersDir'=> ROOT_PATH.'/app/controllers/',
        'modelsDir'     => ROOT_PATH.'/app/models/',
        'viewsDir'      => ROOT_PATH.'/app/views/',
        
        'sourceDir'     => ROOT_PATH.'/app/source/',
        
        'baseUri' => '/' . basename(ROOT_PATH) . '/', //for lab and local env
    ],
];
