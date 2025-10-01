<?php

// returns the absolute path to /data/s####/dom####/
function pagely_root_dir()
{
    // if we're not running via the cli, the path is always /
    if (!(defined('WP_CLI') && WP_CLI))
        return '/';

    $search = ['/mnt', '/etc', '/httpdocs'];
    $path = dirname(__DIR__, 4);
    for ($i=0; $i<3; $i++)
    {
        $found = true;
        foreach($search as $dir)
        {
            if (!file_exists($path . $dir))
                $found = false;
        }
        if ($found)
            return realpath($path);

        $path = dirname($path);
    }
    return false;
}
