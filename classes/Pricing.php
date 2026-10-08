<?php

class Pricing
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function calculatePrice(
        int $productId,
        int $quantity,
        string $paperType
    ): ?float {

        $sql = "
            SELECT price
            FROM pricing
            WHERE product_id = ?
            AND quantity = ?
            AND paper_type = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $productId,
            $quantity,
            $paperType
        ]);

        $result = $stmt->fetch();

        return $result ? (float) $result['price'] : null;
    }
}