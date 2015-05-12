<?php
/**
 * imrpomptu.php
*
* Entry page for BEP pipeline view
*
* @version    0.1 2015-04-15
* @package    BEP Pipeline
* @copyright  Copyright (c) 2015 Lisa Hilton
* @license    GNU General Public License
* @since      Since Release 1.0
*/

//Adapted from http://stackoverflow.com/questions/11448307/importing-csv-data-using-php-mysql

require_once('includes/classes/Database.php');
require_once('includes/menu.php');
spl_autoload_register( 'autoload' );
  
  /**
   * autoload
   *
   * @author Joe Sexton <joe.sexton@bigideas.com>
   * @param  string $class
   * @param  string $dir
   * @return bool
   */
  function autoload( $class, $dir = null ) {
 
    if ( is_null( $dir ) )
      $dir = '../';
 
    foreach ( scandir( $dir ) as $file ) {
 
      // directory?
      if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
        autoload( $class, $dir.$file.'/' );
 
      // php file?
      if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {
 
        // filename matches class?
        if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class ) {
 
            include $dir . $file;
        }
      }
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php //echo $title;?>BEP Pipeline</title>
<link rel="stylesheet" type="text/css" href="http://nova.umuc.edu/~ct463b14/pipeline.css">
</head>
<body>
<header>
<h1>BEP Pipeline</h1>
</header>
<nav>
<?php displayMenu();?>
</nav>
<div id="main">
<?php 
include_once ('includes/impromptu.php');
?>
</div>
<footer>
(c) 2015 by Lisa Hilton
</footer>
</body>
</html>