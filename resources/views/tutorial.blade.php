<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro de Imágenes · Con Ampliación Lightbox</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #1a1e24;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            padding: 1rem;
        }

        .book-container {
            max-width: 1200px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .book-title {
            color: #ecf0f1;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 300;
            letter-spacing: 2px;
            text-align: center;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        /* El libro */
        .book {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            background: #2c3e50;
            border-radius: 20px 8px 8px 20px;
            box-shadow: 
                -10px 10px 20px rgba(0,0,0,0.5),
                0 0 0 2px #8b6b4d inset,
                0 0 0 6px #d4a373 inset;
            padding: 15px;
            cursor: pointer;
        }

        /* Lomo del libro */
        .book::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 10px;
            bottom: 10px;
            width: 16px;
            background: linear-gradient(90deg, #5d3a1a, #8b5a2b);
            border-radius: 8px 0 0 8px;
            box-shadow: -2px 0 5px rgba(0,0,0,0.3);
            z-index: 5;
        }

        /* Páginas del libro */
        .pages {
            position: relative;
            width: 100%;
            aspect-ratio: 3/4;
            background: #f5e6d3;
            border-radius: 12px 4px 4px 12px;
            overflow: hidden;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.1);
        }

        /* Cada página individual */
        .page {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.645, 0.045, 0.355, 1);
            transform-origin: left center;
            transform-style: preserve-3d;
            backface-visibility: hidden;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            border-radius: 0 8px 8px 0;
            overflow: hidden;
        }

        /* Estilos para imágenes */
        .page img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
            cursor: zoom-in;
        }

        /* Overlay para efectos sobre imágenes */
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
            pointer-events: none;
            z-index: 2;
        }

        /* Indicador de zoom */
        .zoom-hint {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.5);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255,255,255,0.3);
            z-index: 10;
            pointer-events: none;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .page:hover .zoom-hint {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Título de la imagen */
        .image-title {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            z-index: 10;
            background: rgba(0,0,0,0.3);
            padding: 8px 16px;
            border-radius: 40px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Número de página */
        .page-number {
            position: absolute;
            bottom: 20px;
            right: 20px;
            color: white;
            font-size: 0.9rem;
            background: rgba(0,0,0,0.3);
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Descripción de la imagen */
        .image-caption {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-size: 0.9rem;
            background: rgba(0,0,0,0.4);
            padding: 4px 12px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Estados de las páginas */
        .page.active {
            transform: rotateY(0deg) translateX(0);
            z-index: 10;
        }

        .page.prev {
            transform: rotateY(-180deg) translateX(-100%);
            z-index: 5;
        }

        .page.next {
            transform: rotateY(180deg) translateX(100%);
            z-index: 5;
        }

        .page.hidden {
            transform: rotateY(180deg) translateX(100%);
            z-index: 1;
        }

        /* Sombra del pliegue */
        .page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(90deg, rgba(0,0,0,0.1), transparent);
            z-index: 20;
            pointer-events: none;
        }

        /* Controles del libro */
        .book-controls {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
            align-items: center;
            background: #2c3e50;
            padding: 1rem 2rem;
            border-radius: 60px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .page-indicator {
            color: #ecf0f1;
            font-size: 1.2rem;
            font-weight: 500;
            min-width: 120px;
            text-align: center;
            background: #34495e;
            padding: 0.5rem 1rem;
            border-radius: 40px;
            letter-spacing: 1px;
        }

        .btn {
            background: #e74c3c;
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid #ecf0f1;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .btn:hover:not(:disabled) {
            background: #c0392b;
            transform: scale(1.1);
        }

        .btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            background: #7f8c8d;
        }

        /* ===== LIGHTBOX / MODAL ===== */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            backdrop-filter: blur(10px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .lightbox.active {
            display: flex;
            opacity: 1;
        }

        .lightbox-content {
            position: relative;
            width: 90%;
            height: 90%;
            margin: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .lightbox-image-container {
            position: relative;
            max-width: 90%;
            max-height: 80%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .lightbox-image-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .lightbox-info {
            position: absolute;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            border-radius: 0 0 12px 12px;
        }

        .lightbox-title {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .lightbox-caption {
            font-size: 1rem;
            opacity: 0.8;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 3rem;
            cursor: pointer;
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.3);
            z-index: 1010;
        }

        .lightbox-close:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.1);
            border-color: rgba(255,255,255,0.5);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1010;
        }

        .lightbox-nav:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-nav.prev {
            left: 30px;
        }

        .lightbox-nav.next {
            right: 30px;
        }

        .lightbox-counter {
            position: absolute;
            bottom: 30px;
            right: 30px;
            color: white;
            font-size: 1rem;
            background: rgba(0,0,0,0.5);
            padding: 8px 16px;
            border-radius: 40px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Efectos especiales para las páginas */
        .effect-1::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255,215,0,0.2), transparent 70%);
            mix-blend-mode: overlay;
            pointer-events: none;
            z-index: 3;
        }

        .effect-2::after {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(45deg, transparent 0px, transparent 20px, rgba(255,255,255,0.1) 20px, rgba(255,255,255,0.1) 40px);
            pointer-events: none;
            z-index: 3;
        }

        /* Mensaje de clic */
        .click-message {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.6);
            font-size: 0.8rem;
            background: rgba(0,0,0,0.3);
            padding: 4px 12px;
            border-radius: 20px;
            z-index: 30;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="book-container">
        <h1 class="book-title">📖 Uso de la busqueda en la NAS </h1>
        <p style="color: #95a5a6; margin-top: 1rem; text-align: center;">
            ⬅️➡️ Navega con las flechas · Haz clic en cualquier imagen para ampliar
        </p>
        <div class="book">
            <div class="pages" id="pages">
                <!-- Página 1 -->
                <div class="page active effect-1" data-index="0" data-title="lista encontrados" data-caption="Montañas al atardecer" data-img=<img src="{{ asset('dist/img/instructivo/1.png') }}">
                    <img src="{{ asset('dist/img/instructivo/1.png') }}" alt="Atardecer en montañas" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">lista de Carpetas</div>
                    <div class="image-title"> Digitar en la busqueda</div>
                    <div class="page-number">1/10</div>
                    <div class="click-message">Haz clic en la imagen para ampliar</div>
                </div>
                
                <!-- Página 2 -->
                <div class="page next effect-2" data-index="1" data-title="Paraíso Tropical" data-caption="AZUL DESCARGAR" data-img="{{ asset('dist/img/instructivo/3.png') }}">
                    <img src="{{ asset('dist/img/instructivo/3.png') }}" alt="Playa tropical" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">ACIONES DE LOS BOTONES</div>
                    <div class="image-title">Boton verde -->VER EL DOCUMENTO</div>
                    <div class="page-number">2/10</div>
                </div>
                
                <!-- Página 3 -->
                <div class="page hidden" data-index="2" data-title="VISTA " data-caption="VISOR PDF" data-img="{{ asset('dist/img/instructivo/4.png') }}">
                    <img src="{{ asset('dist/img/instructivo/4.png') }}" alt="Ciudad nocturna" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">DESCARGA  O</div>
                    <div class="image-title">VER DOCUMENTO</div>
                    <div class="page-number">3/10</div>
                </div>
                
                <!-- Página 4 -->
                <div class="page hidden" data-index="3" data-title="EXPLORAR POR CARPETAS" data-caption="BOTON SUBIR NIVEL PARA DEVOLVERSE" data-img="{{ asset('dist/img/instructivo/51.png') }}">
                    <img src="{{ asset('dist/img/instructivo/51.png') }}" alt="CLICK NE LA CARPETA" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">🌲 BUSQUEDA EN CADA CARPETA</div>
                    <div class="image-title">SUBIR DE NIVEL ES PAR DEVOLVER POR LAS CARPETA</div>
                    <div class="page-number">4/10</div>
                </div>
                
                <!-- Página 5 -->
                <div class="page hidden" data-index="4" data-title="Azul Profundo" data-caption="Olas infinitas" data-img="{{ asset('dist/img/instructivo/51.png') }}">
                    <img src="{{ asset('dist/img/instructivo/51.png') }}" alt="Océano" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">🌊 Océano</div>
                    <div class="image-title">Azul Profundo</div>
                    <div class="page-number">5/10</div>
                </div>
                <div class="page hidden" data-index="4" data-title="Azul Profundo" data-caption="Olas infinitas" data-img="{{ asset('dist/img/instructivo/6.png') }}">
                    <img src="{{ asset('dist/img/instructivo/6.png') }}" alt="Océano" class="page-image">
                    <div class="image-overlay"></div>
                    <div class="zoom-hint">🔍</div>
                    <div class="image-caption">🌊 Océano</div>
                    <div class="image-title">Azul Profundo</div>
                    <div class="page-number">5/10</div>
                </div>
              
            </div>
        </div>

        <div class="book-controls">
            <button class="btn" id="prevBtn" disabled>←</button>
            <span class="page-indicator" id="pageIndicator">Página 1/10</span>
            <button class="btn" id="nextBtn">→</button>
        </div>

        
    </div>

    <!-- LIGHTBOX MODAL -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-close" id="lightboxClose">✕</div>
        <div class="lightbox-nav prev" id="lightboxPrev">‹</div>
        <div class="lightbox-nav next" id="lightboxNext">›</div>
        
        <div class="lightbox-content">
            <div class="lightbox-image-container">
                <img src="" alt="Imagen ampliada" id="lightboxImg">
            </div>
            <div class="lightbox-info">
                <div class="lightbox-title" id="lightboxTitle"></div>
                <div class="lightbox-caption" id="lightboxCaption"></div>
            </div>
            <div class="lightbox-counter" id="lightboxCounter"></div>
        </div>
    </div>

    <script>
        // Elementos del libro
        const pages = document.querySelectorAll('.page');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageIndicator = document.getElementById('pageIndicator');
        let currentIndex = 0;

        // Elementos del lightbox
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightboxImg');
        const lightboxTitle = document.getElementById('lightboxTitle');
        const lightboxCaption = document.getElementById('lightboxCaption');
        const lightboxCounter = document.getElementById('lightboxCounter');
        const lightboxClose = document.getElementById('lightboxClose');
        const lightboxPrev = document.getElementById('lightboxPrev');
        const lightboxNext = document.getElementById('lightboxNext');
        
        let currentLightboxIndex = 0;

        // Función para actualizar páginas del libro
        function updatePages() {
            pages.forEach((page, index) => {
                page.classList.remove('active', 'prev', 'next', 'hidden');
                
                if (index === currentIndex) {
                    page.classList.add('active');
                } else if (index === currentIndex - 1) {
                    page.classList.add('prev');
                } else if (index === currentIndex + 1) {
                    page.classList.add('next');
                } else {
                    page.classList.add('hidden');
                }
            });

            pageIndicator.textContent = `Página ${currentIndex + 1}/10`;
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === pages.length - 1;
        }

        // Función para abrir lightbox
        function openLightbox(index) {
            currentLightboxIndex = index;
            const page = pages[index];
            
            lightboxImg.src = page.dataset.img;
            lightboxTitle.textContent = page.dataset.title;
            lightboxCaption.textContent = page.dataset.caption;
            lightboxCounter.textContent = `${index + 1} / ${pages.length}`;
            
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // Evitar scroll
        }

        // Función para cerrar lightbox
        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = ''; // Restaurar scroll
        }

        // Función para navegar en lightbox
        function navigateLightbox(direction) {
            let newIndex = currentLightboxIndex + direction;
            
            if (newIndex >= 0 && newIndex < pages.length) {
                currentLightboxIndex = newIndex;
                const page = pages[newIndex];
                
                lightboxImg.src = page.dataset.img;
                lightboxTitle.textContent = page.dataset.title;
                lightboxCaption.textContent = page.dataset.caption;
                lightboxCounter.textContent = `${newIndex + 1} / ${pages.length}`;
            }
        }

        // Event listeners para cada página (clic en la imagen)
        pages.forEach((page, index) => {
            const img = page.querySelector('.page-image');
            img.addEventListener('click', (e) => {
                e.stopPropagation();
                openLightbox(index);
            });
        });

        // Event listeners del lightbox
        lightboxClose.addEventListener('click', closeLightbox);
        
        lightboxPrev.addEventListener('click', () => {
            navigateLightbox(-1);
        });
        
        lightboxNext.addEventListener('click', () => {
            navigateLightbox(1);
        });

        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                closeLightbox();
            }
            
            // Navegación con teclas en lightbox
            if (lightbox.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    navigateLightbox(-1);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    navigateLightbox(1);
                }
            }
            
            // Navegación del libro (solo si lightbox no está activo)
            if (!lightbox.classList.contains('active')) {
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    if (currentIndex < pages.length - 1) {
                        currentIndex++;
                        updatePages();
                    }
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    if (currentIndex > 0) {
                        currentIndex--;
                        updatePages();
                    }
                }
            }
        });

        // Clic fuera de la imagen para cerrar
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Eventos de navegación del libro
        nextBtn.addEventListener('click', () => {
            if (currentIndex < pages.length - 1) {
                currentIndex++;
                updatePages();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updatePages();
            }
        });

        // Inicializar
        updatePages();
    </script>
</body>
</html>