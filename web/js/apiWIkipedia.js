// ===============================
//  HOY EN LA HISTORIA (Wikipedia) — en español
// ===============================

async function cargarHoyEnHistoria() {
    try {
        const hoy = new Date();
        const mes = hoy.getMonth() + 1;
        const dia = hoy.getDate();

        const url = `https://en.wikipedia.org/api/rest_v1/feed/onthisday/events/${mes}/${dia}`;
        const res = await fetch(url);
        const data = await res.json();

        // Palabras clave en texto
        const clavesTexto = [
            "film", "movie", "cinema", "director", "actor", "actress",
            "novel", "book", "writer", "author", "literature", "poet",
            "published", "story", "fiction", "screenplay", "screenwriter"
        ];

        // Palabras clave en categorías
        const clavesCategorias = [
            "film", "movie", "cinema", "actor", "actress", "director",
            "novel", "book", "writer", "author", "literature", "poet"
        ];

        // Filtrar eventos relevantes
        const filtrados = data.events.filter(ev => {
            const texto = ev.text.toLowerCase();

            const coincideTexto = clavesTexto.some(c => texto.includes(c));

            const coincideCategoria = ev.pages?.some(p =>
                p?.titles?.normalized &&
                clavesCategorias.some(c =>
                    (p.titles.normalized.toLowerCase().includes(c)) ||
                    (p.description?.toLowerCase().includes(c))
                )
            );

            return coincideTexto || coincideCategoria;
        });

        if (filtrados.length === 0) {
            document.getElementById("hoyHistoria").innerHTML =
                "<p>No hay eventos culturales destacados hoy.</p>";
            return;
        }

        // Elegir uno aleatorio
        const evento = filtrados[Math.floor(Math.random() * filtrados.length)];

        // TRADUCIR el texto al español
        const textoOriginal = `${evento.year} — ${evento.text}`;
        const urlTrad = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(textoOriginal)}&langpair=en|es`;

        const tradRes = await fetch(urlTrad);
        const tradData = await tradRes.json();
        const textoTraducido = tradData.responseData.translatedText || textoOriginal;

        // Mostrar
        document.getElementById("hoyHistoria").innerHTML = `
            <h5 class="mb-2 fw-bold">Hoy en la historia</h5>
            <p class="mb-1">${textoTraducido}</p>
            <a href="https://en.wikipedia.org/wiki/${evento.pages[0].title}" 
               target="_blank" class="small">
               Ver más en Wikipedia
            </a>
        `;
    } catch (e) {
        console.error("Error cargando historia:", e);
    }
}


// ===============================
//  ¿SABÍAS QUE…? (Curiosidades en español)
// ===============================

async function cargarSabiasQue() {
    try {
        // 1. Obtener curiosidad en inglés
        const res = await fetch("https://uselessfacts.jsph.pl/random.json?language=en");
        const data = await res.json();
        const textoOriginal = data.text;

        // 2. Filtrar por temática cultural
        const clavesCultura = [
            "book", "novel", "writer", "author", "library", "story",
            "film", "movie", "cinema", "director", "actor", "actress",
            "literature", "poetry", "reading", "publishing"
        ];

        if (!clavesCultura.some(c => textoOriginal.toLowerCase().includes(c))) {
            return cargarSabiasQue(); // pedir otra si no es cultural
        }

        // 3. Traducir al español usando MyMemory (gratuito)
        const urlTrad = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(textoOriginal)}&langpair=en|es`;
        const tradRes = await fetch(urlTrad);
        const tradData = await tradRes.json();

        const textoTraducido = tradData.responseData.translatedText || textoOriginal;

        // 4. Mostrar en la tarjeta
        document.getElementById("sabiasQue").innerHTML = `
            <h5 class="mb-2 fw-bold">¿Sabías que…?</h5>
            <p>${textoTraducido}</p>
        `;
    } catch (e) {
        console.error("Error curiosidad:", e);
    }
}


// ===============================
//  INICIALIZACIÓN
// ===============================

document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("hoyHistoria")) cargarHoyEnHistoria();
    if (document.getElementById("sabiasQue")) cargarSabiasQue();
});
