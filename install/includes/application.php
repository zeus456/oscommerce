<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

// Set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);

  define('OSCOM_BASE_DIRECTORY', dirname(__FILE__) . '/../../includes/');
  class OSCOM { const BASE_DIRECTORY = OSCOM_BASE_DIRECTORY; }

  define('DEFAULT_LANGUAGE', 'en_US');
  define('HTTP_COOKIE_PATH', '');
  define('HTTPS_COOKIE_PATH', '');
  define('HTTP_COOKIE_DOMAIN', '');
  define('HTTPS_COOKIE_DOMAIN', '');

  require('../includes/functions/compatibility.php');

  require('../includes/functions/general.php');
  require('functions/general.php');
  require('../includes/functions/html_output.php');

  require('../includes/classes/Database.php');
  require('../includes/classes/Database/mysqli.php');
  require('../includes/classes/Database/mysqli_innodb.php');
  
  require('../includes/classes/xml.php');

  session_start();

  require('../includes/classes/DirectoryListing.php');

  require('includes/classes/language.php');
  $osC_Language = new osC_LanguageInstall();

  header('Content-Type: text/html; charset=' . $osC_Language->getCharacterSet());
?>
