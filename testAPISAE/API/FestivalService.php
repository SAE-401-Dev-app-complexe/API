<?php

class FestivalService {

    public static function getFestival($pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM festival WHERE dateDebut > NOW() ORDER BY dateDebut ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}