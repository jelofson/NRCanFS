<?php
error_reporting(-1);
$config = include 'config.php';
$dsn = 'mysql:dbname=cfs2010;host=cfs.nrcan.gc.ca';
$user = $config['dbuser'];
$password = $config['dbpass'];

//$data = json_encode(array('test'=>"Biodiversité La diversité"));

//print_r($data);
//$new = json_decode($data);
//echo $new->test;
//exit;
try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
$dbh->exec('set names utf8');
$sth = $dbh->prepare("select * from search_index where type in ('page', 'project', 'employee')");
$sth->execute();

$result = $sth->fetchAll();

foreach ($result as $info) {
    $data = array(
        'id'=>$info['id'],
        'title'=>$info['title'],
        'body'=>$info['content'],
        'lang'=>$info['lang'],
        'url'=>$info['url'],
        'timestamp'=>$info['timestamp']
        
    );
    //print_r($data);
    //print_r(json_encode($data));
    
    
    $result = add($info['type'], $info['id'], $data);
    //print_r($result);
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
