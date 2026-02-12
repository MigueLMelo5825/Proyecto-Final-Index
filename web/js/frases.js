
const literaryAndCinemaQuotes = [
  { text: "No hay amigo tan leal como un libro.", author: "Ernest Hemingway" },
  { text: "Un lector vive mil vidas antes de morir.", author: "George R. R. Martin" },
  { text: "El que lee mucho y anda mucho, ve mucho y sabe mucho.", author: "Miguel de Cervantes" },
  { text: "La vida es un sueño, y los sueños, sueños son.", author: "Calderón de la Barca" },
  { text: "El cine es verdad 24 veces por segundo.", author: "Jean-Luc Godard" },
  { text: "No son nuestras habilidades las que muestran quiénes somos, sino nuestras elecciones.", author: "J.K. Rowling" },
  { text: "La imaginación es más importante que el conocimiento.", author: "Albert Einstein" },
  { text: "Que la Fuerza te acompañe.", author: "Star Wars" },
  { text: "Hasta el infinito y más allá.", author: "Toy Story" },
  { text: "Siempre nos quedará París.", author: "Casablanca" },
  { text: "La esperanza es algo bueno, quizá lo mejor de todo.", author: "The Shawshank Redemption" },
  { text: "Con un gran poder viene una gran responsabilidad.", author: "Spider-Man" },
  { text: "La vida es como una caja de bombones, nunca sabes lo que te va a tocar.", author: "Forrest Gump" },
  { text: "Un libro es un sueño que tienes en tus manos.", author: "Neil Gaiman" },
  { text: "El verdadero viaje de descubrimiento no consiste en buscar nuevos paisajes, sino en mirar con nuevos ojos.", author: "Marcel Proust" }
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
