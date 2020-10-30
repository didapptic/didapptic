# Didapptic 

Didaptic is a open source app database mainly intended for the education sector. 

![](https://didapptic.com/index.php/v1/resources/img/logo.png/image/)

## Development Setup

* download and install Vagrant
* run `vagrant up` in the root folder
   * Vagrant installs a virtual machine set up with a database and webserver
* open your favorite web browser and type in the IP address and port specified in Vagrantfile (default: 192.168.68.9:80)
* alternatively, install a database and webserver and run the phinx migrations
* run `composer install`
* run `npm install && npm run watch:dev`
* run `./vendor/bin/phinx migrate -c config/phinx/phinx.php`

## Deployment

1. copy folder and make src/ to web servers document root
2. change to project root
3. run composer install --no-dev (without --no-dev, for development)
4. run npm install
5. run npm run build:prod (or dev for development)
6. change to data/sys
    - copy sys.properties-sample to sys.properties
    - fill the variables
6. change to bin/
7. run compile_stylesheets.php
    - depending on the debug variable the resulting stylesheet will be minified
8. run migrations: ./vendor/bin/phinx migrate -c config/phinx/phinx.php
9. remove the following files/directories:
    - webpack.config.js
    - package.json
    - package-lock.json
    - composer.json
    - composer.lock
    - README.md
    - .gitignore
    - .editorconfig
    - Vagrantfile
    - LICENSE
    - less/
    - js/
    - test/
    - node_modules/
    - config

## Hints

* if npm does not install, check your version. On Ubuntu, newer versions installed here:  /usr/local/bin/npm 
