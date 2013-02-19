<?php
/**
 * global file
 * 
 * @author Lukin <my@lukin.cn>
 * @version $Id$
 * @datetime 2011-10-08 23:49
 */
// Prevent repeated loading
if (defined('APP_PATH')) return 0;
// app version
define('APP_VER', '20130219');
// admin path
define('APP_PATH', dirname(__FILE__));
// include UPF
include APP_PATH . '/UPF/UPF.php';