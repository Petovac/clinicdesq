<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
html, body {
    margin: 0;
    height: 100%;
    background: #111;
    overflow: hidden;
}

#viewer {
    height: 100%;
    overflow: auto;
    touch-action: none;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

canvas {
    display: block;
    transform-origin: center top;
    will-change: transform;
}
</style>
</head>

<body>

<div id="viewer"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc =
  'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const url = "{{ route('vet.diagnostics.files.view', $file->id) }}";

const viewer = document.getElementById('viewer');
const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d');
viewer.appendChild(canvas);

let pdfPage;
let baseScale = 1;
let visualScale = 1;

/* ===== LOAD & RENDER ONCE ===== */
pdfjsLib.getDocument(url).promise
.then(pdf => pdf.getPage(1))
.then(page => {
    pdfPage = page;
    renderBase();
});

function renderBase() {
    const viewport = pdfPage.getViewport({ scale: baseScale });
    canvas.width = viewport.width;
    canvas.height = viewport.height;

    pdfPage.render({
        canvasContext: ctx,
        viewport
    });
}

/* =========================
   TOUCH PINCH (SMOOTH)
========================= */
let lastDistance = null;

viewer.addEventListener('touchmove', e => {
    if (e.touches.length === 2) {
        e.preventDefault();

        const dx = e.touches[0].pageX - e.touches[1].pageX;
        const dy = e.touches[0].pageY - e.touches[1].pageY;
        const distance = Math.sqrt(dx * dx + dy * dy);

        if (lastDistance) {
            const delta = (distance - lastDistance) * 0.003;
            visualScale = Math.min(Math.max(visualScale + delta, 0.6), 3);
            canvas.style.transform = `scale(${visualScale})`;
        }

        lastDistance = distance;
    }
}, { passive: false });

viewer.addEventListener('touchend', () => {
    lastDistance = null;
    commitZoom();
});

/* =========================
   TRACKPAD PINCH (MAC)
========================= */
viewer.addEventListener('wheel', e => {
    if (e.ctrlKey) {
        e.preventDefault();

        visualScale += e.deltaY * -0.003;
        visualScale = Math.min(Math.max(visualScale, 0.6), 3);
        canvas.style.transform = `scale(${visualScale})`;

        clearTimeout(commitTimer);
        commitTimer = setTimeout(commitZoom, 70);
    }
}, { passive: false });

let commitTimer = null;

/* ===== COMMIT FINAL ZOOM ===== */
function commitZoom() {
    baseScale *= visualScale;
    visualScale = 1;
    canvas.style.transform = 'scale(1)';
    renderBase();
}
</script>

</body>
</html>