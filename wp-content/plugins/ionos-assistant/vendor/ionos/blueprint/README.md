Blueprint
=========

This library provides tools for our fileformat blueprint. Blueprint is a json-format to describe plugins and themes within a WordPress-installation. This file can be shared accross different WordPress-sites.


Quick links: [Installation](#installation) | [Fileformat](#fileformat) | [Usage](#usage)


## Installation

Add or append the following sections to your _composer.json_
~~~
"repositories": [
    {
        "type": "git",
        "url": "https://gitlab.git-wp.server.lan/wp-dev/package/blueprint.git"
    }
],
require": {
    "ionos/blueprint": "^1.0.0"
}
~~~

Alternatively, you can use the CLI:
~~~
composer config repositories.blueprint git https://gitlab.git-wp.server.lan/wp-dev/package/blueprint.git
composer require ionos/blueprint:^1.0.0
~~~

## Fileformat
An example blueprint-json file can be found here: https://wp-dev.pages.git-wp.server.lan/package/blueprint/example.json

## Example Usage

### Read blueprint file
~~~php
use \Ionos\Blueprint\Controller\Blueprint;

// arguments may be left free, debug defaults to false
$blueprint = new Blueprint( ['debug' => false ]);

// by string
$json = $this->blueprint->decode( $json_string );
// by file path
$json = $this->blueprint->decode_file( 'path/to/file.json' );

if ( false === $json ) {
    wp_die( $this->blueprint->get_error() );
}

// do s.th.
foreach ( $json->items as $item ) {

}
~~~

### Create blueprint file
~~~php
use \Ionos\Blueprint\Controller\Blueprint;
use \Ionos\Blueprint\Model\Activity;
use \Ionos\Blueprint\Model\WebApplication;

$blueprint = new Blueprint();
$blueprint->get_data()->set_generator(
    new WebApplication(
        array(
            'name'    => 'WordPress',
            'version' => 6.1.0,
        )
    ));

$app = new WebApplication( [ 'name' => 'TwentyTwentyTwo' ], 'Theme' );
$blueprint->get_data()->add_item( new Activity( 'Install', $app ) );

$blueprint->encode();
~~~

