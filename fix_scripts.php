<?php
$v = time();
$files = glob('c:/Users/YOGESH/OneDrive/Desktop/Fresh-Mart/*.html');
foreach($files as $f) {
    $c = file_get_contents($f);
    // Find where both scripts are
    if (strpos($c, 'js/main.js') !== false && strpos($c, 'js/products.js') !== false) {
        // Just remove products.js completely first
        $c = preg_replace('/<script src="js\/products\.js[^>]*><\/script>/s', '', $c);
        
        // Then insert it immediately BEFORE main.js
        $c = str_replace('<script src="js/main.js"></script>', '<script src="js/products.js?v=' . $v . '"></script>' . "\n    " . '<script src="js/main.js"></script>', $c);
        file_put_contents($f, $c);
    }
}
echo "Fixed script order in all HTML files.\n";
?>
