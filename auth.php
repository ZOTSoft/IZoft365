<?

if (isset($_POST['redirect'])){
    header("Location: /".$_POST['redirect']);
}

?>