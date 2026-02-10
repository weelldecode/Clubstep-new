const bannerInput = document.getElementById("bannerInput");
const bannerCanvas = document.getElementById("bannerCanvas");
const gridOverlay = document.getElementById("gridOverlay");
const bannerDisplay = document.getElementById("bannerDisplay");
const profileAvatarImage = document.getElementById("profileAvatarImage");
const saveBtn = document.getElementById("saveBannerBtn");
const resetBtn = document.getElementById("resetBannerBtn");
const ctx = bannerCanvas.getContext("2d");
const gridCtx = gridOverlay.getContext("2d");

let hasChanged = false; // controla se houve alteração

let bannerImg = null;
let bannerPos = { x: 0, y: 0 };
let dragging = false;
let startX = 0;
let startY = 0;

// --------------------
// Função para marcar alteração e habilitar botão
// --------------------
function enableSave() {
    hasChanged = true;
    saveBtn.disabled = false;
    saveBtn.classList.remove("opacity-50", "cursor-not-allowed");
}
// --------------------
// Função para resetar posição
// --------------------
window.resetBannerCrop = function () {
    if (!bannerImg) return;

    // centraliza de novo
    const scale = Math.max(
        bannerCanvas.width / bannerImg.width,
        bannerCanvas.height / bannerImg.height,
    );
    const imgWidth = bannerImg.width * scale;
    const imgHeight = bannerImg.height * scale;

    bannerPos = {
        x: (bannerCanvas.width - imgWidth) / 2,
        y: (bannerCanvas.height - imgHeight) / 2,
    };

    drawBanner();
    enableSave();
};
// --------------------
// Função para desenhar imagem no canvas
// --------------------
function drawBanner() {
    if (!bannerImg) return;

    ctx.clearRect(0, 0, bannerCanvas.width, bannerCanvas.height);

    const scale = Math.max(
        bannerCanvas.width / bannerImg.width,
        bannerCanvas.height / bannerImg.height,
    );

    const imgWidth = bannerImg.width * scale;
    const imgHeight = bannerImg.height * scale;

    const x = bannerPos.x ?? (bannerCanvas.width - imgWidth) / 2;
    const y = bannerPos.y ?? (bannerCanvas.height - imgHeight) / 2;

    ctx.drawImage(bannerImg, x, y, imgWidth, imgHeight);

    // Atualiza overlay da grade
    drawGridOverlay();
}

// --------------------
// Função para desenhar grid no overlay
// --------------------
function drawGridOverlay() {
    gridCtx.clearRect(0, 0, gridOverlay.width, gridOverlay.height);

    const cols = 3;
    const rows = 3;
    const stepX = gridOverlay.width / cols;
    const stepY = gridOverlay.height / rows;

    gridCtx.strokeStyle = "rgba(255, 255, 255, 0.6)";
    gridCtx.lineWidth = 1;

    for (let i = 1; i < cols; i++) {
        gridCtx.beginPath();
        gridCtx.moveTo(i * stepX, 0);
        gridCtx.lineTo(i * stepX, gridOverlay.height);
        gridCtx.stroke();
    }

    for (let i = 1; i < rows; i++) {
        gridCtx.beginPath();
        gridCtx.moveTo(0, i * stepY);
        gridCtx.lineTo(gridOverlay.width, i * stepY);
        gridCtx.stroke();
    }
}

function uploadBannerFile(file) {
    const wireId = document
        .getElementById("bannerComponent")
        .closest("[wire\\:id]")
        .getAttribute("wire:id");

    const component = Livewire.find(wireId);

    component.upload(
        "bannerTemp",
        file,
        () => {
            console.log("✅ Upload concluído");
            bannerCanvas.classList.add("hidden");
            gridOverlay.classList.add("hidden");
            saveBtn.classList.add("hidden");
            resetBtn.classList.add("hidden");
            bannerDisplay.classList.remove("hidden");
        },
        (error) => console.error("❌ Erro:", error),
    );
}

// --------------------
// Carregar nova imagem (reset automático)
// --------------------
bannerInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (file.type === "image/gif") {
        bannerCanvas.classList.add("hidden");
        gridOverlay.classList.add("hidden");
        saveBtn.classList.add("hidden");
        resetBtn.classList.add("hidden");
        bannerDisplay.src = URL.createObjectURL(file);
        uploadBannerFile(file);
        return;
    }

    const reader = new FileReader();
    reader.onload = (ev) => {
        bannerImg = new Image();
        bannerImg.src = ev.target.result;
        bannerImg.onload = () => {
            resetBannerCrop(); // centraliza assim que carrega

            // Mostrar canvas e botões
            bannerCanvas.classList.remove("hidden");
            gridOverlay.classList.remove("hidden");
            saveBtn.classList.remove("hidden");
            resetBtn.classList.remove("hidden");
            bannerDisplay.classList.add("hidden");

            // botão salvar volta desabilitado
            saveBtn.disabled = true;
            saveBtn.classList.add("opacity-50", "cursor-not-allowed");
            hasChanged = false;
        };
    };
    reader.readAsDataURL(file);
});
// --------------------
// Drag para reposicionar
// --------------------
bannerCanvas.addEventListener("mousedown", (e) => {
    dragging = true;
    startX = e.offsetX - bannerPos.x;
    startY = e.offsetY - bannerPos.y;
    bannerCanvas.style.cursor = "grabbing";
});

// --------------------
// Quando usuário move a imagem → habilita botão salvar
// --------------------
bannerCanvas.addEventListener("mousemove", (e) => {
    if (!dragging || !bannerImg) return;

    const scale = Math.max(
        bannerCanvas.width / bannerImg.width,
        bannerCanvas.height / bannerImg.height,
    );
    const imgWidth = bannerImg.width * scale;
    const imgHeight = bannerImg.height * scale;

    const minX = bannerCanvas.width - imgWidth;
    const minY = bannerCanvas.height - imgHeight;
    const maxX = 0;
    const maxY = 0;

    bannerPos.x = Math.min(maxX, Math.max(minX, e.offsetX - startX));
    bannerPos.y = Math.min(maxY, Math.max(minY, e.offsetY - startY));

    drawBanner();
    enableSave();
});

bannerCanvas.addEventListener("mouseup", () => {
    dragging = false;
    bannerCanvas.style.cursor = "grab";
});
bannerCanvas.addEventListener("mouseleave", () => {
    dragging = false;
    bannerCanvas.style.cursor = "grab";
});

// --------------------
// Salvar crop (grade não entra no arquivo)
// --------------------
saveBtn.addEventListener("click", () => {
    if (!bannerImg) return;

    const exportScale = 2;
    const exportCanvas = document.createElement("canvas");
    exportCanvas.width = bannerCanvas.width * exportScale;
    exportCanvas.height = bannerCanvas.height * exportScale;
    const exportCtx = exportCanvas.getContext("2d");

    exportCtx.imageSmoothingEnabled = true;
    exportCtx.imageSmoothingQuality = "high";

    const scale = Math.max(
        bannerCanvas.width / bannerImg.width,
        bannerCanvas.height / bannerImg.height,
    );
    const imgWidth = bannerImg.width * scale;
    const imgHeight = bannerImg.height * scale;
    const x = bannerPos.x ?? (bannerCanvas.width - imgWidth) / 2;
    const y = bannerPos.y ?? (bannerCanvas.height - imgHeight) / 2;

    exportCtx.drawImage(
        bannerImg,
        x * exportScale,
        y * exportScale,
        imgWidth * exportScale,
        imgHeight * exportScale,
    );

    exportCanvas.toBlob((blob) => {
        const file = new File([blob], "banner.webp", { type: "image/webp" });

        uploadBannerFile(file);
        bannerDisplay.src = URL.createObjectURL(file);
    }, "image/webp", 0.92);
});

if (window.Livewire) {
    Livewire.on("profile-media-updated", ({ banner, avatar, ts }) => {
        if (bannerDisplay && banner) {
            const sep = banner.includes("?") ? "&" : "?";
            bannerDisplay.src = `${banner}${sep}v=${ts}`;
            bannerDisplay.classList.remove("hidden");
        }
        if (profileAvatarImage && avatar) {
            const sep = avatar.includes("?") ? "&" : "?";
            profileAvatarImage.src = `${avatar}${sep}v=${ts}`;
        }
        if (bannerDisplay && banner) {
            bannerDisplay.style.opacity = "1";
        }
        if (profileAvatarImage && avatar) {
            profileAvatarImage.style.opacity = "1";
        }
    });
}
