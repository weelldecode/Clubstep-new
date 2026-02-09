<?php

namespace App\Domain\Collections\Enums;

enum CollectionStatus: string
{
    case Draft = "draft";
    case Published = "published";
}
