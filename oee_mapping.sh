#!/bin/bash

curl -XPUT http://localhost:9200/oee/ -d '
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

curl -XPUT http://localhost:9200/oee/energystar/_mapping -d '
{
    "energystar" : {
        "properties" : {
            "title" : {
                "type" : "string",
                "store" : "yes",
                "term_vector" : "with_positions_offsets"
            },
            "body" : {
                "type" : "string",
                "store" : "yes",
                "term_vector" : "with_positions_offsets"
            },
            "catalog_id" : {
                "type" : "string"
            }
        }
    }
}'
