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

###Installing  
   
####Back-end
1. Make sure your system meets the application requirements
2. Install dependencies via [Composer](https://getcomposer.org/), `$ composer install`

####Front-end
1. Install dependencies via [npm](https://www.npmjs.com/), `$ npm install`
2. Compile assets by running [Gulp](http://gulpjs.com/) default task, `$ gulp default`

####Database
1. Set application parameters in *app/config/parameters.yml*
2. Generate empty database by running command `$ php bin/console doctrine:database:create`    
3. To load LDraw models into database run commad `$ php bin/console app:load:ldraw`  
     If you prefer local ldraw library you can specify source by running `$ php bin/console app:load:ldraw [ldraw_dir_path]` instead