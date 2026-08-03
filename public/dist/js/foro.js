function renderMensajes(data) {
    let html = '';
    data.forEach(item => {
        // Mensaje principal
        html += `
            <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-left">${item.usuario}</span>
                    <span class="direct-chat-timestamp float-right">${item.created_at}</span>
                </div>
                <img class="direct-chat-img" src="/img/user1-128x128.jpg" alt="User Image">
                <div class="direct-chat-text">${item.mensaje}</div>
            </div>
        `;

        // Respuestas asociadas
        if (item.respuestas) {
            item.respuestas.forEach(resp => {
                html += `
                    <div class="direct-chat-msg right">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-right">${resp.usuario}</span>
                            <span class="direct-chat-timestamp float-left">${resp.created_at}</span>
                        </div>
                        <img class="direct-chat-img" src="/img/user2-128x128.jpg" alt="User Image">
                        <div class="direct-chat-text">${resp.respuesta}</div>
                    </div>
                `;
            });
        }
    });
    document.getElementById('contenedor-mensajes').innerHTML = html;
}
