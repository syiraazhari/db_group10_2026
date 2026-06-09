<?php
require_once __DIR__ . '/config.php';

function h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function get_room_type_name(PDO $pdo, int $roomTypeId): string {
    $stmt = $pdo->prepare("SELECT TypeName FROM ROOM_TYPE WHERE RoomTypeID = ?");
    $stmt->execute([$roomTypeId]);
    $row = $stmt->fetch();
    return $row['TypeName'] ?? 'Unknown';
}
?>