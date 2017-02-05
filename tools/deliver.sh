#!/usr/bin/env bash

# this script is responsible to deliver(update remote)
# and call the deploy script in the remote machine

. $(dirname "$0")/functions.sh


#Check parameters
if [ $# -eq 0 ] || [[ "$1" != "master" ]] && [[ "$1" != "pre" ]]
then
    display_error "You must set a branch to deliver (master/pre)"
    die
else
    branch=$1
    display_info "Trying to deliver branch: $branch"
fi

env="dev"
#if [[ "$branch" == "master" ]]
#then
#    env="prod"
#fi

server_alias='nzlabes'
base_dir='/srv/imploy'
destination_dir="$base_dir/"
#destination_dir="$base_dir/$branch"

display_info "Delivering here: $destination_dir"
display_info "branch: $branch"
display_info "environment: $env"

#die
#ssh nzlabes "
ssh $server_alias "
mkdir -p $destination_dir;
cd $destination_dir;
git checkout $branch;
git pull origin $branch;
sh ./tools/deploy.sh $env;
exit"


display_info "Finished deliver"


