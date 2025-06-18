<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\Status;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;


    #[ORM\Column(type: 'string', enumType: Status::class)]
    private status $status;

    #[ORM\Column]
    private ?int $stockSold = null;

    #[ORM\Column]
    private ?int $stockAvailable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getStockSold(): ?int
    {
        return $this->stockSold;
    }

    public function setStockSold(int $stockSold): static
    {
        $this->stockSold = $stockSold;

        return $this;
    }

    public function getStockAvailable(): ?int
    {
        return $this->stockAvailable;
    }

    public function setStockAvailable(int $stockAvailable): static
    {
        $this->stockAvailable = $stockAvailable;

        return $this;
    }

}
