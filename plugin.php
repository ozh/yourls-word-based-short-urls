<?php
/*
Plugin Name: Word Based Short URLs
Plugin URI: http://ozh.org/
Description: Short URLs like <tt>http://sho.rt/ExcellentSneakyUnicorn</tt>
Version: 1.0
Author: Ozh
Author URI: http://ozh.org/
*/

/********** Edit this if you want **************/

// how many words in the shorturl ? The first ones will be adjectives, the last one will be a noun
define('OZH_WBSU_NUMBER_OF_WORDS', 3);

// adjective list
define('OZH_WBSU_ADJECTIVE_LIST', __DIR__.'/adjectives.txt');

// noun list
define('OZH_WBSU_NOUN_LIST', __DIR__.'/nouns.txt');

/**
 *  These adjective and noun lists courtesy of https://github.com/hugsy/stuff/tree/master/random-word
 */

/********** No touching further **************/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Generate a random AjdectiveAdjectiveNoun
yourls_add_filter('random_keyword', 'ozh_wbsu_random_keyword');
function ozh_wbsu_random_keyword() {

    $first_adjective  = ucfirst( ozh_wbsu_get_random_word_from_file(OZH_WBSU_ADJECTIVE_LIST) );
    $second_adjective = ucfirst( ozh_wbsu_get_random_word_from_file(OZH_WBSU_ADJECTIVE_LIST) );
    $noun             = ucfirst( ozh_wbsu_get_random_word_from_file(OZH_WBSU_NOUN_LIST) );

    return $first_adjective.$second_adjective.$noun;
}

// Don't increment sequential keyword tracker
yourls_add_filter('get_next_decimal', 'ozh_wbsu_keyword_next_decimal');
function ozh_wbsu_keyword_next_decimal($next) {
    return ($next - 1);
}

// Append lowercase and uppercase letters to the currently used charset
yourls_add_filter('get_shorturl_charset', 'ozh_wbsu_charset');
function ozh_wbsu_charset($charset) {
    return $charset.'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
}

/**
 *  Read random line from file
 *
 *  @param $file_to_read    path of file to read
 *  @return string          random line from file, trimmed of \n
 */
function ozh_wbsu_get_random_word_from_file($file_to_read) {
    static $num_of_lines = array();

    $file = new \SplFileObject($file_to_read, 'r');

    // if we haven't already counted the number of lines, count them
    if (!isset($num_of_lines[$file_to_read])) {
        $num_of_lines[$file_to_read] = ozh_wbsu_get_number_of_lines($file_to_read);
    }
    $file->seek( mt_rand(0,$num_of_lines[$file_to_read]) );

    return (trim($file->fgets()));
}

/**
 *  Get total number of lines from file
 *
 *  @param $file_to_read    path of file to read
 *  @return integer         number of lines
 */
function ozh_wbsu_get_number_of_lines($file_to_read) {
    $file = new \SplFileObject($file_to_read, 'r');
    $file->seek(PHP_INT_MAX);
    return ($file->key() + 1);
}
