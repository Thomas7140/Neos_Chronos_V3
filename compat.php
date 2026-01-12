<?php
// PHP 8 compatibility shims for legacy mysql_* and ereg_replace usage.

if (!defined('MYSQL_ASSOC')) {
    define('MYSQL_ASSOC', MYSQLI_ASSOC);
}
if (!defined('MYSQL_NUM')) {
    define('MYSQL_NUM', MYSQLI_NUM);
}
if (!defined('MYSQL_BOTH')) {
    define('MYSQL_BOTH', MYSQLI_BOTH);
}

if (!function_exists('mysql_connect')) {
    $GLOBALS['_mysql_compat_link'] = null;

    function _mysql_compat_get_link($link = null)
    {
        if ($link instanceof mysqli) {
            return $link;
        }
        if ($link === null && isset($GLOBALS['_mysql_compat_link']) && $GLOBALS['_mysql_compat_link'] instanceof mysqli) {
            return $GLOBALS['_mysql_compat_link'];
        }
        return null;
    }

    function mysql_connect($host = null, $username = null, $password = null)
    {
        $link = @mysqli_connect($host, $username, $password);
        if ($link instanceof mysqli) {
            $GLOBALS['_mysql_compat_link'] = $link;
        }
        return $link;
    }

    function mysql_pconnect($host = null, $username = null, $password = null)
    {
        if ($host !== null && strpos($host, 'p:') !== 0) {
            $host = 'p:' . $host;
        }
        return mysql_connect($host, $username, $password);
    }

    function mysql_select_db($dbname, $link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return false;
        }
        return mysqli_select_db($link, $dbname);
    }

    function mysql_query($query, $link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return false;
        }
        return mysqli_query($link, $query);
    }

    function mysql_num_rows($result)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        return mysqli_num_rows($result);
    }

    function mysql_fetch_array($result, $result_type = MYSQL_BOTH)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        return mysqli_fetch_array($result, $result_type);
    }

    function mysql_fetch_assoc($result)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        return mysqli_fetch_assoc($result);
    }

    function mysql_fetch_row($result)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        return mysqli_fetch_row($result);
    }

    function mysql_num_fields($result)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        return mysqli_num_fields($result);
    }

    function mysql_field_name($result, $field_offset)
    {
        if (!($result instanceof mysqli_result)) {
            return false;
        }
        $field = mysqli_fetch_field_direct($result, $field_offset);
        if (!$field) {
            return false;
        }
        return $field->name;
    }

    function mysql_real_escape_string($string, $link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return addslashes($string);
        }
        return mysqli_real_escape_string($link, $string);
    }

    function mysql_insert_id($link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return false;
        }
        return mysqli_insert_id($link);
    }

    function mysql_affected_rows($link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return false;
        }
        return mysqli_affected_rows($link);
    }

    function mysql_error($link = null)
    {
        $link = _mysql_compat_get_link($link);
        if (!$link) {
            return '';
        }
        return mysqli_error($link);
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern, $replacement, $string)
    {
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        return preg_replace($delimiter . $escaped . $delimiter, $replacement, $string);
    }
}
