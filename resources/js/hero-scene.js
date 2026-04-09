import * as THREE from 'three';

/**
 * Organic floating particle orb for the hero section.
 * Warm terracotta-cream palette matching the Daimaa brand.
 */
export function initHeroScene(container) {
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.z = 4;

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    const particleCount = 1000;
    const positions = new Float32Array(particleCount * 3);
    const colors = new Float32Array(particleCount * 3);
    const sizes = new Float32Array(particleCount);

    const palette = [
        new THREE.Color('#93452d'),
        new THREE.Color('#b25d43'),
        new THREE.Color('#d4775d'),
        new THREE.Color('#c4956a'),
        new THREE.Color('#a67c52'),
        new THREE.Color('#f3bf37'),
    ];

    for (let i = 0; i < particleCount; i++) {
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        const r = 1.0 + Math.random() * 1.0;

        positions[i * 3] = r * Math.sin(phi) * Math.cos(theta);
        positions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
        positions[i * 3 + 2] = r * Math.cos(phi);

        const color = palette[Math.floor(Math.random() * palette.length)];
        colors[i * 3] = color.r;
        colors[i * 3 + 1] = color.g;
        colors[i * 3 + 2] = color.b;

        sizes[i] = 0.03 + Math.random() * 0.08;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
    geometry.setAttribute('aSize', new THREE.BufferAttribute(sizes, 1));

    const vertexShader = `
        attribute float aSize;
        varying vec3 vColor;
        varying float vAlpha;
        uniform float uTime;

        void main() {
            vColor = color;
            vec3 pos = position;

            float wave = sin(uTime * 0.4 + pos.x * 2.0 + pos.y * 1.5) * 0.2;
            float wave2 = cos(uTime * 0.3 + pos.z * 1.8) * 0.1;
            pos += normalize(pos) * (wave + wave2);

            vec4 mvPosition = modelViewMatrix * vec4(pos, 1.0);
            gl_Position = projectionMatrix * mvPosition;
            gl_PointSize = aSize * 350.0 / -mvPosition.z;
            vAlpha = smoothstep(2.5, 0.5, length(pos));
        }
    `;

    const fragmentShader = `
        varying vec3 vColor;
        varying float vAlpha;

        void main() {
            float d = distance(gl_PointCoord, vec2(0.5));
            if (d > 0.5) discard;
            float alpha = smoothstep(0.5, 0.05, d) * vAlpha;
            gl_FragColor = vec4(vColor, alpha * 0.7);
        }
    `;

    const material = new THREE.ShaderMaterial({
        vertexShader,
        fragmentShader,
        uniforms: { uTime: { value: 0 } },
        vertexColors: true,
        transparent: true,
        depthWrite: false,
        blending: THREE.NormalBlending,
    });

    const particles = new THREE.Points(geometry, material);
    scene.add(particles);

    const ringGeo = new THREE.TorusGeometry(1.5, 0.01, 16, 100);
    const ringMat = new THREE.MeshBasicMaterial({ color: '#93452d', transparent: true, opacity: 0.12 });
    const ring1 = new THREE.Mesh(ringGeo, ringMat);
    ring1.rotation.x = Math.PI / 3;
    scene.add(ring1);

    const ring2 = ring1.clone();
    ring2.rotation.x = -Math.PI / 4;
    ring2.rotation.y = Math.PI / 6;
    scene.add(ring2);

    let mouse = { x: 0, y: 0 };
    document.addEventListener('mousemove', (e) => {
        mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
    }, { passive: true });

    let startTime = performance.now();
    let animId;

    function animate() {
        animId = requestAnimationFrame(animate);
        const elapsed = (performance.now() - startTime) / 1000;
        material.uniforms.uTime.value = elapsed;

        particles.rotation.y = elapsed * 0.06 + mouse.x * 0.3;
        particles.rotation.x = elapsed * 0.04 + mouse.y * 0.2;

        ring1.rotation.z = elapsed * 0.12;
        ring2.rotation.z = -elapsed * 0.08;

        renderer.render(scene, camera);
    }

    animate();

    const ro = new ResizeObserver(() => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        if (w === 0 || h === 0) return;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
    ro.observe(container);

    return () => {
        cancelAnimationFrame(animId);
        ro.disconnect();
        renderer.dispose();
        geometry.dispose();
        material.dispose();
    };
}
