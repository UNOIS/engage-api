# engage-api

An abstraction class for the Campus Labs Engage product

# Requirements

- PHP >= 7.0
- [php guzzle/guzzle/](https://github.com/guzzle/guzzle/)

# Installation

1. Download and Install PHP Composer.

   ``` sh
   curl -sS https://getcomposer.org/installer | php
   ```

2. Add the following to your composer.json file.
   ```json
	"repositories": [
        {
        	"type" : "vcs",
        	"url": "https://github.com/unoadis/engage-api"
        }
   ```
   ```json
   "require" : {
        "unoadis/engage-api" : "dev-master"
   }
   ```

3. Then run Composer's install or update commands to complete installation.

   ```sh
   php composer.phar install
   ```

# Example

   ```php
   require '../vendor/autoload.php';

	use EngageApi\Client as EngageClient;

	$baseUri  = 'https://<subdomain>.campuslabs.com/engage/api/';
	$clientId = '';
	$secret   = '';

	try {
	    echo "<pre>";
	    $api = new EngageClient($baseUri, $clientId, $secret);
	    $o = $api->getOrganization('<integer>');
	    print_r($o);
	    echo "</pre>";
	} catch (Exception $e) {
	    echo($e->getMessage());
	}
   ```
