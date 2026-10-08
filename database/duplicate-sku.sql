SELECT sku, COUNT(*) AS duplicate_count
FROM products
GROUP BY sku
HAVING COUNT(*) > 1;

SELECT *
FROM products
WHERE sku IN (
    SELECT sku
    FROM products
    GROUP BY sku
    HAVING COUNT(*) > 1
)
ORDER BY sku;