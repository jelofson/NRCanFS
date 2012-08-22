<?php
$rss = simplexml_load_file('http://geoscan.ess.nrcan.gc.ca/rss/newpub_e.rss');

foreach ($rss->channel->item as $item) {
    $data = array(
        'title'=>(string) $item->title,
        'url'=>(string) $item->link,
        'body'=>(string) $item->description,
        'date_added'=>date('Y-m-d', strtotime((string) $item->pubDate)),
        'id'=>(string) $item->guid
    );
    //print_r($data);
    //exit;
    print_r($result = add('publications', $item->guid, $data));
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
    
    return json_decode(file_get_contents('http://localhost:9200/ess/' . $type . '/' . $id, null, $context));
}
?>
