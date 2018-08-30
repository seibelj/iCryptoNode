#!/bin/sh

usage () {
    echo "\niCryptoNode Release Generator"
    echo "Usage: create_release.sh [version_number]"
    echo "   EX: create_release.sh 9.8.7\n"
    exit 0
}

if [ -z "$1" ]; then
    usage
fi

RELEASE_FILE="icn-v$1.tar.gz"

if [ -f "$RELEASE_FILE" ]; then
    rm $RELEASE_FILE
fi

tar czvf $RELEASE_FILE icryptonode/*

