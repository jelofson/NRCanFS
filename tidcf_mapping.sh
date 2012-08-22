#!/bin/bash

curl -XPUT http://localhost:9200/cfs/trees/_mapping -d '
{
    "trees" : {
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
            }
        }
    }
}'

curl -XPUT http://localhost:9200/cfs/insects/_mapping -d '
{
    "insects" : {
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
            }
        }
    }
}'

curl -XPUT http://localhost:9200/cfs/diseases/_mapping -d '
{
    "diseases" : {
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
            }
        }
    }
}'
