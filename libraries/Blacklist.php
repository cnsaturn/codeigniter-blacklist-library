<?php
/**
* Blacklist
*
* A simple blacklist library for codeigniter.
*
* @package 		Blacklist
* @version 		1.0
* @author  		Yang Hu <yangg.hu@gmail.com>
* @license 		Apache License v2.0
* @copyright 	2010 Yang Hu
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
class Blacklist
{

	/**
	 * Blocked ip addresses
	 *
	 * @access  protected
	 * @var		array
	 */
	protected $_ip_addresses = array();

	/**
	 * Forbidden keywords
	 *
	 * @access  protected
	 * @var		array
	 */
	protected $_words = array();

	/**
	 * Regexs that can be used to match against 
	 *   special forbidden string pattern
	 *
	 * @access  protected
	 * @var		array
	 */
	protected $_regexs = array();

	/**
	 * Is the target IP or text block permitted?
	 *
	 * @access protected
	 * @var		bool
	 */
	protected $_blocked = FALSE;
	
	/**
	 * IP addresses to check
	 *
	 * @access public
	 * @var		array
	 */
	public $target_ip = array();
	
	/**
	 * Text blocks to check 
	 *
	 * @access public
	 * @var		array
	 */
	public $target_text = array();

	/**
	 * Construct
	 *
	 *	Populate our blacklist from the config file.
	 *
	 *
	 * @access public
	 * @param	array  $config
	 * @var		array
	 */
	public function __construct($config)
	{
		// Notice: Config array corresponding with the class are automatically
		// loaded when loading the library itself
		if (!empty($config))
		{
			foreach ($config as $key => $val)
			{
				if (isset($this->{'_' . $key}))
				{
					$val = ! empty($val) ? is_array($val) ? $val : array($val) : array();	
					$this->{'_' . $key} = $val;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Is the target IP or text block blocked?
	 *
	 * @access public 
	 * @return bool	  TRUE if it is blocked
	 */
	public function is_blocked()
	{
		return $this->_blocked;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add an IP address to the current forbidden IP blacklist
	 *
	 * @access  public 
	 * @param	mixed   array|string IP address or an array of addresses (allows wildcard '*')
	 * @return  object  $this
	 */
	public function add_ip($ips)
	{
		$ips = ! is_array($ips) ? array($ips) : $ips;
		
		foreach($ips as $ip)
		{
			if (preg_match('/^([\d\*]{1,3}\.){3}[\d\*]{1,3}$/', $ip)) 
			{
				$long = ip2long(str_replace('*', '1', $ip));
				
				if ($long != -1 AND $long !== FALSE) 
				{
					continue;
				}
			}
			
			show_error("Invalid IP address: $ip");
		}
		
		$this->_ip_addresses = array_merge($this->_ip_addresses, $ips);

		return $this;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a keyword to the current forbidden word blacklist
	 *
	 * @access  public 
	 * @param	mixed  array|string keyword or an array of keywords
	 * @return  object $this
	 */
	public function add_word($words)
	{
		$words = ! is_array($words) ? array($words) : $words;
		
		$this->_words = array_merge($this->_words, $words);

		return $this;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a regex (aka. regular expression) pattern to the blacklist
	 *
	 * @access public 
	 * @param	mixed  array|string regex or an array of regexs
	 * @return object $this
	 */
	public function add_regex($regexs)
	{
		$regexs = ! is_array($regexs) ? array($regexs) : $regexs;
		
		$this->_regexs = array_merge($this->_regexs, $regexs);

		return $this;
	}
	
	// --------------------------------------------------------------------


	/**
	 * Check whether or not the IP is in the blacklist
	 *
	 * @access public 
	 * @return object $this
	 */
	public function check_ip($ips)
	{
		$this->_set_target_ip($ips);
		
		$ip_address = implode(" ", $this->target_ip);

		foreach ($this->_ip_addresses as $ip) 
		{
			$regex = str_replace(array('*', '.'), array('\d{1,3}', '\.'), $ip);
			
			if (preg_match("/$regex/", $ip_address)) {
				
				$this->_blocked = TRUE;
				
				log_message('debug', "Found IP address in the blacklist: '$ip'");
			}
		}

		return $this;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Check whether or not the text block includes forbidden keywords
	 *
	 * @access public 
	 * @param  string  $texts text blocks to check
	 * @return object  $this
	 */
	public function check_text($texts)
	{
		$this->_set_target_text($texts);
		
		foreach ($this->_words as $word) 
		{
			if (stripos($this->target_text, $word) !== false) 
			{
				$this->_blocked = TRUE;
				
				log_message('debug', "Found forbidden word: '$word' in the given text.");
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Check whether or not the given text includes strings that matching
	 * the regex.
	 *
	 * @access public 
	 * @return object $this
	 */
	public function check_regex()
	{
		foreach ($this->_regexs as $regex) 
		{
			if (preg_match($regex, $this->target_text, $m)) 
			{
				$this->_blocked = TRUE;
				
				log_message('debug', "Found forbidden word '$word' in the text.");
			}
		}
		
		return $this;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Replace the forbidden word(s)
	 *
	 * @access  public
	 * @param	mixed  array|string The given text block or an array of text blocks
	 * @param	string marks to replace the forbidden word(s)
	 * @return  mixed  array|string 
	 */
	public function replace($text, $fill = '*')
	{
		// If $text is an array, this function recursively invokes itself
		if (is_array($text) AND !empty($text))
		{
			$result = array();
			
			foreach ($text as $t) 
			{
				$result[] = $this->replace($t, $fill);
			}
			
			return $result;
		} 
		// If $text is just a string...
		else 
		{
			// Replace the words matched aganist that within the blacklist
			foreach ($this->_words as $word) 
			{
				if (stripos($text, $word) !== false) 
				{
					$replacement = implode('', array_fill(0, iconv_strlen($word, 'UTF-8'), $fill));
					$result = str_ireplace($word, $replacement, $text);
					$text = $result;
				}
			}
			
			// Replace the regexs matched aganist that within the blacklist
			foreach ($this->_regexs as $regex) 
			{
				if (preg_match($regex, $result)) 
				{
					// Fill stuff for regexs are not that accurate because of escape marks, 
					// so we only exclude the starting and ending mark of the regex, that is '/'
					// e.g. /\bphp\b/i
					$replacement = implode('', array_fill(0, iconv_strlen($regex, 'UTF-8') - 2, $fill));
					$result = preg_replace($regex, $replacement, $result);
				}
			}
			
			return $result;
		}
		
		return;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the target IP
	 *
	 * @access  private 
	 * @param	mixed  array|string
	 * @return  object $this
	 */
	private function _set_target_ip($ips)
	{
		$ips = ! is_array($ips) ? array($ips) : $ips;
		
		foreach($ips as $ip)
		{
			if (preg_match('/^([\d\*]{1,3}\.){3}[\d\*]{1,3}$/', $ip)) 
			{
				$long = ip2long(str_replace('*', '1', $ip));
				
				if ($long != -1 AND $long !== FALSE) 
				{
					continue;
				}
			}
			
			show_error(lang('invalid_ip', array($ip)));
		}
		
		$this->target_ip = $ips;
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Set the target text block
	 *
	 * @access  private 
	 * @param	mixed  array|string
	 * @return  object $this
	 */
	private function _set_target_text($texts)
	{
		$texts = ! is_array($texts) ? array($texts) : $texts;
		
		$this->target_text = implode("\n", $texts);
	}
}

/* End of file Blacklist.php */