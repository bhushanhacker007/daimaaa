import { initHeroScene } from './hero-scene.js';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('hero-webgl');
    if (container) initHeroScene(container);
});
