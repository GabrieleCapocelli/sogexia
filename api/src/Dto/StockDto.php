<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class StockDto
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public int $available;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public int $sold;
}
