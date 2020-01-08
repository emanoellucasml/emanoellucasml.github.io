const elementos = [
    {tag: 'p', texto: 'Frase 1'},
    {tag: 'div', texto: 'Frase 2'},
    {tag: 'footer', texto: 'Frase 3'},
    {tag: 'section', texto: 'Frase 4'}
]

window.onload = () => {
    efeitoEscrita(window.document.querySelector('h1'))
    let container = window.document.querySelector('.container')
    for(let i = 0; i < elementos.length; i++){
        let tagASerCriada = elementos[i].tag
        let textoASerAdicionado = window.document.createTextNode(elementos[i].texto.toString())
        tagASerCriada = window.document.createElement(tagASerCriada.toString())
        tagASerCriada.appendChild(textoASerAdicionado)
        container.appendChild(tagASerCriada)
    }
}


function efeitoEscrita(elemento) {
    const textoArray = elemento.innerHTML.split('');
    elemento.innerHTML = '';
    for(let i = 0; i < textoArray.length; i++) {
        setTimeout(() => elemento.innerHTML += textoArray[i], 75 * i);
        setTimeout(() => reproduzSomTecla(), 75 * i);
    }
}

function reproduzSomTecla(){
    var audio = new Audio('assets/audio/tecla.wav');
    audio.addEventListener('canplaythrough', function() {
        audio.play();
    });
}

