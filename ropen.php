<?php

session_name('ropen');
session_start();

if (!is_array($_SESSION['queue']))
   $_SESSION['queue'] = array();
   
switch ($_GET['mode']) {
    case 'put':
        array_push($_SESSION['queue'], array(
            'connect' => $_GET['connect'],
            'app'     => $_GET['app'],
            'paths'   => $_GET['paths']
        ));
        break;
    case 'get':
        $message = array_pop($_SESSION['queue']);
        if ($message)
            echo $message['connect'] . ' ' . $message['app'] . ' ' . $message['paths'];
        break;
    case 'purge':
        unset($_SESSION['queue']);
        break;
    default:
        echo 'unknown command';
        break;
}

?>