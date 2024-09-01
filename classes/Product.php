<?php

namespace classes;
use PDO;

abstract class Product
{
    protected $sku;
    protected $name;
    protected $price;
    protected $type;
    protected $pdo;

    public function __construct($sku, $name, $price, $type)
    {
        $this->setSKU($sku);
        $this->setName($name);
        $this->setPrice($price);
        $this->setType($type);

        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function saveToDatabase(): void
    {
        if ($this->skuExists($this->getSKU())) {
            throw new \Exception("Product with SKU '{$this->getSKU()}' already exists.");
        }

        $this->insertIntoDatabase();
    }

    abstract protected function insertIntoDatabase();

    protected function skuExists($sku): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM products WHERE sku = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getSKU()
    {
        return $this->sku;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setSKU($sku): void
    {
        $this->sku = $sku;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
    public function setPrice($price): void
    {
        $this->price = $price;
    }
    public function setType($type): void
    {
        $this->type = $type;
    }

    public static function getAllProducts(): array
    {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id");

        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = [];

        foreach ($productsData as $data) {
            $type = "classes\\" . $data['type'];

            if (class_exists($type)) {
                $product = new $type($data);
                $products[] = $product;
            } else {
                throw new \Exception("Class not found: $type");
            }
        }

        return $products;
    }

    public static function deleteProductsBySkus(array $skus): bool
    {
        if (empty($skus)) {
            return false;
        }

        try {
            $db = new Database();
            $pdo = $db->getConnection();

            $inQuery = implode(',', array_fill(0, count($skus), '?'));
            $stmt = $pdo->prepare("DELETE FROM products WHERE sku IN ($inQuery)");

            $stmt->execute($skus);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
