<?php
/**
 * One-time refresh: new room titles/descriptions + new gallery images from Unsplash.
 * Run: php tools/refresh_room_demo.php
 */

require __DIR__ . '/../admin/inc/db_config.php';
require __DIR__ . '/../admin/inc/essentials.php';

$projectRoot = realpath(__DIR__ . '/..');
$roomsDir = $projectRoot . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'rooms' . DIRECTORY_SEPARATOR;
if (!is_dir($roomsDir)) {
    mkdir($roomsDir, 0755, true);
}

function download_file($url, $dest)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        $fp = fopen($dest, 'wb');
        if (!$fp) {
            return false;
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BookEaseRoomRefresh/1.0');
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        return $code === 200 && filesize($dest) > 1000;
    }
    $data = @file_get_contents($url);
    if ($data === false || strlen($data) < 1000) {
        return false;
    }
    return file_put_contents($dest, $data) !== false;
}

$rooms = [
    8 => [
        'name' => 'Deluxe Garden Room',
        'description' => 'Bright room with garden views, queen bed, work desk, and rainfall shower. Ideal for couples or short business stays.',
        'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1400&q=82',
        'file' => 'ROOM_8_deluxe.jpg',
    ],
    9 => [
        'name' => 'Executive City View',
        'description' => 'Spacious executive room with city skyline views, king bed, seating area, and premium bath amenities.',
        'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1400&q=82',
        'file' => 'ROOM_9_executive.jpg',
    ],
    10 => [
        'name' => 'Premium Family Suite',
        'description' => 'Large suite with separate lounge, two beds, extra storage, and family-friendly layout—perfect for longer stays.',
        'image_url' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1400&q=82',
        'file' => 'ROOM_10_suite.jpg',
    ],
];

foreach ($rooms as $roomId => $cfg) {
    $path = $roomsDir . $cfg['file'];
    echo "Downloading room $roomId...\n";
    if (!download_file($cfg['image_url'], $path)) {
        fwrite(STDERR, "Failed to download image for room $roomId\n");
        continue;
    }

    $name = mysqli_real_escape_string($con, $cfg['name']);
    $desc = mysqli_real_escape_string($con, $cfg['description']);
    $img = mysqli_real_escape_string($con, $cfg['file']);

    mysqli_query($con, "UPDATE `rooms` SET `name`='$name', `description`='$desc' WHERE `id`=" . (int) $roomId . " AND `removed`=0");

    mysqli_query($con, "UPDATE `room_images` SET `image`='$img' WHERE `room_id`=" . (int) $roomId . " AND `thumb`=1");

    echo "Updated room $roomId -> {$cfg['file']}\n";
}

echo "Done.\n";
