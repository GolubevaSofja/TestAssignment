<?php

namespace classes;

use ReturnTypeWillChange;

class Furniture extends Product implements \JsonSerializable
{
    protected $height;
    protected $width;
    protected $length;

    public function __construct($data)
    {
        parent::__construct($data['sku'], $data['name'], $data['price'], $data['type']);
        $this->setHeight($data['height']);
        $this->setWidth($data['width']);
        $this->setLength($data['length']);
    }


    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setHeight($height): void
    {
        $this->height = $height;
    }
    public function setWidth($width): void
    {
        $this->width = $width;
    }

    public function setLength($length): void
    {
        $this->length = $length;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'sku' => $this->getSKU(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'type' => $this->getType(),
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'length' => $this->getLength()
        ];
    }

    protected function insertIntoDatabase(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO products (sku, name, price, type, height, width, length)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $this->getSKU(),
            $this->getName(),
            $this->getPrice(),
            $this->getType(),
            $this->getHeight(),
            $this->getWidth(),
            $this->getLength()
        ]);
    }
}
