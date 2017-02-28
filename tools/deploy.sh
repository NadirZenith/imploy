#!/usr/bin/env sh

# this script is responsible to deploy(install)
# the app in the current machine

. $(dirname "$0")/functions.sh

display_info "Working Dir:"
pwd

#Check parameters
if [ $# -eq 0 ]
then
    display_error "You must set an environment (dev|test|prod)"
    die
else
    display_success "Environment: $1"
fi

#Check php binary
if [ ! -x 'bin/php' ]
then
    display_error "PHP CLI not found "
    display_info "Do you forgot to create a link? (ln -s /usr/bin/php bin/php)"
    die
else
    version=`bin/php -v | grep cli`
    display_success "PHP found: $version"
fi

#Check Composer binary
if [ ! -x 'bin/composer' ]
then
    display_error "Composer not found at 'bin' folder"
    display_info "Do you forgot to create a link? (ln -s /usr/local/bin/composer bin/composer)"
    die
else
    version=`bin/composer -V`
    display_success "Composer found: $version"
fi

#Check NODE binary
if [ ! -x 'bin/node' ]
then
    display_error "NODE not found at 'bin' folder"
    display_info "Do you forgot to create a link? (ln -s /usr/local/bin/node bin/node)"
    die
else
    version=`bin/node -v`
    display_success "Node found: $version"
fi

#Check NPM binary
if [ ! -x 'bin/npm' ]
then
    display_error "NPM not found at 'bin' folder"
    display_info "Do you forgot to create a link? (ln -s /usr/local/bin/npm bin/npm)"
    die
else
    version=`bin/npm -v`
    min='2.0'
    if version_lt $min $version; then
        display_success "NPM found: $version"
    else
        display_error "Old npm version found, require +$min"
        die
    fi
fi

display_info 'Check validators'
dumps=`find src/ -type f -print0 | xargs -0 grep -l "dump("`
if [ ! -z "$dumps" ]
then
    display_error "Remove dump() function from: $dumps"
    die
else
    display_success "* dump() calls not found in src"
fi

dumps=`find app/Resources/ -type f -print0 | xargs -0 grep -l "dump("`
if [ ! -z "$dumps" ]
then
    display_error "Remove dump() function from: $dumps"
    die
else
    display_success "* dump() calls not found views"
fi

#Check for htaccess & robots
#if [ ! -e 'web/.htaccess' ]
#then
#    cp web/.htaccess.dist web/.htaccess
#    display_info ".htaccess generated"
#fi
if [ ! -e 'web/robots.txt' ]
then
    cp web/robots.txt.dist web/robots.txt
    display_info "robots.txt generated"
fi


display_info 'Check for NPM updates'
bin/npm update
bin/npm list --depth=0

if [ $1 = 'dev' ]
then
    display_info 'Check for COMPOSER updates'
    export SYMFONY_ENV=dev
    bin/composer install

    display_success 'Upgrade database'
    bin/php bin/console doctrine:schema:update --dump-sql --force

#    display_success 'Generate ASSETS'
#    bin/node node_modules/.bin/grunt --force default

elif [ $1 = 'test' ]
then
    display_info 'Check for COMPOSER updates'
    export SYMFONY_ENV=dev
    bin/composer install

    display_success 'Upgrade database'
    bin/php app/console doctrine:schema:update --dump-sql --force
    bin/php app/console doctrine:fixtures:load --no-interaction

#    display_success 'Generate ASSETS'
#    bin/node node_modules/.bin/grunt --force package

elif [ $1 = 'prod' ]
then
    display_info 'Check for COMPOSER updates'
    export SYMFONY_ENV=prod
    bin/composer install --no-dev --optimize-autoloader

    display_success 'Upgrade database'
    bin/php app/console doctrine:schema:update --dump-sql --force

#    display_success 'Generate ASSETS'
#    bin/node node_modules/.bin/grunt --force package

else
    display_error 'Environment not exists'
    die
fi

display_success 'Done'