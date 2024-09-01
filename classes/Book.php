<?php

namespace classes;

use ReturnTypeWillChange;

class Book extends Product implements \JsonSerializable
{
    protected $weight;

    public function __construct($data)
    {
        parent::__construct($data['sku'], $data['name'], $data['price'], $data['type']);
        $this->setWeight($data['weight']);
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight): void
    {
        $this->weight = $weight;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'sku' => $this->getSKU(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'type' => $this->getType(),
            'weight' => $this->getWeight(),
        ];
    }

    protected function insertIntoDatabase(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (sku, name, price, type, weight) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->getSKU(),
            $this->getName(),
            $this->getPrice(),
            $this->getType(),
            $this->getWeight(),
        ]);
    }
}
