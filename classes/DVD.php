<?php
// DVD.php
namespace classes;

use ReturnTypeWillChange;

class DVD extends Product implements \JsonSerializable
{
    protected $size;

    public function __construct($data) {
        parent::__construct($data['sku'], $data['name'], $data['price'], $data['type']);
        $this->setSize($data['size']);
    }

    public function getSize() { return $this->size; }

    public function setSize($size): void { $this->size = $size; }

    #[ReturnTypeWillChange] public function jsonSerialize(): array
    {
        return [
            'sku' => $this->getSKU(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'type' => $this->getType(),
            'size' => $this->getSize(),
        ];
    }

    protected function insertIntoDatabase(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (sku, name, price, type, size) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->getSKU(),
            $this->getName(),
            $this->getPrice(),
            $this->getType(),
            $this->getSize()
        ]);
    }

}
