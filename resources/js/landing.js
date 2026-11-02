document.addEventListener('DOMContentLoaded', () => {
    const depthValueEl = document.getElementById('depth-value');
    const depthZoneBadgeEl = document.getElementById('depth-zone-badge');
    const depthProgressBarEl = document.getElementById('depth-progress-bar');
    const creatures = document.querySelectorAll('.parallax-creature');

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const MAX_DEPTH = 4000;

    function updateOceanDepth() {
        const scrollTop = window.scrollY || window.pageYOffset;
        const maxScroll = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        const scrollRatio = Math.min(1, Math.max(0, scrollTop / maxScroll));
        const currentDepth = Math.round(scrollRatio * MAX_DEPTH);

        if (depthValueEl) {
            depthValueEl.textContent = currentDepth.toLocaleString('vi-VN');
        }

        if (depthProgressBarEl) {
            depthProgressBarEl.style.height = `${(scrollRatio * 100).toFixed(1)}%`;
        }

        if (depthZoneBadgeEl) {
            if (currentDepth <= 200) {
                depthZoneBadgeEl.textContent = '☀️ Vùng nắng (0–200m)';
                depthZoneBadgeEl.className = 'mt-1 inline-block px-2 py-0.5 rounded text-xs font-semibold bg-sky-500/20 text-sky-200 border border-sky-400/30';
            } else if (currentDepth <= 1000) {
                depthZoneBadgeEl.textContent = '🪼 Chạng vạng (200–1.000m)';
                depthZoneBadgeEl.className = 'mt-1 inline-block px-2 py-0.5 rounded text-xs font-semibold bg-cyan-500/20 text-cyan-200 border border-cyan-400/30';
            } else {
                depthZoneBadgeEl.textContent = '🏮 Nửa đêm (1.000–4.000m)';
                depthZoneBadgeEl.className = 'mt-1 inline-block px-2 py-0.5 rounded text-xs font-semibold bg-amber-500/20 text-amber-200 border border-amber-400/30';
            }
        }

        // Hiệu ứng Parallax cho sinh vật biển (tắt nếu người dùng bật prefers-reduced-motion)
        if (!prefersReducedMotion) {
            creatures.forEach((el) => {
                const speed = parseFloat(el.dataset.speed || '0.25');
                const offset = -(scrollTop * speed * 0.25);
                el.style.transform = `translateY(${offset.toFixed(1)}px)`;
            });
        }
    }

    window.addEventListener('scroll', updateOceanDepth, { passive: true });
    updateOceanDepth();
});

