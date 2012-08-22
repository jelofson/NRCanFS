<?php
error_reporting(-1);
$config = include 'config.php';
$dsn = 'mysql:dbname=AIMFC;host=132.156.208.89';
$user = $config['idcf_user'];
$password = $config['idcf_pass'];


try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
$dbh->exec('set names utf8');
$sth = $dbh->prepare("select * from vListInsects limit 10");
$sth->execute();

$result = $sth->fetchAll();

foreach ($result as $info) {
    $data = array(
        'id'=>$info['geID'],
        'title'=>$info['geNameAN'] . " [" . $info['geNameFR'] . "]" . " (" . $info['genName'] . " " . $info['espName'] . ")",
        'body'=>$info['geDegatSympAN'] . ' ' . $info['geDegatSympFR'],
        'author'=>$info['geAuteur'],
        'year'=>$pub['publication_year'],
        'url'=>'http://tidcf.nrcan.gc.ca/insects/factsheet/' . $info['geID']
        
    );
    print_r($data);
    exit;
    //$result = add('publications', $pub['id'], $data);
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
