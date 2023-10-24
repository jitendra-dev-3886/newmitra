<?php
/**
 * @package    Error Lib
 * **********************************************************************
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   // bdN7RvWNBu8GWV4zg0tF5rAwtzqSNSqTnTyhubYCuemV75k1Sbt2XDgA8ZqKmBQycXThtGuEuYHRBsVKw1rrX7HWBrF7YPvuWevSfHhEAubxRfaRCrHfdgxQ9TqPz6qgkUkNzM5QyYvcFdcWKPYfbHVkWu4pKxYHVrabXcsATXutzHG1qswvYXunrcZw74rq2Gw3X65mGZrKyAq98uQCFgs5PA430BVHGzx7SKZuHXKbgzWxhXB5G8aUVvERvrn4GbG8qTsUwaUnk3US9r9su82fyBERhcv5tucfEzQ9fzYy0bUn6Nab
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Set the platform root path as a constant if necessary.


// Detect the native operating system type.
$os = strtoupper    (           substr(PHP_OS, 0, 3));

if (!defined('IS_WIN'))
{
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (IS_WIN === false) ? true : false);
}
