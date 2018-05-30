<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @version $Id: Table.class.php 11353 2008-06-27 14:27:18Z lem9 $
 */

/**
 *
 */
if(!defined("PMA_MYSQL_INT_VERSION")) define("PMA_MYSQL_INT_VERSION",50037);

require_once('common/lib/PMA_common.lib.php');

class PMA_Table {

    /**
     * generates column/field specification for ALTER or CREATE TABLE syntax
     *
     * @todo    move into class PMA_Column
     * @todo on the interface, some js to clear the default value when the default
     * current_timestamp is checked
     * @static
     * @param   string  $name       name
     * @param   string  $type       type ('INT', 'VARCHAR', 'BIT', ...)
     * @param   string  $length     length ('2', '5,2', '', ...)
     * @param   string  $attribute
     * @param   string  $collation
     * @param   string  $null       with 'NULL' or 'NOT NULL'
     * @param   string  $default    default value
     * @param   boolean $default_current_timestamp  whether default value is
     *                                              CURRENT_TIMESTAMP or not
     *                                              this overrides $default value
     * @param   string  $extra      'AUTO_INCREMENT'
     * @param   string  $comment    field comment
     * @param   array   &$field_primary list of fields for PRIMARY KEY
     * @param   string  $index
     * @param   string  $default_orig
     * @return  string  field specification
     */
    function generateFieldSpec($name, $type, $length = '', $attribute = '',
        $collation = '', $null = false, $default = '',
        $default_current_timestamp = false, $extra = '', $comment = '',
        &$field_primary, $index, $default_orig = false)
    {

        $is_timestamp = strpos(' ' . strtoupper($type), 'TIMESTAMP') == 1;

        // $default_current_timestamp has priority over $default

        /**
         * @todo include db-name
         */
        $query = PMA_backquote($name) . ' ' . $type;

        if ($length != ''
            && !preg_match('@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)$@i', $type)) {
            $query .= '(' . $length . ')';
        }

        if ($attribute != '') {
            $query .= ' ' . $attribute;
        }

        if (PMA_MYSQL_INT_VERSION >= 40100 && !empty($collation)
          && $collation != 'NULL'
          && preg_match('@^(TINYTEXT|TEXT|MEDIUMTEXT|LONGTEXT|VARCHAR|CHAR|ENUM|SET)$@i', $type)) {
            $query .= PMA_generateCharsetQueryPart($collation);
        }

        if ($null !== false) {
            if (!empty($null)) {
                $query .= ' NOT NULL';
            } else {
                $query .= ' NULL';
            }
        }

        if ($default_current_timestamp && $is_timestamp) {
            $query .= ' DEFAULT CURRENT_TIMESTAMP';
        // auto_increment field cannot have a default value
        } elseif ($extra !== 'AUTO_INCREMENT'
          && (strlen($default) || $default != $default_orig)) {
            if (strtoupper($default) == 'NULL') {
                $query .= ' DEFAULT NULL';
            } else {
                if (strlen($default)) {
                    if ($is_timestamp && $default == '0') {
                        // a TIMESTAMP does not accept DEFAULT '0'
                        // but DEFAULT 0  works
                        $query .= ' DEFAULT ' . PMA_sqlAddslashes($default);
                    } elseif ($default && $type == 'BIT') {
                        $query .= ' DEFAULT b\'' . preg_replace('/[^01]/', '0', $default) . '\'';
                    } else {
                        $query .= ' DEFAULT \'' . PMA_sqlAddslashes($default) . '\'';
                    }
                }
            }
        }

        if (!empty($extra)) {
            $query .= ' ' . $extra;
            // Force an auto_increment field to be part of the primary key
            // even if user did not tick the PK box;
        }
        if (PMA_MYSQL_INT_VERSION >= 40100 && !empty($comment)) {
            $query .= " COMMENT '" . PMA_sqlAddslashes($comment) . "'";
        }
        return $query;
    } // end function
}
