import './bootstrap';

import { togglePlay } from './beats/play';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.togglePlay = togglePlay;
