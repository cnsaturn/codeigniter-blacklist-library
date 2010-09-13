<?php

/**
* Blacklist dictionary
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

// Forbidden IP adresses
$config['ip_addresses'] = array('10.55.66.*');

// Forbidden keywords
$config['words'] = array('GFW', 'FUCK', '群体灭绝', '红色恐怖', '迷魂药', '代开发票');

// Regexs (aka. regular expression) patterns
// It should be PERL style!!
$config['regexs'] = array();

/* End of file blacklist.php */
/* Location: ./system/application/config/blacklist.php */