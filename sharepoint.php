<?php
// sharepoint is namespaced so must use children(). Could also use DOM methods.
$xml = simplexml_load_file('sharepoint.xml');
$rows = $xml->children('rs', true)->data->children('z', true)->row;

foreach ($rows as $item) {
    $attribs = $item->attributes();
    if ($attribs->ows_Sector == 'IETS') {
        $data = array();
        $data['title'] = (string) $attribs->ows_Title;
        $data['body'] = (string) $attribs->ows_Plain_x0020_Language_x0020_Summa;
        $data['date_added'] = (string) $attribs->ows_Created;
        $data['url'] = "https://dev1-sp-projects.nrcan.gc.ca/STP-PST/Lists/Publication/DispForm.aspx?ID=" . 
                        (string) $attribs->ows_ID;
        $data['authors'] = (string) $attribs->ows_Primary_x0020_Author_x0020_Conta . "; " . 
                           $attribs->ows_External_x0020_Authors;
        $year = date('Y', strtotime($attribs->ows_Published_x0020_Date_x0020_or_x0));
        $data['year'] = $year;
        $data['id'] = (int) $attribs->ows_Publication_x0020_ID;
        print_r($result = add('publications', $data['id'], $data));
    }
        
    
    
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
    
    return json_decode(file_get_contents('http://localhost:9200/iets/' . $type . '/' . $id, null, $context));
}
?>
