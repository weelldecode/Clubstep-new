const routeModules = [
    {
        pattern:
            /^\/([a-z]{2}(?:_[A-Z]{2})?)?\/checkout\/pay\/renew\/[\w-]+(\/.*)?$/,
        loader: () => import("./frontend/checkout/renew.js"),
    },
    {
        pattern: /^\/([a-z]{2}(?:_[A-Z]{2})?)?\/checkout\/pay\/[\w-]+(\/.*)?$/,
        loader: () => import("./frontend/checkout/form.js"),
    },
    {
        pattern: /^\/([a-z]{2}(?:_[A-Z]{2})?)?\/checkout\/order\/[\w-]+(\/.*)?$/,
        loader: () => import("./frontend/checkout/order.js"),
    },
    {
        // rota de perfil: /xx/profile/slug ou /profile/slug
        pattern: /^\/([a-z]{2}(?:_[A-Z]{2})?)?\/profile\/[\w-]+$/,
        loader: () => import("./frontend/profile.js"),
    },
];

document.addEventListener("DOMContentLoaded", async () => {
    const path = window.location.pathname;

    const matchedRoute = routeModules.find((route) => route.pattern.test(path));
    if (matchedRoute) {
        try {
            const module = await matchedRoute.loader();
            if (typeof module.init === "function") {
                module.init(notyf, animate, scroll, Masonry);
            }
        } catch (error) {
            console.error("Erro ao carregar módulo para a rota:", path, error);
        }
    }
});
