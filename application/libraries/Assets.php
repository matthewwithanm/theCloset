<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assets {

	private $ci;
	private $host;

	// Javascript variables
	private $inline_scripts		= array();
	private $external_scripts 	= array();
	
	// Styles
	private $styles				= array();	

	//---------------------------------------------------------------

	/**
	 * Constructor.
	 * 
	 * Load the assets config file, and inserts the base
	 * css and js into our array for later use. This ensures
	 * that these files will be processed first, in the order
	 * the user is expecting, prior to and later-added files.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
		
		$this->ci->config->load('assets');
		
		// Load our default styles and scripts
		$this->add_css($this->ci->config->item('assets_css'));
		$this->add_js($this->ci->config->item('assets_js'));
		
		// Setup our host
		$this->host = $this->ci->config->item('asset_host');
		$this->host = empty($this->host) ? base_url() : $this->host;
	}

	//---------------------------------------------------------------
	
	//---------------------------------------------------------------
	// !STYLESHEET FUNCTIONS
	//---------------------------------------------------------------

	/**
	 * add_css function.
	 *
	 * accepts either an array or a string with a single css file name
	 * and appends them to the base styles in $this->styles;
	 *
	 * The file names should NOT have the .css added on to them.
	 * 
	 * @access public
	 * @param mixed $styles. (default: null)
	 * @return void
	 */
	public function add_css($styles=null) 
	{
		if (is_string($styles) && !empty($styles))
		{
			$this->styles[] = $styles;
		} else if (is_array($styles) && count($styles) != 0)
		{
			foreach ($styles as $style)
			{
				$this->styles[] = $style;
			}
		} 
	}
	
	//---------------------------------------------------------------
	
	/**
	 * css function.
	 *
	 * Creates the proper links for inserting into the HTML head, 
	 * depending on whether devmode is 'dev' or other (test/production).
	 *
	 * Accepts an array of styles to be used in place of the base files
	 * set in the config file. This allows you to completely replace
	 * the styles being used in one area of your site, like the admin section.
	 *
	 * If you need additional styles than the base, you should make a call
	 * to add_css(), above.
	 *
	 * The file names should NOT have the .css extension. They will be added.
	 * 
	 * @access public
	 * @param mixed $new_styles. (default: null)
	 * @return void
	 */
	public function css($new_styles=null) 
	{
		$styles = array();
	
		if (is_array($new_styles))
		{
			$styles = $new_styles;
		} else 
		{
			$styles = $this->styles;
		}
		
		$path = $this->ci->config->item('asset_folder') . $this->ci->config->item('css_folder') . '/';

		// If development mode, then render individual links, 
		// else create a link to the combine.php file.		
		if ($this->ci->config->item('combine_on_'.devmode()) === false)
		{
			foreach ($styles as $style)
			{
				echo '<link rel="stylesheet" type="text/css" href="' . $this->host . $path . $style . '.css" />' . "\n";
			}
		} else
		{
			// For test and production modes
			$files = implode('.css,', $styles);
			$files = $files . '.css';
			
			echo '<link rel="stylesheet" type="text/css" href="/combine.php?type=css&files=' . $files . '" />';
		}
	}
	
	//---------------------------------------------------------------
	
	//---------------------------------------------------------------
	// !JAVASCRIPT FUNCTIONS
	//---------------------------------------------------------------
	
	/**
	 * add_js function.
	 *
	 * accepts either an array or a string with a single js file name
	 * and appends them to the base scripts in $this->js;
	 *
	 * The file names should NOT have the .js added on to them.
	 * 
	 * @access public
	 * @param mixed $scripts. (default: null)
	 * @return void
	 */
	public function add_js($scripts=null) 
	{
		if (is_string($scripts) && !empty($scripts))
		{
			$this->external_scripts[] = $scripts;
		} else if (is_array($scripts) && count($scripts) != 0)
		{
			foreach ($scripts as $js)
			{
				$this->external_scripts[] = $js;
			}
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * add_inline_js function.
	 *
	 * Adds scripts to be rendered on just that page, inline.
	 * 
	 * @access public
	 * @param mixed $scripts. (default: null)
	 * @return void
	 */
	public function add_inline_js($scripts=null) 
	{
		if (is_array($scripts) && count($scripts) != 0)
		{
			foreach ($scripts as $js)
			{
				$this->inline_scripts[] = $js;
			}
		} else if (!empty($scripts))
		{
			$this->inline_scripts[] = $scripts;
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * js function.
	 *
	 * Creates the proper links for inserting into the HTML head, 
	 * depending on whether devmode is 'dev' or other (test/production).
	 *
	 * Accepts an array of scripts to be used in place of the base files
	 * set in the config file. This allows you to completely replace
	 * the javascript being used in one area of your site, like the admin section.
	 *
	 * If you need additional scripts than the base, you should make a call
	 * to add_js(), above.
	 *
	 * The file names should NOT have the .js extension. They will be added.
	 * 
	 * @access public
	 * @param mixed $new_js. (default: null)
	 * @return void
	 */
	public function js($new_js=null) 
	{
		$js = array();
	
		if (is_array($new_js))
		{
			$js = $new_js;
		} else 
		{
			$js = $this->external_scripts;
		}

		$this->_external_js($js);
		$this->_inline_js();
	}
	
	//---------------------------------------------------------------
	
	/**
	 * _external_js function.
	 *
	 * This private method does the actual work of generating the
	 * links to the js files. It is called by the js() method.
	 * 
	 * @access private
	 * @param mixed $js. (default: null)
	 * @return void
	 */
	private function _external_js($js=null) 
	{
		if (!is_array($js))
		{
			return;
		}
		
		$path = $this->ci->config->item('asset_folder') . $this->ci->config->item('js_folder') . '/';
	
		// If development mode, then render individual links, 
		// else create a link to the combine.php file.
		if ($this->ci->config->item('combine_on_'.devmode()) === false)
		{
			foreach ($js as $script)
			{
				echo '<script type="text/javascript" src="'. $this->host . $path . $script.'.js" ></script>' . "\n";
			}
		} else
		{
			// For test and production modes
			$files = implode('.js,', $js);
			$files = $files . '.js';
			
			echo '<script type="text/javascript" href="/combine.php?type=javascript&files=' . $files . '" />';
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * _inline_js function.
	 *
	 * This private method does the actual work of generating the
	 * inline js code. All code is wrapped by open and close tags
	 * specified in the config file, so that you can modify it to
	 * use your favorite js library.
	 * 
	 * It is called by the js() method.
	 * 
	 * @access private
	 * @return void
	 */
	private function _inline_js() 
	{	
		// Are there any scripts to include? 
		if (count($this->inline_scripts) == 0)
		{
			return false;
		}
		
		// Create our shell opening
		echo '<script type="text/javascript">' . "\n";
		echo $this->ci->config->item('inline_js_opener') ."\n\n";
		
		// Loop through all available scripts
		// inserting them inside the shell.
		foreach($this->inline_scripts as $script)
		{
			echo $script . "\n";
		}
		
		// Close the shell.
		echo "\n" . $this->ci->config->item('inline_js_closer') . "\n";
		echo '</script>' . "\n";
		
	}
	
	//---------------------------------------------------------------
	
	//---------------------------------------------------------------
	// !IMAGE FUNCTIONS
	//---------------------------------------------------------------
	
	public function image($path='', $extras=array()) 
	{
		if (empty($path)) return;
		
		// Build our extra attributes string
		$attributes = '';
		
		foreach ($extras as $key => $value)
		{
			$attributes .= " $key='$value'";
		}
		
		echo '<img src="'. $this->host . $path .'"'. $attributes .' />';
	}
	
	//---------------------------------------------------------------
}

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
	$ci =& get_instance();
    $servers = $ci->config->item('servers');

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

//---------------------------------------------------------------

// END Assets class

/* End of file Assets.php */
/* Location: ./application/libraries/Assets.php */