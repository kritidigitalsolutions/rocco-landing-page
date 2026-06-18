<?php
require_once __DIR__ . '/includes/db.php';

// 1. Add icon_class column to popular_categories (if not exists)
try {
    $pdo->exec("ALTER TABLE popular_categories ADD COLUMN icon_class VARCHAR(100) DEFAULT '' AFTER bg_image");
    echo "Added icon_class column.\n";
} catch (Exception $e) {
    echo "icon_class column already exists.\n";
}

// 2. Add OTP columns to admin_users (if not exists)
try {
    $pdo->exec("ALTER TABLE admin_users ADD COLUMN reset_otp VARCHAR(10) DEFAULT NULL");
    echo "Added reset_otp column.\n";
} catch (Exception $e) {
    echo "reset_otp column already exists.\n";
}
try {
    $pdo->exec("ALTER TABLE admin_users ADD COLUMN reset_otp_expires TIMESTAMP NULL DEFAULT NULL");
    echo "Added reset_otp_expires column.\n";
} catch (Exception $e) {
    echo "reset_otp_expires column already exists.\n";
}

// 3. Delete "Popular Categories" from the general categories table
$deleted = $pdo->exec("DELETE FROM categories WHERE slug = 'popular-categories' OR name = 'Popular Categories'");
echo "Deleted $deleted row(s) from categories.\n";

// 4. Seed popular categories (only if table is empty)
$stmt = $pdo->query("SELECT count(*) FROM popular_categories");
if ($stmt->fetchColumn() == 0) {
    $seedData = [
        ['Action',   '1,200+ titles', '#B11226', 'fas fa-explosion',          1],
        ['Sci-Fi',   '850+ titles',   '#D4AF37', 'fas fa-robot',              2],
        ['Thriller', '960+ titles',   '#E63946', 'fas fa-user-secret',        3],
        ['Romance',  '700+ titles',   '#F5D27A', 'fas fa-heart',              4],
        ['Comedy',   '1,100+ titles', '#A67C00', 'fas fa-face-laugh-squint',  5],
        ['Drama',    '1,500+ titles', '#7A0C18', 'fas fa-masks-theater',      6],
        ['Horror',   '650+ titles',   '#B11226', 'fas fa-ghost',              7],
        ['Fantasy',  '500+ titles',   '#D4AF37', 'fas fa-wand-magic-sparkles',8]
    ];
    $insert = $pdo->prepare("INSERT INTO popular_categories (name, titles_count, card_color, icon_class, display_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($seedData as $data) {
        $insert->execute($data);
    }
    echo "Seeded " . count($seedData) . " popular categories.\n";
} else {
    echo "Popular categories already exist, skipping seed.\n";
}

echo "\n✅ Migration complete.\n";
