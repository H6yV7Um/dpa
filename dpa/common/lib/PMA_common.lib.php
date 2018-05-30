<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Misc functions used all over the scripts.
 *
 * @version $Id: common.lib.php 11321 2008-06-13 16:26:21Z lem9 $
 */

if(!defined("PMA_MYSQL_INT_VERSION")) define("PMA_MYSQL_INT_VERSION",50037);

/**
 * Add slashes before "'" and "\" characters so a value containing them can
 * be used in a sql comparison.
 *
 * @uses    str_replace()
 * @param   string   the string to slash
 * @param   boolean  whether the string will be used in a 'LIKE' clause
 *                   (it then requires two more escaped sequences) or not
 * @param   boolean  whether to treat cr/lfs as escape-worthy entities
 *                   (converts \n to \\n, \r to \\r)
 *
 * @param   boolean  whether this function is used as part of the
 *                   "Create PHP code" dialog
 *
 * @return  string   the slashed string
 *
 * @access  public
 */
function PMA_sqlAddslashes($a_string = '', $is_like = false, $crlf = false, $php_code = false)
{
    if ($is_like) {
        $a_string = str_replace('\\', '\\\\\\\\', $a_string);
    } else {
        $a_string = str_replace('\\', '\\\\', $a_string);
    }

    if ($crlf) {
        $a_string = str_replace("\n", '\n', $a_string);
        $a_string = str_replace("\r", '\r', $a_string);
        $a_string = str_replace("\t", '\t', $a_string);
    }

    if ($php_code) {
        $a_string = str_replace('\'', '\\\'', $a_string);
    } else {
        $a_string = str_replace('\'', '\'\'', $a_string);
    }

    return $a_string;
} // end of the 'PMA_sqlAddslashes()' function

/**
 * Adds backquotes on both sides of a database, table or field name.
 * and escapes backquotes inside the name with another backquote
 *
 * example:
 * <code>
 * echo PMA_backquote('owner`s db'); // `owner``s db`
 *
 * </code>
 *
 * @uses    PMA_backquote()
 * @uses    is_array()
 * @uses    strlen()
 * @uses    str_replace()
 * @param   mixed    $a_name    the database, table or field name to "backquote"
 *                              or array of it
 * @param   boolean  $do_it     a flag to bypass this function (used by dump
 *                              functions)
 * @return  mixed    the "backquoted" database, table or field name if the
 *                   current MySQL release is >= 3.23.6, the original one
 *                   else
 * @access  public
 */
function PMA_backquote($a_name, $do_it = true)
{
    if (! $do_it) {
        return $a_name;
    }

    if (is_array($a_name)) {
         $result = array();
         foreach ($a_name as $key => $val) {
             $result[$key] = PMA_backquote($val);
         }
         return $result;
    }

    // '0' is also empty for php :-(
    if (strlen($a_name) && $a_name !== '*') {
        return '`' . str_replace('`', '``', $a_name) . '`';
    } else {
        return $a_name;
    }
} // end of the 'PMA_backquote()' function

function PMA_generateCharsetQueryPart($collation) {
        list($charset) = explode('_', $collation);
        return ' CHARACTER SET ' . $charset . ($charset == $collation ? '' : ' COLLATE ' . $collation);
    }
