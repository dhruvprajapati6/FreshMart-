<?php
$files = array_merge(glob('c:/Users/YOGESH/OneDrive/Desktop/Fresh-Mart/*.html'), glob('c:/Users/YOGESH/OneDrive/Desktop/Fresh-Mart/*.php'));

$cat_pattern = '/<li class="nav-item dropdown">\s*<a class="nav-link dropdown-toggle"[^>]*>\s*Categories\s*<\/a>\s*<ul class="dropdown-menu[^>]*>.*?<\/ul>\s*<\/li>/is';

$off_pattern = '/<li class="nav-item">\s*<a class="nav-link text-secondary-custom fw-bold" href="shop\.html"><i class="bi bi-tag-fill me-1"><\/i>\s*Offers<\/a>\s*<\/li>/is';

foreach($files as $f) {
    if (basename($f) === 'fix_nav.php' || basename($f) === 'get_products.php' || basename($f) === 'db_connect.php') continue;
    $content = file_get_contents($f);
    
    $modified = preg_replace($cat_pattern, '', $content);
    $modified = preg_replace($off_pattern, '', $modified);
    
    if ($content !== $modified) {
        file_put_contents($f, $modified);
        echo "Updated: " . basename($f) . "\n";
    }
}
echo "Done!\n";
?>
