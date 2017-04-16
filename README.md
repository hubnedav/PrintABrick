# Website for printable building kits
A Symfony project 

## Install

### System requirements
* PHP needs to be a minimum version of PHP 5.5.9
* PHP Extensions
    * FTP 
    * SOAP 
    * PDO 
    * Zip 
* *date.timezone* setting set in *php.ini*
* LDView OSMesa >= 4.2.1 [source](https://tcobbs.github.io/ldview/).

You can check if your system meets requirements by running `$ bin/symfony_requirements`

For full requirements see Symfony 3.2 [docs](http://symfony.com/doc/3.2/reference/requirements.html).

### Installing  
   
#### Back-end
1. Make sure your system meets the application requirements
2. Install dependencies via [Composer](https://getcomposer.org/), `$ composer install`

#### Front-end
1. Install dependencies via [npm](https://www.npmjs.com/), `$ npm install`
2. Compile assets by running [Gulp](http://gulpjs.com/), `$ gulp`

#### Database
1. Set application parameters in *app/config/parameters.yml*
2. Generate empty database by running command `$ php bin/console doctrine:database:create`   
3. Create database tables by running command `$ bin/console doctrine:schema:create`
3. Load LDraw models into database by running commad `$ php bin/console app:load:models <ldraw_dir> [--all] [--file=FILE] [--update]`
4. Load Rebrickable data into database by running command `$ php bin/console app:load:rebrickable`  
5. Load relations between LDraw models and Rebrickable parts by running command `$ php bin/console app:load:relation`  