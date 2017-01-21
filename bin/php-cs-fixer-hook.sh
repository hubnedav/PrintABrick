#!/bin/sh

# To use this hook you need to install php-cs-fixer
# Copy this file to ./git/hooks/pre-commit to get it working.

ROOT=""

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER="vendor/bin/php-cs-fixer"
HAS_PHP_CS_FIXER=false

if [  -x vendor/bin/php-cs-fixer ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        $PHP_CS_FIXER fix --config=$ROOT/.php_cs --verbose "$line";
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require --dev friendsofphp/php-cs-fixer"
    echo ""
fi

echo "php-cs-fixer pre commit hook finish"