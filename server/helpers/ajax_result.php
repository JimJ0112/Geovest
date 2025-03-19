<?php

function ajax_result($success, $message, $data = null)
{


    $result = array(
        "success" => $success,
        "message" => $message,
        "data" => $data
    );



    echo json_encode($result);
}
