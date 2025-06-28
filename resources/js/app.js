import './bootstrap';
import Typed from 'typed.js';
// import '@animxyz/core';



document.addEventListener('livewire:navigated', () => {
    if (document.getElementById('typed-output')) {
        new Typed('#typed-output', {
            strings: [
                "Узнай свою профессию",
                "за 20 минут!",
                "Индивидуальный разбор твоих талантов",
                "Рекомендации по выбору профессий"
            ],
            typeSpeed: 50,
            backSpeed: 25,
            backDelay: 1500,
            loop: true
        });
    }
});


// import Alpine from 'alpinejs';

// window.Alpine = Alpine;
// Alpine.start();

