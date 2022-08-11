#!/bin/bash

cat build/app--tailwind-utilities--keys.json | jq | grep -v ':' | egrep '[a-z]' | cut -d '"' -f2 | sed 's/\.//' | cut -d'-' -f1 | sort | uniq
