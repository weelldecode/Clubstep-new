import { loadMercadoPago } from "@mercadopago/sdk-js";

const el = document.getElementById("checkout-data");
if (!el) {
    console.error("checkout-data não encontrado.");
}

const amount = parseFloat(el?.dataset?.planPrice || "0");
const planId = el?.dataset?.planId;
const processUrl = el?.dataset?.processUrl;
const publicKey = el?.dataset?.publicKey;

if (!publicKey) {
    console.error("MP_PUBLIC_KEY ausente no checkout.");
}

await loadMercadoPago();
const mp = new MercadoPago(publicKey, { locale: "pt-BR" });
const bricksBuilder = mp.bricks();

async function switchToStatusScreen() {
    document.getElementById("form-checkout").style.display = "none";
    document.getElementById("status-screen-container").style.display = "block";
}
 
async function renderStatusScreen(paymentId) {
    await bricksBuilder.create("statusScreen", "status-screen-container", {
        initialization: {
            paymentId: paymentId,
        },
        callbacks: {
            onReady: () => {
                console.log("Status Screen carregada");
            },
            onError: (error) => {
                console.error("Erro ao renderizar Status Screen:", error);
            },
        },
    });
}

await bricksBuilder.create("payment", "form-checkout", {
    initialization: {
        amount,
        paymentMethods: {
            excludedPaymentTypes: [],
            excludedPaymentMethods: [],
        },
    },
    customization: {
        paymentMethods: {
            ticket: "all",
            bankTransfer: "all",
            creditCard: "all",
        },
    },
    callbacks: {
        onReady: () => console.log("Brick Payment pronto"),
        onSubmit: async (formData) => {
            try {
                const csrfToken = document
                    .querySelector('meta[name="csrf_token"]')
                    ?.getAttribute("content");

                if (!processUrl) {
                    throw new Error("processUrl ausente.");
                }

                const res = await fetch(processUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken || "",
                    },
                    credentials: "same-origin", // <== ESSENCIAL para enviar cookies de sessão!
                    body: JSON.stringify({
                        ...formData,
                        plan_id: planId,
                    }),
                });

                let result = {};
                try {
                    result = await res.json();
                } catch (e) {
                    result = { message: "Resposta inválida do servidor." };
                }

                if (!res.ok) {
                    throw new Error(result.message || "Erro ao processar pagamento.");
                }

                if (result.payment_id) {
                    await switchToStatusScreen();
                    await renderStatusScreen(result.payment_id);

                    if (result.status === "approved") {
                        let countdown = 30;
                        const statusContainer = document.getElementById(
                            "status-screen-container",
                        );

                        const countdownEl = document.createElement("p");
                        countdownEl.className =
                            "my-6 text-lg font-semibold tracking-wide text-center";
                        statusContainer?.appendChild(countdownEl);

                        const interval = setInterval(() => {
                            if (countdownEl) {
                                countdownEl.textContent = `Você será redirecionado em ${countdown} segundos...`;
                            }
                            countdown--;

                            if (countdown < 0) {
                                clearInterval(interval);
                                if (result.redirect_url) {
                                    window.location.href = result.redirect_url;
                                }
                            }
                        }, 1000);
                    }
                } else {
                    alert(result.message || "Erro ao processar pagamento.");
                }
            } catch (error) {
                console.error("Erro no checkout:", error);
                alert(error?.message || "Erro ao processar pagamento.");
            }
        },
        onError: (error) => console.error("Erro:", error),
    },
});
