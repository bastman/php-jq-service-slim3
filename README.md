# php-jq-service-slim3
a sample microservice showcasing jmespath aka "jq"

    Goal: proof-of-concept: serve json files using jmespath query language

    see: http://jmespath.org/

# add json data sources

    copy your *.json files into folder app/resources/datasource/

# query your json data sources

    $ http://localhost:8080/dataset_10000/search?q=.
    $ http://localhost:8080/dataset_10000/search?q=items[0].foo.bar.txt

## build
    $ ./bin/composer self-update
    $ ./bin/composer install
    $ ./bin/composer update
    $ ./bin/composer dump-autoload --optimize

## run service
    $ ./bin/service.sh

## requirements: install php

    - https://developerjack.com/blog/2016/08/26/Installing-PHP71-with-homebrew/
