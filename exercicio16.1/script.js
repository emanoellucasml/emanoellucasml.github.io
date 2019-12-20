var numeros = []

window.document.querySelector('#btnAdicionar').addEventListener('click', function(){
    var numeroASerAdicionado = Number.parseInt(window.document.querySelector('#txtNumero').value)
    if(!Number.isNaN(numeroASerAdicionado) && (numeroASerAdicionado >= 1 && numeroASerAdicionado <= 100) && numeros.indexOf(numeroASerAdicionado) == -1){
        numeros.push(numeroASerAdicionado)
        atualizaExibicao()
        limpaCaixaDeTexto()
        limpaResultado()
    }else{
        window.alert('Inválido!')
    }
})

window.document.querySelector('#btnFinalizar').addEventListener('click', function(){
    if(numeros.length == 0){
        window.alert('ERRO! Para finalizar, deve haver pelo menos um número na lista.')
        return
    }
    resumo()
})

function atualizaExibicao(){
    let wrapperNumero = window.document.querySelector('#wrapperNumero')
    wrapperNumero.innerHTML = ''
    for(let i in numeros){
        let divComNumero = window.document.createElement('div')
        divComNumero.setAttribute('class', 'numero')
        let textoNumero = window.document.createTextNode(numeros[i].toString())
        divComNumero.appendChild(textoNumero)
        wrapperNumero.appendChild(divComNumero)
    }
}

function limpaCaixaDeTexto(){
    window.document.querySelector('#txtNumero').value = ''
}

function resumo(){
    limpaResultado()
    numeros.sort(function (a, b){
        return a - b;
    })
    let soma = 0
    for(let i in numeros){
        soma += numeros[i]
    }
    let msg = []
    msg[0] = `Ao todo, temos ${numeros.length} números cadastrados.`
    msg[1] = `O maior valor informado foi ${numeros[numeros.length - 1]}`
    msg[2] = `O menor valor informado foi ${numeros[0]}`
    msg[3] = `Somando todos os valores, temos ${soma}`
    msg[4] = `A média dos valores digitados é ${(soma/numeros.length).toFixed(2)}`
    let wrapperNumero = window.document.querySelector('#resultado')
    for(let i = 0; i < 5; i++){
        let elemento = window.document.createElement('p')
        let texto = window.document.createTextNode(msg[i])
        elemento.appendChild(texto)
        wrapperNumero.appendChild(elemento)
    }

}

function limpaResultado(){
    window.document.querySelector('#resultado').innerHTML = ''
}