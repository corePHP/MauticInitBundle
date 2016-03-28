MauticInitBundle
================================

A Mautic Plugin for initializing Mautic from the command line.  Works with Mautic version 1.2.4

Installation
-------------------------

Just clone this repo into the `/plugins` folder of your mautic install.

```
#!bash

git clone https://github.com/corePHP/MauticInitBundle.git /path/to/mautic/plugins/.
```


Usage
-------------------------

**You will need to first create a local config file for defining your database connection**

To build your Mautic database we first create the file `local.php` in the `/app/config` folder of your Mautic install.  Here's an example:

```
<?php
$parameters = array(
	'db_driver' => 'pdo_mysql',
	'db_host' => '127.0.0.1',
	'db_port' => null,
	'db_name' => 'mautic',
	'db_user' => 'user',
	'db_password' => 'password',
	'db_prefix' => 'mautic'
);
```

We recommend flushing the cache before running this command.

```
#!bash

php app/console cache:clear --env=prod --no-warmup
```

Then run the following from the command line, replacing the fields in `{}` with your own information.

```
#!bash

php app/console corephp:mautic:init {first_name} {last_name} {email} {username} {password}
```