# Blacklist

Version 1.0

* Author: [Yang Hu](http://www.cnsaturn.com/), aka. Saturn

Original thoughts borrowed from a framework called [egp](http://code.google.com/p/egp/) .

## DESCRIPTION

This is a simple blacklist library written for CodeIgniter 1.7.x and the to-be-released CI 2.0. 

If you are googling a solution ONLY for preventing your app from spam blood, I STRONGLY recommend you using Wordpress Akismet service instead of this library. However, if you'd like to take full control of your blacklist dictionary, i.e., your application requires Internet censorship capabilities, you can consider this library.

## FEATURES

1. IP-address filtering.  
2. Keyword-based string filtering
3. Regex-based string filtering

## INSTALLATION

####  CodeIgniter

1.  Put Blacklist.php into your application/libraries folder
2.  Put blacklist.php into your application/config folder
3.  Load it like normal: $this->load->library('blacklist'); OR you can autoload it.

####  For other framework or app

Dive into the code, you can find that it can be easily migrated to other apps without making hands dirty.

## Usage

1. Check if the given text block contains forbidden keywords:

// return TRUE if it does exist, otherwise, return FALSE.
$this->blacklist->check_text('I am a spammer!')->is_blocked();

2. Check if the client IP is blocked by the system
// return TRUE if it is blocked
$this->blacklist->check_ip('1.1.1.1')->is_blocked();

3. If a forbidden keyword (such as word 'spammer') was found in the given text block, we can do the following replacement:

$this->blacklist->replace('I am a spammer!', '@'); 

After this, The original text will be turned into 'I am a @@@@@@@!'