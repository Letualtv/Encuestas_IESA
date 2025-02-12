// Función para mostrar notificaciones
function showToast(message, type) {
    const toastContainer =
        document.getElementById("toastContainer") ||
        document.body.appendChild(document.createElement("div"));
    toastContainer.id = "toastContainer";
    toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";

    const toast = document.createElement("div");
    toast.className = `toast align-items-center border-0 bg-${type} text-white`;
    toast.role = "alert";
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    setTimeout(() => toast.remove(), 3000); // Eliminar el toast después de 3 segundos
}