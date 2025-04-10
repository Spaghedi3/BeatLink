import './bootstrap';

import './beats/play.js';

import './beats/hover-player.js';

import './beats/form-toggle.js';

import './beats/name-exists.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.togglePlay = togglePlay;
