#!/bin/bash

curl -XPUT http://localhost:9200/geogratis/ -d '
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

curl -XPUT http://localhost:9200/geogratis/documents/_mapping -d '
{
    "documents" : {
        "properties" : {
            "title" : {
                "type" : "multi_field",
                    "fields" : {
                        "title" : {
                            "type" : "string",
                            "store" : "yes",
                            "term_vector" : "with_positions_offsets"
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
            }
        }
    }
}'

