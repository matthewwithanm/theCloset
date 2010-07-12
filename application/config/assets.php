<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

///---------------------------------------------------------------
// theCLOSET ASSETS LIBRARY
// by Lonnie Ezell (http://igniteyourcode.com/thecloset)
//
// version: 1.0 Beta 1
//---------------------------------------------------------------

//---------------------------------------------------------------
// Asset Folders
//---------------------------------------------------------------

/**
 * The parent folder that your css/js/images are stored in. 
 * 
 * This should be relative to your index.php file.
 */
$config['asset_folder']		= 'public/';

/**
 * Individual asset folders for css/js/images.
 * 
 * Relative to the asset_folder.
 */
$config['js_folder']		= 'js';
$config['css_folder']		= 'css';
$config['img_folder']		= 'images';

//---------------------------------------------------------------
// Base Assets
//---------------------------------------------------------------

$config['assets_js']	= array('jquery');
$config['assets_css']	= array('application');

$config['inline_js_opener']	= '$(document).ready(function(){';
$config['inline_js_closer'] = '});';

/**
 * Prepended to every js(), css(), or image() call, the use
 * of an asset_host to automatically add a domain to every page
 * makes it simple to change up an existing media path to 
 * different subdomains. This is particularly helpful when
 * switching to a third-party cloud file server like Amazon S3.
 *
 * Leave empty '' for the current domain.
 */
$config['asset_host']		= '';