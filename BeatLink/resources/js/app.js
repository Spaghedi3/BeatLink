import './bootstrap';

import './tracks/play.js';

import './tracks/hover-player.js';

import './tracks/form-toggle.js';

import './tracks/name-exists.js';

import './tracks/react.js';

import './topFunction.js';

import { destroyAudio } from './cleanup.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.addEventListener('beforeunload', destroyAudio);
