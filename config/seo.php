<?php

return [
    "site_name" => env("SEO_SITE_NAME", env("APP_NAME", "ClubStep")),
    "description" => env(
        "SEO_DESCRIPTION",
        "Sites, collections, and arts to inspire your next project.",
    ),
    "image" => env("SEO_IMAGE", "/assets/img/icone.webp"),
    "robots" => env("SEO_ROBOTS", "index, follow"),
];
