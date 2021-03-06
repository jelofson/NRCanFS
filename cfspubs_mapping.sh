#!/bin/bash

curl -XPUT http://localhost:9200/cfs/ -d '
{
    "index": {
        "analysis": {
            "analyzer": {
                "default": {
                    "tokenizer": "standard",
                    "filter": ["lowercase", "snowball"]
                }
            }
        }
    }
}'

curl -XPUT http://localhost:9200/cfs/publications/_mapping -d '
{
    "publications" : {
        "properties" : {
            "title" : {
                "type" : "multi_field",
                    "fields" : {
                        "title" : {
                            "type" : "string",
                            "store" : "yes",
                            "term_vector" : "with_positions_offsets"
                        },
                        "title_fr" : {
                            "type" : "string",
                            "store" : "yes",
                            "term_vector" : "with_positions_offsets",
                            "analyzer" : "french"
                        },
                        "sortable" : {
                            "include_in_all" : false,
                            "index" : "not_analyzed",
                            "type" : "string"
                        }
                    }
            },
            "authors" : {
                "type" : "multi_field",
                "fields" : {
                    "authors" : {
                        "type" : "string"
                    },
                    "sortable" : {
                        "type" : "string",
                        "index" : "not_analyzed",
                        "include_in_all" : false
                    }
                }
            },
            "body" : {
                "type" : "string",
                "store" : "yes",
                "term_vector" : "with_positions_offsets"
            },
            "year" : {
                "type" : "date",
                "format" : "YYYY"
                
            },
            "date_added" : {
                "type" : "date",
                "format" : "YYYY-MM-DD"
            }
        }
    }
}'
