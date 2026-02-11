@php
    $routeName = request()->route()?->getName() ?? "";
    $siteName = config("seo.site_name", config("app.name"));
    $defaultDescription = config(
        "seo.description",
        t("Sites, collections, and arts to inspire your next project."),
    );
    $defaultImage = config("seo.image", "/assets/img/icone.webp");
    $defaultImageUrl = str_starts_with($defaultImage, "http")
        ? $defaultImage
        : url($defaultImage);

    $routeTitle = match (true) {
        str_starts_with($routeName, "home") => t("Home"),
        str_starts_with($routeName, "plans") => t("Plans"),
        str_starts_with($routeName, "collection.index") => t("Collections"),
        str_starts_with($routeName, "collection.show") => t("Collection"),
        str_starts_with($routeName, "collection.tag") => t("Collections by Tag"),
        str_starts_with($routeName, "collection.category") => t("Collections by Category"),
        str_starts_with($routeName, "profile.user") => t("Profile"),
        str_starts_with($routeName, "login") => t("Sign In"),
        str_starts_with($routeName, "register") => t("Create Account"),
        str_starts_with($routeName, "password.request") => t("Forgot Password"),
        str_starts_with($routeName, "password.reset") => t("Reset Password"),
        default => null,
    };

    $routeDescription = match (true) {
        str_starts_with($routeName, "home") => t("Explore curated collections, creative assets, and premium downloads."),
        str_starts_with($routeName, "plans") => t("Choose a plan and unlock unlimited downloads on ClubStep."),
        str_starts_with($routeName, "collection.index") => t("Browse all collections and find the perfect resources."),
        str_starts_with($routeName, "collection.tag") => t("Browse collections filtered by tag."),
        str_starts_with($routeName, "collection.category") => t("Browse collections filtered by category."),
        str_starts_with($routeName, "profile.user") => t("See the latest collections from this creator."),
        default => null,
    };

    $seoTitle = $seoTitle ?? $title ?? $routeTitle ?? $siteName;
    $seoDescription = $seoDescription ?? $description ?? $routeDescription ?? $defaultDescription;
    $seoImage = $seoImage ?? $defaultImageUrl;
    $seoType = $seoType ?? "website";
    $canonical = $canonical ?? url()->current();
    $siteUrl = url("/");
    $fullTitle = $seoTitle ? $seoTitle . " - " . $siteName : $siteName;

    $robots = $robots ?? config("seo.robots", "index, follow");
    $noindexRoutes = [
        "login",
        "register",
        "password.request",
        "password.reset",
        "verification.notice",
        "password.confirm",
        "billing",
        "wishlist.index",
        "cart.index",
        "download",
    ];
    if (str_starts_with($routeName, "admin.") ||
        str_starts_with($routeName, "studio.") ||
        str_starts_with($routeName, "checkout.") ||
        str_starts_with($routeName, "settings.") ||
        in_array($routeName, $noindexRoutes, true)) {
        $robots = "noindex, nofollow";
    }

    $schema = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "Organization",
                "name" => $siteName,
                "url" => $siteUrl,
                "logo" => $seoImage,
            ],
            [
                "@type" => "WebSite",
                "name" => $siteName,
                "url" => $siteUrl,
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => url("/collection") . "?q={search_term_string}",
                    "query-input" => "required name=search_term_string",
                ],
            ],
        ],
    ];
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $seoDescription }}" />
<meta name="robots" content="{{ $robots }}" />

<link rel="canonical" href="{{ $canonical }}" />

<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:title" content="{{ $fullTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta property="og:type" content="{{ $seoType }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:image:alt" content="{{ $seoTitle }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $fullTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />

<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="csrf_token" content="{{ csrf_token() }}" />
<link rel="icon" href="/assets/img/icone.webp" type="image/webp">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@stack("seo")
@vite(["resources/css/app.css", "resources/js/app.js"])
@livewireStyles
@fluxAppearance
@wireUiScripts
