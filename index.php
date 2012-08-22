<?php
error_reporting(-1);

$valid_idx = array(
    'cfs-publications'=>'CFS Publications',
    'geogratis-documents'=>'GEOGRATIS Documents',
    'geogratis-maps'=>'GEOGRATIS Maps',
    'ess-publications'=>'ESS 2012 Publications',
    //'cfs-page'=>'CFS Web Pages',
    //'cfs-project'=>'CFS Projects',
    //'cfs-employee'=>'CFS Employees'
);

$valid_sorts = array(
    'score'=>'_score',
    'title'=>'title.sortable',
    'year'=>'year',
    'authors'=>'authors.sortable'
);


$idx = isset($_GET['index']) ? (array) $_GET['index'] : array();

foreach ($idx as $key=>$index) {
    if (! array_key_exists($index, $valid_idx)) {
        unset($idx[$key]);
    }
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'score';
if (! array_key_exists($sort, $valid_sorts)) {
    $sort = 'score';
}

$sort_field = $valid_sorts[$sort];

$dir = isset($_GET['dir']) ? $_GET['dir'] : '';

if (empty($dir) && $sort == 'score') {
    $dir = 'desc';
}

if (! in_array($dir, array('asc', 'desc'))) {
    $dir = 'asc';
}

$date_from = isset($_GET['date-from']) ? (int) $_GET['date-from'] : ''; 
$date_to = isset($_GET['date-to']) ? (int) $_GET['date-to'] : ''; 

if (! $date_from) {
    $date_from = null;
}

if (! $date_to) {
    $date_to = null;
}

$query  = isset($_GET['q']) ? htmlentities($_GET['q'], ENT_QUOTES, 'UTF-8') : '';

$results = false;

if (! empty($query) && ! empty($idx)) {
    //$es->setType($idx);
    
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if (! $page) {
        $page = 1;
    }
    $perpage = 25;
    $from  = ($page - 1) * $perpage;
    /*
    $results = $es->search(array(
        'query'=>array(
            'query_string'=>array(
                'fields'=>array('_all'),
                'query'=>urlencode($query)
            )
        ),
        'from'=>$from,
        'highlight'=>array('fields'=>array('title'=>array(), 'body'=>array()))
    ));
    
    */
    
    $data = array(
        'query'=>array(
            'filtered'=>array(
                'query'=>array(
                    'query_string'=>array(
                        'fields'=>array('_all'),
                        'query'=>$query,
                        'default_operator'=>'AND'
                    )
                )
            )
        ),
        'size'=>25,
        'from'=>$from,
        'highlight'=>array(
            'fields'=>array(
                'title'=>array('number_of_fragments'=>0),
                'body'=>array('number_of_fragments'=>2, 'fragment_size'=>250)
            ),
            'tags_schema'=>'styled'
        ),
        'sort'=>array($sort_field=>$dir)
        
    );
    
    if ($date_from || $date_to) {
        $data['query']['filtered']['filter'] = array(
            'range'=>array(
                'year'=>array(
                    'from'=>$date_from,
                    'to'=>$date_to
                )
            )
        );
            
    }

    
    $opts = array(
        'http'=>array(
            'method'=>"GET",
            'header'=>'Content-type: application/json',
            'content'=>json_encode($data)
        )
    );
    
    $indexes = array();
    $types = array();
    foreach ($idx as $index) {
        $info = explode("-", $index);
        $indexes[] = $info[0];
        $types[] = $info[1];
    }
    
    $indexes = implode(',', $indexes);
    $types = implode(',', $types);
    
    $context = stream_context_create($opts);
    
    $results = json_decode(file_get_contents('http://localhost:9200/' . $indexes . '/' . $types . '/_search', null, $context));
    //print_r($results); exit;
    $hits = $results->hits->total;
    
    $pages = ceil($hits/$perpage);

}

function paginate($page, $pages, $query, $idx, $sort='score', $dir='asc') {
    if ($pages <= 1) {
        return;
    }
    $list = getPageList($page, $pages);
    $html = '<div class="pagination"><ul>' . "\n";
    foreach ($list as $page_num) {
        $query_parts = array(
            'q'=>$query,
            'index'=>$idx,
            'page'=>$page_num,
            'sort'=>$sort,
            'dir'=>$dir
            
        );
        
        $query_string = http_build_query($query_parts);
        
        if ($page_num == $page) {
            $html .= '<li class="active"><a href="#null">' . $page_num . "</a></li>\n";
        }
        elseif ($page_num == '...') {
            $html .= '<li class="disabled"><a href="#null">' . $page_num . "</a></li>\n";
        } else {
            
            $html .= '<li><a href="?' . $query_string . '">' . $page_num . "</a></li>\n";
        }
        
    }
    $html .= "</ul></div>\n";
    return $html;
}
// From Solar's Pager helper (modified the end list (-7))
function getPageList($page, $pages) {
    // keep a list of 11 items
    $list = array();
    
    // how to show them?
    if ($pages <= 11) {
        // 11 or fewer items
        $list = range(1, $pages);
    } elseif ($page < 8) {
        // early in the list
        $list = array(
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            '...',
            $pages - 1,
            $pages,
        );
    } elseif ($page > $pages - 7) {
        // late in the list
        $list = array(
            1,
            2,
            '...',
            $pages - 7,
            $pages - 6,
            $pages - 5,
            $pages - 4,
            $pages - 3,
            $pages - 2,
            $pages - 1,
            $pages,
        );
    } else {
        // mid-list
        $list = array(
            1,
            2,
            '...',
            $page - 2,
            $page - 1,
            $page,
            $page + 1,
            $page + 2,
            '...',
            $pages - 1,
            $pages,
        );
    }
    
    // done!
    return $list;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Federated Search via ElasticSearch</title>
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    
    <style>
    body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
    
    em.hlt1, em.hlt2, em.hlt3, em.hlt4, em.hlt5, em.hlt6, em.hlt7, em.hlt8, em.hlt9, em.hlt10 {
        background-color: #ffc;
        padding: 0 2px;
    }
    
    </style>
</head>

<body>

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="#">NRCan Federated Search</a>
                
            </div>
        </div>
    </div>
    <div class="container">
        <h2>Federated Search via ElasticSearch</h2>
        
        <form id="search" method="get">
        
        <div class="row">
            <div class="span4">
                <label for="q">Search for:</label>
                <input type="text" name="q" id="q" value="<?php echo $query; ?>" placeholder="Search for something..." />
                
                <div>
                    <label for="date-from">Year from:</label>
                    <input type="text" id="date-from" name="date-from" placeholder="eg. 1995" value="<?php echo $date_from; ?>" />
                </div>
                <div>
                    <label for="date-to">Year to:</label>
                    <input type="text" id="date-to" name="date-to" placeholder="eg. 2012" value="<?php echo $date_to; ?>" />
                </div>
                <div><input class="btn btn-large btn-primary" type="submit" value="Search" /></div>
            </div>
            <div class="span4">
                <fieldset>
                    <legend>Data source</legend>
                    <div>
                        <label class="checkbox" for="idx-cfs-publications">
                            <input type="checkbox" name="index[]" value="cfs-publications" id="idx-cfs-publications" <?php echo in_array('cfs-publications', $idx) ? 'checked="checked"' : ''; ?>/>
                            CFS Publications
                        </label>
                    </div>
                    <div>
                        <label class="checkbox" for="idx-geogratis-documents">
                            <input type="checkbox" name="index[]" value="geogratis-documents" id="idx-geogratis-documents" <?php echo in_array('geogratis-documents', $idx) ? 'checked="checked"' : ''; ?>/>
                            GEOGRATIS Documents
                        </label>
                    </div>
                    <div>
                        <label class="checkbox" for="idx-geogratis-maps">
                            <input type="checkbox" name="index[]" value="geogratis-maps" id="idx-geogratis-maps" <?php echo in_array('geogratis-maps', $idx) ? 'checked="checked"' : ''; ?>/>
                            GEOGRATIS Maps
                        </label>
                    </div>
                    <div>
                        <label class="checkbox" for="idx-ess-publications">
                            <input type="checkbox" name="index[]" value="ess-publications" id="idx-ess-publications" <?php echo in_array('ess-publications', $idx) ? 'checked="checked"' : ''; ?>/>
                            ESS 2012 Publications
                        </label>
                    </div>
                </fieldset>
                <div>
                    <label for="sort">Sort by:</label>
                    <select name="sort" id="sort">
                        <option value="score"<?php echo $sort == 'score' ? ' selected="selected"' : ''; ?>>Relevance</option>
                        <option value="year"<?php echo $sort == 'year' ? ' selected="selected"' : ''; ?>>Publication year</option>
                        <option value="title"<?php echo $sort == 'title' ? ' selected="selected"' : ''; ?>>Publication title</option>
                        <option value="authors"<?php echo $sort == 'authors' ? ' selected="selected"' : ''; ?>>Publication authors</option>
                    </select>
                    <br/>
                    <label class="radio inline" for="dir-asc"><input type="radio" name="dir" id="dir-asc" value="asc" <?php echo $dir == 'asc' ? 'checked="checked"' : ''; ?>/>Ascending</label>
                    <label class="radio inline" for="dir-desc"><input type="radio" name="dir" id="dir-desc" value="desc" <?php echo $dir == 'desc' ? 'checked="checked"' : ''; ?>/>Descending</label>    
                </div>
                
            </div>
        </div>
        
        </form>
        
        <hr/>
        
        <?php if ($results !== false) : ?>
        
        <?php $items = $results->hits->hits; ?>

        <p>Found <strong><?php echo $hits; ?></strong> documents.</p>
        <?php echo paginate($page, $pages, $query, $idx, $sort, $dir); ?>
        <?php foreach ($items as $item) : ?>
        
        <?php // print_r($item); exit; ?>
        <?php $index_key = $item->_index . '-' . $item->_type; ?>
        <div class="well well-small">
        <p><a href="<?php echo $item->_source->url; ?>"><?php echo isset($item->highlight->title) ? $item->highlight->title[0] : $item->_source->title; ?></a></p>
            <p>
                Source: <?php echo $valid_idx[$index_key]; ?>
            </p>
            <?php if (isset($item->_source->authors)) : ?>
            <p>Authors: <?php echo $item->_source->authors; ?></p>
            <?php endif; ?>
            <?php if (isset($item->highlight->body)) : ?>
            <?php foreach ($item->highlight->body as $highlight_body) : ?>
            <p><?php echo $highlight_body; ?></p>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($item->_score)) : ?>
            <p>Score: <?php echo $item->_score; ?></p>
            <?php endif; ?>
        </div>
        
        
        <?php endforeach; ?>
        
        <?php echo paginate($page, $pages, $query, $idx, $sort, $dir); ?>
        
        <?php endif; ?>
    </div>
    <script src="../bootstrap/js/bootstrap.js" />
    
</body>

</html>

