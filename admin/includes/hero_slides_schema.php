<?php

function getDefaultHeroSlides()
{
    $slides = [
        'Hero banner/Hollywood hero  banner/wp9875976-hollywood-poster-wallpapers.jpg',
        'Hero banner/Hollywood hero  banner/wp10388053-hollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Hollywood hero  banner/wp14444805-cinema-poster-wallpapers.jpg',
        'Hero banner/Hollywood hero  banner/wp10388016-hollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Hollywood hero  banner/wp15666911-hollywood-posters-wallpapers.jpg',
        'Hero banner/Hollywood hero  banner/wp8525542-film-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807405-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807421-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp4253014-bollywood-movies-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807385-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807390-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807422-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807444-bollywood-movie-poster-wallpapers.jpg',
        'Hero banner/Bollywood hero banner/wp8807445-bollywood-movie-poster-wallpapers.jpg',
    ];

    return array_map(function ($imagePath, $index) {
        return [
            'image_path' => $imagePath,
            'badge_text' => 'Streaming Now - New Releases Every Week',
            'badge_icon' => 'fas fa-satellite-dish',
            'heading' => 'Unlimited Movies, Shows & Originals',
            'paragraph' => 'Dive into thousands of blockbusters, exclusive originals, and binge-worthy series. Stream in stunning 4K on any device, anytime, anywhere.',
            'display_order' => $index + 1,
            'is_active' => 1,
        ];
    }, $slides, array_keys($slides));
}

function ensureHeroSlidesTable(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hero_slides (
          id INT AUTO_INCREMENT PRIMARY KEY,
          image_path VARCHAR(500) NOT NULL,
          badge_text VARCHAR(255) DEFAULT NULL,
          badge_icon VARCHAR(100) DEFAULT 'fas fa-satellite-dish',
          heading VARCHAR(255) NOT NULL,
          paragraph TEXT DEFAULT NULL,
          display_order INT DEFAULT 0,
          is_active TINYINT(1) DEFAULT 1,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $count = (int) $pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
    if ($count > 0) {
        return;
    }

    $insert = $pdo->prepare("
        INSERT INTO hero_slides
          (image_path, badge_text, badge_icon, heading, paragraph, display_order, is_active)
        VALUES
          (:image_path, :badge_text, :badge_icon, :heading, :paragraph, :display_order, :is_active)
    ");

    foreach (getDefaultHeroSlides() as $slide) {
        $insert->execute($slide);
    }
}
