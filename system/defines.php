<?php
function u_print_r($subject, $ignore = array(), $depth = 1, $refChain = array()) 
{
    if ($depth > 20) return;
    if (is_object($subject)) {
        foreach ($refChain as $refVal)
            if ($refVal === $subject) {
                echo "*RECURSION*\n";
                return;
            }
        array_push($refChain, $subject);
        echo get_class($subject) . " Object ( \n";
        $subject = (array) $subject;
        foreach ($subject as $key => $val)
            if (is_array($ignore) && !in_array($key, $ignore, 1)) {
                echo str_repeat(" ", $depth * 4) . '[';
                if ($key{0} == "\0") {
                    $keyParts = explode("\0", $key);
                    echo $keyParts[2] . (($keyParts[1] == '*')  ? ':protected' : ':private');
                } else
                    echo $key;
                echo '] => ';
                u_print_r($val, $ignore, $depth + 1, $refChain);
            }
        echo str_repeat(" ", ($depth - 1) * 4) . ")\n";
        array_pop($refChain);
    } elseif (is_array($subject)) {
        echo "Array ( \n";
        foreach ($subject as $key => $val)
            if (is_array($ignore) && !in_array($key, $ignore, 1)) {
                echo str_repeat(" ", $depth * 4) . '[' . $key . '] => ';
                u_print_r($val, $ignore, $depth + 1, $refChain);
            }
        echo str_repeat(" ", ($depth - 1) * 4) . ")\n";
    } else
        echo $subject . "\n";
}
define("WWWROOT", getcwd()."/");
define("DOMAIN", $_SERVER['HTTP_HOST']);
define("URL", $_SERVER['HTTP_HOST'].str_replace("index.php", "", $_SERVER["PHP_SELF"]));
define("SYSTEM_DIR", WWWROOT."system/");
define("DEFAULT_DOMAIN", "default");
define("SHARED_PUBLIC_DIR", WWWROOT."shared/public/");
define("SHARED_STATIC_DIR", WWWROOT."shared/static/");

define("PLUGIN_BEFORE_MODULE", 2);
define("PLUGIN_AFTER_MODULE", 3);
define("PLUGIN_BA_MODULE", 4);

define("AUTOLOAD_BEFORE_MODULE", 0);
define("AUTOLOAD_AFTER_MODULE", 1);

