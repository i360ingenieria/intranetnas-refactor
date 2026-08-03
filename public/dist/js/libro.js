document.addEventListener('DOMContentLoaded', function() {
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

        if (pageIndicator) {
            pageIndicator.textContent = `Página ${currentIndex + 1}/${pages.length}`;
        }
        
        if (prevBtn) prevBtn.disabled = currentIndex === 0;
        if (nextBtn) nextBtn.disabled = currentIndex === pages.length - 1;
    }

    // Función para abrir lightbox
    function openLightbox(index) {
        currentLightboxIndex = index;
        const page = pages[index];
        
        if (page) {
            lightboxImg.src = page.dataset.img;
            lightboxTitle.textContent = page.dataset.title;
            lightboxCaption.textContent = page.dataset.caption;
            lightboxCounter.textContent = `${index + 1} / ${pages.length}`;
            
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    // Función para cerrar lightbox
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
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

    // Verificar que los elementos existen antes de agregar eventos
    if (pages.length > 0) {
        // Event listeners para cada página (clic en la imagen)
        pages.forEach((page, index) => {
            const img = page.querySelector('.page-image');
            if (img) {
                img.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openLightbox(index);
                });
            }
        });
    }

    // Event listeners del lightbox
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', () => {
            navigateLightbox(-1);
        });
    }
    
    if (lightboxNext) {
        lightboxNext.addEventListener('click', () => {
            navigateLightbox(1);
        });
    }

    // Cerrar con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lightbox?.classList.contains('active')) {
            closeLightbox();
        }
        
        // Navegación con teclas en lightbox
        if (lightbox?.classList.contains('active')) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateLightbox(-1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                navigateLightbox(1);
            }
        }
        
        // Navegación del libro (solo si lightbox no está activo)
        if (!lightbox?.classList.contains('active')) {
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
    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    // Eventos de navegación del libro
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentIndex < pages.length - 1) {
                currentIndex++;
                updatePages();
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updatePages();
            }
        });
    }

    // Inicializar
    updatePages();
});