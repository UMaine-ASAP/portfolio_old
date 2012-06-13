<?PHP
$filename = time(). "." .end(explode( '.' , $_POST['file_name']));

rename($_POST['file_path'], "/var/www/html/dev_portfolio/media/" . $filename );


$_POST['file_name'] = $filename;

session_start();
$_SESSION['uploadForm'] = $_POST;

header('Location: '. $_POST['upload_origin'] );

?>
