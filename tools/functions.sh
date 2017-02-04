#!/usr/bin/env bash

#######################################################################
########################  FUNCTIONS  ##################################
#######################################################################
display_error () {
    echo -e "\033[33;31m[ERROR] $1 \033[0m"
}

display_success () {
    echo -e "\033[33;32m[OK] $1 \033[0m"
}

display_info () {
    echo -e "\033[33;33m[INFO] $1 \033[0m"
}

die () {
    exit 1
}
version_lt() { test "$(printf "$2\n$1" | sort -rV | head -n 1)" != "$1"; }
#######################################################################
###################### END FUNCTIONS  #################################
#######################################################################

