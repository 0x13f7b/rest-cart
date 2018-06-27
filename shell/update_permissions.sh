#!/usr/bin/env bash

HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
HTTPDUSER_MAC=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

console="bin/console"
var="var"
cache="var/cache"
log="var/logs"

mkdir -p var
mkdir -p $cache $log

rm -rf $cache/*
rm -rf $cache/*
chmod -R 755 $console
chmod -R 777 $cache $log
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX $var
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX $var
chmod -R +a "$HTTPDUSER_MAC allow delete,write,append,file_inherit,directory_inherit" $var
chmod -R +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" $var
