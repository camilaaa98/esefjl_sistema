/**
 * Lógica del Dashboard - ESE Fabio Jaramillo
 */
document.addEventListener('DOMContentLoaded', () => {
    // Animación de entrada de tarjetas
    const cards = document.querySelectorAll('.card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.5s ease ${index * 0.1}s`;

        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });

    // Manejo de botones de acción
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('action-btn')) {
            const row = e.target.closest('tr');
            const item = row.cells[0].innerText;
            alert(`Iniciando proceso de reabastecimiento para: ${item}`);
        }
    });

    // Simulación de actualización de solicitudes en tiempo real
    const requestsList = document.getElementById('requestsList');

    const addSimulatedRequest = (municipio) => {
        const div = document.createElement('div');
        div.className = 'request-item';
        div.style.animation = 'slideIn 0.5s forwards';
        div.innerHTML = `
            <span>${municipio} - Solicitud Urgente</span>
            <span class="badge-pending">PENDIENTE</span>
        `;
        requestsList.prepend(div);
    };

    // Estilos para animación de entrada
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .badge-pending { background: #fff3e0; color: #ef6c00; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; }
        .request-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
    `;
    document.head.appendChild(style);
});
