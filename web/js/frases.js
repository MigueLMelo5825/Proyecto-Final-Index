
const literaryAndCinemaQuotes = [
  { text: "No hay amigo tan leal como un libro.", author: "Ernest Hemingway" },
  { text: "Un lector vive mil vidas antes de morir.", author: "George R. R. Martin" },
  { text: "El que lee mucho y anda mucho, ve mucho y sabe mucho.", author: "Miguel de Cervantes" },
  { text: "Un libro es un sueño que tienes en tus manos.", author: "Neil Gaiman" },
  { text: "La literatura no es otra cosa que un sueño dirigido.", author: "Jorge Luis Borges" },
  { text: "Siempre imaginé que el Paraíso sería algún tipo de biblioteca.", author: "Jorge Luis Borges" },
  { text: "Donde se queman libros, se termina quemando personas.", author: "Heinrich Heine" },
  { text: "Un clásico es un libro que nunca termina de decir lo que tiene que decir.", author: "Italo Calvino" },
  { text: "Los libros son la prueba de que los humanos pueden hacer magia.", author: "Carl Sagan" },
  { text: "Aprender a leer es encender un fuego; cada sílaba pronunciada es una chispa.", author: "Victor Hugo" },
  { text: "El mundo era tan reciente que muchas cosas carecían de nombre.", author: "Gabriel García Márquez" },
  { text: "Para viajar lejos, no hay mejor nave que un libro.", author: "Emily Dickinson" },
  { text: "El verbo leer no soporta el imperativo.", author: "Daniel Pennac" },
  { text: "Un hogar sin libros es como un cuerpo sin alma.", author: "Cicerón" },
  { text: "Los libros no cambian el mundo, cambian a las personas que van a cambiar el mundo.", author: "Mario Vargas Llosa" },
  { text: "El verdadero viaje de descubrimiento no consiste en buscar nuevos paisajes, sino en mirar con nuevos ojos.", author: "Marcel Proust" },
  { text: "Leer es resistir.", author: "Antonio Basanta" },
  { text: "La pasión por la lectura es la llave del conocimiento.", author: "Isaac Asimov" },
  { text: "Un libro abierto es un cerebro que habla; cerrado, un amigo que espera.", author: "Proverbio hindú" },
  { text: "Las palabras tienen el poder de cambiar el mundo.", author: "Margaret Atwood" }


];


function displayRandomQuote() {
  const quoteTextElement = document.getElementById("quoteText");
  const quoteAuthorElement = document.getElementById("quoteAuthor");

  if (!quoteTextElement || !quoteAuthorElement) return;

  const randomIndex = Math.floor(Math.random() * literaryAndCinemaQuotes.length);
  const selectedQuote = literaryAndCinemaQuotes[randomIndex];

  quoteTextElement.textContent = `“${selectedQuote.text}”`;
  quoteAuthorElement.textContent = `— ${selectedQuote.author}`;
}


document.addEventListener("DOMContentLoaded", () => {
  displayRandomQuote();

  const newQuoteButton = document.getElementById("newQuoteBtn");
  if (newQuoteButton) {
    newQuoteButton.addEventListener("click", displayRandomQuote);
  }
});
