<?php
// Reset OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared successfully!\n";
} else {
    echo "OPcache not available\n";
}

// Also clear any file stat cache
clearstatcache(true);
echo "File stat cache cleared!\n";

// Clear realpath cache
realpath_cache_get();
echo "Realpath cache cleared!\n";

echo "\nDone! Please refresh your browser.";
