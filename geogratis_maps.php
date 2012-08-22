<?php

//$url = 'http://geogratis.gc.ca/api/en/nrcan-rncan/ess-sst/-/%28urn:iso:type%29mapdigital-cartenumerique?max-results=100&entry-type=full&alt=json';
//$url = 'http://geogratis.gc.ca/api/en/nrcan-rncan/ess-sst/-/%28urn:iso:type%29mapdigital-cartenumerique?start-index=352&max-results=100&entry-type=full&alt=json';
$url = 'http://geogratis.gc.ca/api/en/nrcan-rncan/ess-sst/-/%28urn:iso:type%29mapdigital-cartenumerique?start-index=736&max-results=100&entry-type=full&alt=json';

$json = file_get_contents($url);

$data = json_decode($json);


foreach ($data->products as $item) {
    if ($item->citation->alternateTitle) {
        $item->title .= ' (' . $item->citation->alternateTitle . ')';
    }
    $data = array(
        'title'=>(string) $item->title,
        'url'=>(string) $item->links[0]->href,
        'body'=>(string) $item->summary,
        'year'=>substr($item->citation->publicationDate, 0, 4),
        'id'=>(string) $item->id,
        'authors'=>(string) $item->author
    );
    //print_r($data); exit;
    $result = add('maps', $item->id, $data);
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
