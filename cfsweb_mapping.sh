#!/bin/bash

curl -XPUT http://localhost:9200/cfs/page/_mapping -d '
{
    "page" : {
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

curl -XPUT http://localhost:9200/cfs/employee/_mapping -d '
{
    "employee" : {
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

curl -XPUT http://localhost:9200/cfs/project/_mapping -d '
{
    "project" : {
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

