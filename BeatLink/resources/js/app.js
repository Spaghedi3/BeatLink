import './bootstrap';

import './tracks/play.js';

import './tracks/hover-player.js';

import './tracks/form-toggle.js';

import './tracks/name-exists.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.togglePlay = togglePlay;
