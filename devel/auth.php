<?php

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Текст, отправляемый в том случае,
    если пользователь нажал кнопку Cancel';
    exit;
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'demo' && $_SERVER['PHP_AUTH_PW'] == 'demo' ) { 
    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
    }else{
        header('WWW-Authenticate: Basic realm="realm"'); 
      header('HTTP/1.0 401 Unauthorized'); 
      exit; 
    }
}
?>