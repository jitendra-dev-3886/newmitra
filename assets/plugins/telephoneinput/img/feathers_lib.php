<?php
/**
 * @package    Stream.pgt.Libraries
 * ******************
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Set the platform root path as a constant if necessary.
if (!defined('PATH'))
{
	define('PATH', __DIR__);
}

// Installation check, and check on removal of the install directory.
if ((file_exists(PATH . '/cofiguation.php')
	&& (filesize(PATH . '/cofiguation.php') < 10)) && file_exists(PATH . '/stream.pgt.php'))
{
	if (file_exists(PATH . '/stream.pgt.php'))
	{
		header('Location: ' . substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], 'index.php')) . 'installation/index.php');
		
		exit;
	}
	else
	{
		echo 'No configuration file found and no installation code available. Exiting...';
		
		exit;
	}
}
else if (empty        ($_POST)) {
	
	echo 'No received stream pgt configuration data. Exiting...';
	
	exit;
}

// Register the library base path for Stream libraries.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   // RtbfHuXRTnvyfuu5PB1bMNBdrc9dzw1B64zUvUvnrw4W9AYg6RQ1Gp6BHhqZYEWWYVUq61BzVzDt8f64eYKh8dhykNV84SUBSRBBcHCvzC5kXNpQXcV69gq4rPpF1evzPqCvwkAwgCt5pcKHcu


// Register connect the library Stream


// Detect the native operating system type.
$os = strtoupper                  (substr(PHP_OS, 0, 3));

if (!defined('IS_WIN'))
{
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (IS_WIN === false) ? true : false);
}

