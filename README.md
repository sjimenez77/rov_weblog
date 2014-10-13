ROV Weblog
==========

ROV weblog is a personal web page and blog made with the [Symfony Standard Edition][1].

1) Install project
------------------

### Vendors and dependencies

In order to install the Symfony dependencies you need [Composer][2] and execute the next commands in your working directory:

    composer selfupdate
    composer update

The second command will prompt you for your database parameters.

### Database

You need to create the database schema. In order to do that you have to execute the next Symfony console command:

    php app/console doctrine:schema:create

### Node modules

In order to use [UglifyCSS][3] and [UglifyJS2][4] compressors you must install these modules locally:

	npm install uglifycss
	npm install uglify-js

If you wish to install them globally on your system with the `-g` option remember to change the path of these tools in the config.yml file.

2) Checking your System Configuration
-------------------------------------

Before starting coding, make sure that your local system is properly
configured for Symfony.

Execute the `check.php` script from the command line:

    php app/check.php

The script returns a status code of `0` if all mandatory requirements are met,
`1` otherwise.

Access the `config.php` script from a browser:

    http://localhost/path/to/symfony/app/web/config.php

If you get any warnings or recommendations, fix them before moving on.

[1]:  http://symfony.com
[2]:  http://getcomposer.org/
[3]:  https://github.com/fmarcia/UglifyCSS
[4]:  https://github.com/mishoo/UglifyJS2