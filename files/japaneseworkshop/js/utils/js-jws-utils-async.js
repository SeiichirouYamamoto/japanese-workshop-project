


function waitUntil(testFn, { timeoutMs = 8000 } = {}) {
    return new Promise((resolve, reject) => {
        const start = performance.now();
        (function loop() {
            if (testFn()) return resolve();
            if (performance.now() - start > timeoutMs) return reject(new Error('waitUntil timeout'));
            requestAnimationFrame(loop);
        })();
    });
}


function wait(ms) {
    return new Promise(r => setTimeout(r, ms));
}
