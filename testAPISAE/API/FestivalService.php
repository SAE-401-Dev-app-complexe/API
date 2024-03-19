<?php

class FestivalService {

    public static function getFestival($pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM festival");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}