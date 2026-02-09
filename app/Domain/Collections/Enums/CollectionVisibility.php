<?php

namespace App\Domain\Collections\Enums;

enum CollectionVisibility: string
{
    case Public = "public";
    case Unlisted = "unlisted";
    case Private = "private";
}
