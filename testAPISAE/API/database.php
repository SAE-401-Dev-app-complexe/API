<?php

function getPDO(): PDO
{
    $host = 'localhost';
    $dbName = 'festiplanbfgi_sae';
    $dbCharset = 'utf8mb4';
    $dbPort = '3306';
    $user = 'root';
    $pass = 'root';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $dsn = "mysql:host=$host;dbname=$dbName;charset=$dbCharset;port=$dbPort";

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new PDOException('Database connection failed');
    }
}

function existsArticle($codeBarre)
{
    $pdo = getPDO();
    if ($pdo == null) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM stockprix WHERE CODE_BARRE = ?;");

    $stmt->execute([$codeBarre]);
    return $stmt->fetchColumn() > 0;
}

function modifierPrixStock($codeBarre, $data)
{
    $pdo = getPDO();
    if ($pdo == null) {
        return null;
    }

    $prix = isset($data['prix'])
        ? htmlspecialchars($data['prix'])
        : null;
    
    $stock = isset($data['stock'])
        ? htmlspecialchars($data['stock'])
        : null;

    if (!$prix || !is_numeric($prix) || $prix <= 0)
    {
        throw new Exception('Price is not specified, not a number, or not positive');
    }
    
    if (!$stock || !ctype_digit((string) $stock) || $stock <= 0)
    {
        throw new Exception('Stock is not specified, not an integer, or not positive');
    }

    $stmt = $pdo->prepare("UPDATE stockprix SET PRIX = ?, STOCK = ? WHERE CODE_BARRE = ?;");

    $stmt->execute([$prix, $stock, $codeBarre]);

    return ['message' => 'Article updated'];
}