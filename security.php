<?php

if(!function_exists("clean")){


    function clean($data){

        return htmlspecialchars(
            $data,
            ENT_QUOTES,
            'UTF-8'
        );

    }


}

?>