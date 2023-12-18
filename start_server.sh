#!/bin/bash

symfony console doctrine:migrations:migrate 
symfony server:start --no-tls
