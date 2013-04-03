<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Federated Search Prototype</title>
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    
    
    <style>
    body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
    }
    
    em.hlt1, em.hlt2, em.hlt3, em.hlt4, em.hlt5, em.hlt6, em.hlt7, em.hlt8, em.hlt9, em.hlt10 {
        background-color: #ffc;
        padding: 0 2px;
    }
    
    </style>
    <link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
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
                <a class="brand" href="index.php">NRCan Federated Search</a>
                <div class="nav-collapse collapse">
                    <ul class="nav">
                      <li class="active"><a href="about.php">About</a></li>
                      <li><a href="mailto:jelofson@nrcan.gc.ca">Contact</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
                
            </div>
        </div>
    </div>
    <div class="container">
        <h2>NRCan Federated Search: About</h2>
        
        <p>This NRCan Federated Search (NRCan FS) provides a simple, single point of access to NRCan publications from 
        several sources. The concept has some important advantages as outlined below.<p>
        
        <h3>How it works</h3>
        <p>The NRCan FS works by indexing (crawling) data from a data source. The data source can be almost anything, such as 
        a database table, or structured data such as XML or JSON. As long as the data is accessible and structured, it can be indexed. 
        It can be a local file, or a URL to some web page, similar to an RSS feed.
        </p>
        <p>Behind the scenes are pre-defined maps that tell the index what fields will be indexed and why kind of fields they are. 
        For example, a date field vs a keyword field. Once the map is created, we write a program that goes out and gets data from 
        a data provider, such as the CFS Publications Database. We get permission from the data provider for the data and some 
        information about the data fields they provide, and then we customize our program to insert their data into our index. 
        Never do we change their data or ask them to change it. This is a read-only operation.</p>
        
        <h3>Not a copy</h3>
        <p>The data stored and used by the NRCan FS is <strong>not a copy</strong> of the original data. It is simply an index of the key words
        and fields used by the source data. Some of the fields may be stored verbatim in the index, but that is only to provide 
        useful results (for example, the title of the document). In other words, we don't create a new database to hold the exact 
        same data that is already in the source application. That would be an obvious duplication of effort. If we made a copy 
        of the data, we would still have to create some sort of index on that data to be able to effectively search it. 
        Also, having a data copy means there is significant risk of the copies behing out of sync.
        Here, we skip the copy step and simply create the search index.</p>
        
        <h3>Pain free for content owners</h3>
        <p>The majority of the heavy lifting is handled by the NRCan FS background processes. Since the NRCan FS is the 
        consumer of other services' data, we don't want to dictate how the data provider should be providing their data.
        It's up to the NRCan FS to map the data providers fields to common fields that make sense for us. For example,
        we would like to use a common field called "citation"; however, the CFS Publications database uses a field 
        called "citation_title_first". We don't want to tell them to change their naming structure to suit our needs. Instead,
        we do the mapping at our end. When we do our indexing, we tell the system that "citation_title_first" is the 
        same as "citation". Now, it may not always be that simple. It may be necessary to request a new field, such as
        "plain_language_summary", but we don't tell them what to call it. </p>
        
        <p>We also don't want to burden the content providers with manual exports. Instead, we just point our indexer at
        the URL (web page address) of their data and consume (index) it when we want to. This is much simpler than asking the 
        content providers to export a copy of their data and manually put it into a special folder and some periodic interval.</p>
        
        <h3>Flexible</h3>
        <p>The ElasticSearch indexer has a huge variety of indexing options, including word highlighting, weighting, sorting, etc.
        Additionally, since the search results are a simple web page, we can format the display any way we want. In fact, we 
        can even make them smartphone and tablet friendly. We are in complete control over how the data is displayed to the user.
        It is also possible to create separate interfaces for public or internal clients where we define the different fields 
        displayed in the results. Regardless of the search interface, there is still only a single index working behind the 
        scenes. We can create custom or saved searches, data exports based on search parameters. The possibilities are pretty 
        much endless.</p>
        
        
        <h3>Speed</h3>
        <p>The search performs very well and very fast. Several thousand records can be retrieved in the blink of an eye. We can also 
        update or add individual records without needing to re-index the entire data set.</p>
        
        
        
    </div>
    <script src="../bootstrap/js/bootstrap.js" />
    
</body>

</html>
