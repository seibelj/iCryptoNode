#!/bin/sh

NUM_DIRS=$(find $1 -type d -mindepth 1 -maxdepth 1 | wc -l)

if [ "$NUM_DIRS" -eq "1" ]; then
    PARENT_DIR=$(find $1 -type d -mindepth 1 -maxdepth 1)
    mv "$PARENT_DIR" "$1/$2"
fi

