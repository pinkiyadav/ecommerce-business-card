<?php

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getProduct(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM products
            WHERE id = ?
            AND status = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $product = $stmt->fetch();

        return $product ?: null;
    }

    public function getOptions(int $productId, string $type): array
    {
        $sql = "
            SELECT *
            FROM product_options
            WHERE product_id = ?
            AND option_type = ?
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $type]);

        return $stmt->fetchAll();
    }
}