<?php
$xml = simplexml_load_file('STPWG_test_GEOSCAN_2012.xml');
//print_r($xml->GetRecords->Response->Record); exit;
foreach ($xml->GetRecords->Response->Record as $item) {
    $data = array();
    foreach ($item as $field) {
        foreach($field->attributes() as $a=>$b) {
            
            if ($a == 'OutputFieldName') {
                switch($b) {
                    case "XAB" : 
                        $data['body'] = (string) $field;
                    break;
                    case "XID" :
                        $data['id'] = (string) $field;
                    break;
                    case "XCITE" : 
                        $data['title'] = (string) $field;
                    break;
                    case "XPD" :
                        $data['date_added'] = (string) str_replace(" ", "-", $field);
                    break;
                    case "XWWGE" : 
                        $data['url'] = (string) $field;
                    break;
                    case "XOR" : 
                        $data['authors'] = (string) $field;
                    break;
                    case "XYR" : 
                        $data['year'] = (string) $field;
                    break;
                }
            }
            
        }
    }
    
    /*
    $data = array(
        'title'=>(string) $item->title,
        'url'=>(string) $item->link,
        'body'=>(string) $item->description,
        'date_added'=>date('Y-m-d', strtotime((string) $item->pubDate)),
        'id'=>(string) $item->guid
    );
    */
    //print_r($data);
    //exit;
    print_r($result = add('publications', $data['id'], $data));
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
