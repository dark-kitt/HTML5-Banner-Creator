<?php
function get_header()
{
    require dirname(__DIR__) . '/templates/partials/header.php';
}
function get_footer()
{
    require dirname(__DIR__) . '/templates/partials/footer.php';
}
function set_dir(bool $set_slash = false)
{
    $index = count(debug_backtrace()) - 1;
    $return = str_replace( substr($_SERVER['DOCUMENT_ROOT'], 0, -1) , '', dirname(debug_backtrace()[$index]['file']) );
    if ($return !== '') {
        $return = set_parent_dir($return, $set_slash);
    }
    return $return;
}
function set_parent_dir(string $dir, bool $set_slash = false)
{
    $dir = preg_replace_callback(
                    '/(?:\/)([\w\d\s\-\.\!\?\@\&\$\§]+)/',
                    function($match) use ($set_slash) {
                        if ($set_slash) {
                            return '/' . str_replace($match[1], '..', $match[1]);
                        } else {
                            return str_replace($match[1], '..', $match[1]) . '/';
                        }
                    },
                    $dir
                );
    return $dir;
}
?>
