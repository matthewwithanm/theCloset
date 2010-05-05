<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//---------------------------------------------------------------
// devMode CodeIgniter Plugin
// by Lonnie Ezell (http://igniteyourcode.com/devmode)
//
// version: 1.0
//---------------------------------------------------------------

/*
| -------------------------------------------------------------------
|  ABout devMode
| -------------------------------------------------------------------
| This helper provides a simple, efficient, and site-wide way of
| knowing whether your app is running on the development, test, or
| production server.
|
| For many smaller sites, this helper will not be necessary, though
| it can still prove useful, even if it's only used to set the active
| database group.
|
| When building your $servers array, do not include the http:// or
| https://. Also, make sure that your production server is listed last 
| within the array, so that any subdomain searches will be caught
| prior to finding the main site.
|
*/

function devmode($test_mode=null)
{
    $servers = array(
            'dev'    => 'zero2novel.local',
            'test'    => '',
            'prod'    => 'zero2novel.com'
        );

    // To make testing more accurate, get rid of the http://, etc.
    $current_server = strtolower(trim(base_url(), ' /'));
    $current_server = str_replace('http://', '', $current_server);
    $current_server = str_replace('https://', '', $current_server);
    
    //$current_mode = array_search($current_server, $servers);
    
    $current_mode = '';
    
    // Because the server name could contain www. or subdomains,
    // we need to search each item to see if it contains the string.
    foreach ($servers as $name => $domain)
    {
        if (!empty($domain))
        { 
            if (strpos($current_server, $domain) !== FALSE)    {
                $current_mode = $name;
                break;
            }
        }
    }
    

    // Time to figure out what to return.
    if (empty($test_mode))
    {
        // Not performing a check, so just return the current value
        return $current_mode;
    } else
    {
        return $current_mode == $test_mode;
    }
    
}