#!/bin/bash

symfony console doctrine:database:create
symfony console doctrine:migrations:migrate 
symfony server:start --no-tls
