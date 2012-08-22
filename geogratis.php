<?php
$json = file_get_contents('http://geogratis.gc.ca/api/en/nrcan-rncan/ess-sst/-/(urn:iso:type)documentdigital-documentnumerique?max-results=100&entry-type=full&alt=json');
$data = json_decode($json);

//print_r($data); exit;
foreach ($data->products as $item) {
    $data = array(
        'title'=>(string) $item->title,
        'url'=>(string) $item->links[0]->href,
        'body'=>(string) $item->summary,
        'year'=>substr($item->citation->publicationDate, 0, 4),
        'id'=>(string) $item->id,
        'authors'=>(string) $item->author
    );
    //print_r($data); exit;
    $result = add('documents', $item->id, $data);
    print_r($result);

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
    
    return json_decode(file_get_contents('http://localhost:9200/geogratis/' . $type . '/' . $id, null, $context));
}
?>
