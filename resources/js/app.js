import './bootstrap';
import './echo.js'

import Alpine from 'alpinejs';
import { marked } from 'marked';

import AOS from 'aos';
import 'aos/dist/aos.css';

import './calendar.js';

import ebookReader from './ebook-reader.js';

window.marked = marked;
window.Alpine = Alpine;
Alpine.data('ebookReader', ebookReader);

Alpine.start();

AOS.init();

