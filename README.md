[![Build Status](https://travis-ci.org/lechimp-p/flightcontrol.svg?branch=master)](https://travis-ci.org/lechimp-p/flightcontrol)
[![Scrutinizer](https://scrutinizer-ci.com/g/lechimp-p/flightcontrol/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lechimp-p/flightcontrol)
[![Coverage](https://scrutinizer-ci.com/g/lechimp-p/flightcontrol/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lechimp-p/flightcontrol)

# Flightcontrol 

**An interface for iteration and recursion over the Leagues 
  [flysystem](https://github.com/thephpleague/flysystem).**


*This README.md is also a literate PHP-file.*

*This code is released under the [MIT License](LICENSE.md)*

## Usage

### Initialisation

The flightcontrol is initialized over a flysystem, e.g. as such:

```php
<?php

require_once("tests/autoloader.php");

use \League\Flysystem\Adapter\Local;
use \League\Flysystem\Filesystem;
use \Lechimp\Flightcontrol\Flightcontrol;

$adapter = new Local(__DIR__, LOCK_EX, Local::SKIP_LINKS);
$flysystem = new Filesystem($adapter);

$flightcontrol = new Flightcontrol($flysystem);

?>
```

Make sure us use the `Local::SKIP_LINKS`-option when using flightcontrol, as it
will only handle files and directories.


### Filesystem Objects

The flightcontrol can give you objects from the flystem:

```php
<?php

// base directory from the flysystem:
$root = $flightcontrol->get("/");
assert($root !== null);
assert($root instanceof \Lechimp\Flightcontrol\FSObject);

// the tests directory:
$tests = $flightcontrol->directory("/tests");
assert($tests !== null);
assert($tests instanceof \Lechimp\Flightcontrol\Directory);

// this file:
$readme = $flightcontrol->file("/README.md");  
assert($readme !== null);
assert($readme instanceof \Lechimp\Flightcontrol\File);

?>
```

Note that you could use `Flightcontrol::get` to either get a directory or file, 
and `Flightcontrol::directory` to get a directory or `Flightcontrol::file` to
get a file respectively. These getters will return null if there is no matching
object on the flysystem.

You could also use `FSObject::toDirectory` or `FSObject::toFile` to force an
FSObject to a file or directory, where you get a `null` if the object is not
what you want it to be.

### Properties of Filesystem Object

The objects returned from the flightcontrol have different properties:

```php
<?php

// properties of every filesystem object:
echo '$tests->path() == '.$tests->path()."\n";
assert($tests->path() == "/tests");
echo '$tests->name() == '.$tests->name()."\n";
assert($tests->name() == "tests");

// properties of files:
echo '$readme->timestamp() == '.$readme->timestamp()."\n";
assert(is_string($readme->timestamp()) || is_int($readme->timestamp()));
echo '$readme->mimetype() == '.$readme->mimetype()."\n";
assert($readme->mimetype() == "text/plain");
echo '$readme->content()'."\n";
assert(is_string($readme->content()));

// properties of directories:
$contents = $tests->contents();
echo '$tests->contents()'."\n";
assert(count($contents) > 0);
assert($contents[0] instanceof \Lechimp\Flightcontrol\FSObject);

?>
```

`File::content` returns the content of the file as string. `Directory::contents`
gives you a list of filesystem objects in the directory.
