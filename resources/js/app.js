import './bootstrap';
import './echo.js'

import Alpine from 'alpinejs';

import AOS from 'aos';
import 'aos/dist/aos.css';

window.Alpine = Alpine;

Alpine.start();

AOS.init();
