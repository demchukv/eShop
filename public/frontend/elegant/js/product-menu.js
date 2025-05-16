document.addEventListener('livewire:navigated', () => {

    const toggleButton = document.getElementById('categoryMenuToggle');
    const menu = document.getElementById('productMenu');

    // Створення оверлею
    const createOverlay = () => {
        const overlay = document.createElement('div');
        overlay.className = 'overlay';
        document.body.appendChild(overlay);
        return overlay;
    };

    // Перемикання меню та оверлею
    toggleButton.addEventListener('click', (e) => {
        e.stopPropagation();
        const isVisible = menu.classList.toggle('is-visible');
        let overlay = document.querySelector('.overlay');

        if (isVisible) {
            if (!overlay) {
                overlay = createOverlay();
            }
            overlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden'; // Блокування прокрутки
        } else {
            if (overlay) {
                overlay.classList.remove('is-visible');
            }
            document.body.style.overflow = 'auto'; // Розблокування прокрутки
        }
    });

    // Закриття меню при кліку на оверлей
    document.addEventListener('click', (e) => {
        const overlay = document.querySelector('.overlay');
        if (e.target.classList.contains('overlay')) {
            menu.classList.remove('is-visible');
            if (overlay) {
                overlay.classList.remove('is-visible');
            }
            document.body.style.overflow = 'auto';
        }
    });

    // Закриття меню при кліку поза ним
    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && !toggleButton.contains(e.target)) {
            menu.classList.remove('is-visible');
            const overlay = document.querySelector('.overlay');
            if (overlay) {
                overlay.classList.remove('is-visible');
            }
            document.body.style.overflow = 'auto';
        }
    });

    // Закриття меню при натисканні Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            menu.classList.remove('is-visible');
            const overlay = document.querySelector('.overlay');
            if (overlay) {
                overlay.classList.remove('is-visible');
            }
            document.body.style.overflow = 'auto';
        }
    });

});