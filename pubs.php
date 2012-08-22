<?php
error_reporting(-1);
$config = include 'config.php';
$dsn = 'mysql:dbname=bookstore;host=cfs.nrcan.gc.ca';
$user = $config['dbuser'];
$password = $config['dbpass'];


try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
$dbh->exec('set names utf8');
$sth = $dbh->prepare("select * from v_catalog");
$sth->execute();

$result = $sth->fetchAll();

foreach ($result as $pub) {
    $data = array(
        'id'=>$pub['id'],
        'title'=>$pub['title'],
        'centre'=>$pub['centre_centre_id'],
        'keywords'=>$pub['keywords'],
        'body'=>$pub['abstract_en'] . ' ' . $pub['abstract_fr'],
        'authors'=>$pub['authors'],
        'year'=>$pub['publication_year'],
        'date_added'=>substr($pub['date_added'], 0, 10),
        'url'=>'http://cfs.nrcan.gc.ca/publications?id=' . $pub['id']
        
    );
    //print_r($data); exit;
    $result = add('publications', $pub['id'], $data);
}

function add($type, $id, $data) {

    $opts = array(
        'http'=>array(
            'method'=>"PUT",
            'header'=>'Content-type: application/json',
            'content'=>json_encode($data)
        )
    );
    
    $context = stream_context_create($opts);
    
    return json_decode(file_get_contents('http://localhost:9200/cfs/' . $type . '/' . $id, null, $context));
}
?>
