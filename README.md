# PrintABrick
Web catalogue of LEGO® parts for 3D printing

A Symfony project 

## Install

### System requirements

* PHP needs to be a minimum version of PHP 7.0
* PHP Extensions
    * FTP 
    * SOAP 
    * GD
    * PDO 
    * Zip 
* *date.timezone* setting set in *php.ini*

You can check if your system meets requirements by running `$ bin/symfony_requirements`

For full requirements see Symfony 3.3 [docs](http://symfony.com/doc/3.3/reference/requirements.html).


#### Required 
* Elasticsearch >= 5

    Instructions for installing and deploying Elasticsearch may be found [here](https://www.elastic.co/downloads/elasticsearch). 
* POV-Ray [source](http://www.povray.org/).
* stl2pov [source](https://github.com/rsmith-nl/stltools/releases/tag/3.3).
* ADMesh 
* LDView OSMesa >= 4.2.1 [source](https://tcobbs.github.io/ldview/).

### Installing  
   
#### Back-end
1. Make sure your system meets the application requirements
2. Install dependencies via [Composer](https://getcomposer.org/), `$ composer install`

#### Front-end
1. Install dependencies via [npm](https://www.npmjs.com/), `$ npm install`
2. Install bower dependencies via [bower](https://bower.io), `$ bower install`
3. Compile assets by running [Gulp](http://gulpjs.com/), `$ gulp default [--env production]`

#### Initialization

##### Setup database 
1. Set application parameters in *app/config/parameters.yml*
2. Generate an empty database by running command (if it does not yet exist) `$ bin/console doctrine:database:create`   
3. Create database tables/schema by running command`$ bin/console doctrine:schema:create`
4. Load database fixtures `$ bin/console doctrine:fixtures:load`

##### Load data
You can load initial application data by running command `$ bin/console app:init`

This command consists of multiple subcommands that can be called separately:
1. Load LDraw models into database by running commad `$ bin/console app:load:ldraw [--ldraw=PATH] [--all] [--file=FILE] [--update] `
2. Load Rebrickable data into database by running command `$ bin/console app:load:rebrickable`  
3. Load relations between LDraw models and Rebrickable parts by running command `$ bin/console app:load:relations` 
4. Download images of models from rebrickable.com `$ bin/console app:load:images [--color=INT] [--rebrickable] [--missing]`
5. Populate Elastisearch index `$ bin/console fos:elastica:populate`

##### Adding part relation 
Relations between LDraw models and Rebrickable parts are matched automatically by identical (or similar) id/name when executing command `$ bin/console app:load:relation`. 

Unmatched relations can be specified by adding relation of IDs to `app/Resources/relations/part_model.yml` 

## Testing
The test database must be created before the first test runs. You can create new one by running:

1. Generate an empty database by running command (if it does not yet exist) `$ bin/console doctrine:database:create --env=test`   
2. Create database tables/schema by running command`$ bin/console doctrine:schema:create --env=test`


You can run complete system tests by `$ phpunit`. These should cover the main system functions and the functionality of calling the third-party programs that are required are needed to seamlessly retrieve the necessary application data.
