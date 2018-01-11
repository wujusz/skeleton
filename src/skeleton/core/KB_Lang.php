<?php
/**
 * CodeIgniter Skeleton
 *
 * A ready-to-use CodeIgniter skeleton  with tons of new features
 * and a whole new concept of hooks (actions and filters) as well
 * as a ready-to-use and application-free theme and plugins system.
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2018, Kader Bouyakoub <bkader@mail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package 	CodeIgniter
 * @author 		Kader Bouyakoub <bkader@mail.com>
 * @copyright	Copyright (c) 2018, Kader Bouyakoub <bkader@mail.com>
 * @license 	http://opensource.org/licenses/MIT	MIT License
 * @link 		https://github.com/bkader
 * @since 		Version 1.0.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * KB_Lang Class
 *
 * This class extends CI_Lang class in order en add, override or
 * enhance some of the parent's methods.
 *
 * @package 	CodeIgniter
 * @subpackage 	Skeleton
 * @category 	Core Extension
 * @author 		Kader Bouyakoub <bkader@mail.com>
 * @link 		https://github.com/bkader
 * @copyright	Copyright (c) 2018, Kader Bouyakoub (https://github.com/bkader)
 * @since 		Version 1.0.0
 * @version 	1.0.0
 */
class KB_Lang extends CI_Lang
{
	/**
	 * Fall-back language.
	 * @var string
	 */
	protected $fallback = 'english';

	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @param	mixed	$langfile	Language file name
	 * @param	string	$idiom		Language name (english, etc.)
	 * @param	bool	$return		Whether to return the loaded array of translations
	 * @param 	bool	$add_suffix	Whether to add suffix to $langfile
	 * @param 	string	$alt_path	Alternative path to look for the language file
	 *
	 * @return	void|string[]	Array containing translations, if $return is set to true
	 */
	public function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
	{
		if (is_array($langfile))
		{
			foreach ($langfile as $value)
			{
				$this->load($value, $idiom, $return, $add_suffix, $alt_path);
			}

			return;
		}

		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix === true)
		{
			$langfile = preg_replace('/_lang$/', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
		{
			$config =& get_config();
			$idiom = empty($config['language']) ? $this->fallback : $config['language'];
		}

		if ($return === false && isset($this->is_loaded[$langfile]) && $this->is_loaded[$langfile] === $idiom)
		{
			return;
		}

		// Prepare the array of language lines and
		// load the english version first.
		$full_lang = array();

		// Load the base file, so any others found can override it
		$basepath = BASEPATH.'language/'.$this->fallback.'/'.$langfile;
		if (($found = file_exists($basepath)) === true)
		{
			include_once($basepath);
		}

		// Do we have an alternative path to look in?
		if ($alt_path !== '')
		{
			$alt_path .= 'language/'.$this->fallback.'/'.$langfile;
			if (file_exists($alt_path))
			{
				include_once($alt_path);
				$found = true;
			}
		}

		if ($found !== true)
		{
			foreach (get_instance()->load->get_package_paths(true) as $package_path)
			{
				$package_path .= 'language/'.$this->fallback.'/'.$langfile;
				if ($basepath !== $package_path && file_exists($package_path))
				{
					include_once($package_path);
					$found = true;
					break;
				}
			}
		}

		if ($found !== true)
		{
			show_error('Unable to load the requested language file: language/'.$this->fallback.'/'.$langfile);
		}

		// Was the language array updated?
		$full_lang = isset($lang) ? $lang : array();

		// Proceed only if the requested language id different of the fallback.
		if ($this->fallback <> $idiom)
		{
			$lang = array();

			// Load the base file, so any others found can override it
			$basepath = BASEPATH.'language/'.$idiom.'/'.$langfile;
			if (file_exists($basepath))
			{
				include_once($basepath);
			}

			// Do we have an alternative path to look in?
			if ($alt_path !== '')
			{
				$alt_path .= 'language/'.$idiom.'/'.$langfile;
				if (file_exists($alt_path))
				{
					include_once($alt_path);
					$found = true;
				}
			}
			else
			{
				foreach (get_instance()->load->get_package_paths(true) as $package_path)
				{
					$package_path .= 'language/'.$idiom.'/'.$langfile;
					if ($basepath !== $package_path && file_exists($package_path))
					{
						include_once($package_path);
						$found = true;
						break;
					}
				}
			}
		}
		else
		{
			$idiom = $this->fallback;
		}

		if ($found !== true)
		{
			show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
		}

		(isset($lang)) OR $lang = array();
		$full_lang = array_replace_recursive($full_lang, $lang);

		if ( ! isset($full_lang) OR ! is_array($full_lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);

			if ($return === true)
			{
				return array();
			}
			return;
		}

		if ($return === true)
		{
			return $full_lang;
		}

		$this->is_loaded[$langfile] = $idiom;
		$this->language = array_merge($this->language, $full_lang);

		log_message('info', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return true;
	}

	// --------------------------------------------------------------------

	/**
	 * Language line
	 *
	 * Fetches a single line of text from the language array
	 *
	 * @param	string	$line 	Language line key
	 * @param	string	$index 	Acts like gettext domain.
	 * @return	string	Translation
	 */
	public function line($line, $index = '')
	{
		if ($index == '')
		{
			$value = (isset($this->language[$line]))
				? $this->language[$line]
				: false;
		}
		else
		{
			$value = (isset($this->language[$index][$line]))
				? $this->language[$index][$line]
				: false;
		}

		// Because killer robots like unicorns!
		if ($value === false)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');

			// Use inflector to generate the $value instead.
			// (function_exists('humanize')) OR get_instance()->load->helper('inflector');
			// $value = humanize($line);
			$value = "FIXME('{$line}')";
		}

		return $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('line'))
{
	/**
	 * Alias of Lang::line with optional arguments.
	 *
	 * @param 	string 	$line 	the line the retrieve.
	 * @param 	string 	$index 	whether to look under an index.
	 * @return 	string
	 */
	function line($line, $index = '')
	{
		return get_instance()->lang->line($line, $index);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('__'))
{
	/**
	 * Alias of Lang::line with optional arguments.
	 *
	 * @param 	string 	$line 	the line the retrieve.
	 * @param 	string 	$index 	whether to look under an index.
	 * @return 	string
	 */
	function __($line, $index = '')
	{
		return get_instance()->lang->line($line, $index);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_e'))
{
	/**
	 * Alias of Lang::line with optional arguments.
	 *
	 * @param 	string 	$line 	the line the retrieve.
	 * @param 	string 	$index 	whether to look under an index.
	 * @return 	string
	 */
	function _e($line, $index = '')
	{
		echo get_instance()->lang->line($line, $index);
	}
}
