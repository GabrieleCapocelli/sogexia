<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDto
{
    #[Assert\NotBlank]
    public string $status;

    #[Assert\NotBlank]
    public float $price;

    #[Assert\NotBlank]
    public string $name;
    #[Assert\NotNull]
    public StockDto $stock;

    public ?string $description = null;

}
