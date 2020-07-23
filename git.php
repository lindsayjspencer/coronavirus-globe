<?PHP

//Something to write to txt log
$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
        "Payload: ".$_POST["PAYLOAD"].PHP_EOL.
        "-------------------------".PHP_EOL;
//Save string to log, use FILE_APPEND to append.
file_put_contents('./logs/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);

?>
