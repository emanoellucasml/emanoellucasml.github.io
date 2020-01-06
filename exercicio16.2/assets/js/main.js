let imc = undefined
let res = undefined
let msg = undefined

const cor = {
    ABAIXO: '#a85860',
    NORMAL: '#61b079',
    SOBREPESO: '#f03c66',
    OBESIDADEI: '#f72d5c',
    OBESIDADEII: '#f7164b',
    OBESIDADEIII: '#ff0000',
    ERRO: '#802929'
}

window.onload = function(){
    window.document.querySelector('#btnEnviar').addEventListener('click', function(event){
        event.preventDefault()
        let peso = Number(window.document.querySelector('#txtPeso').value)
        let altura = Number(window.document.querySelector('#txtAltura').value)
        if(Number.isNaN(peso) || Number.isNaN(altura)){
            msg = 'Peso e/ou altura inválido(s)!'
            mostraResultado(msg, cor.ERRO)
            return
        }
        imc = (peso/(altura**2)).toFixed(1)
        msg = 'Seu IMC é '
        if(imc < 18.5){
            res = ' (Abaixo do peso)'
            msg = msg + imc + res
            mostraResultado(msg, cor.ABAIXO)
        }else if(imc >= 18.5 && imc <= 24.9){
            res = ' (Peso normal)'
            msg = msg + imc + res
            mostraResultado(msg, cor.NORMAL)
        }else if(imc >= 25 && imc <= 29.9){
            res = ' (Sobrepreso)'
            msg = msg + imc + res
            mostraResultado(msg, cor.SOBREPESO)
        }else if(imc >= 30 && imc <= 34.9){
            res = '( Obesidade grau I)'
            msg = msg + imc + res
            mostraResultado(msg, cor.OBESIDADEI)
        }else if(imc >= 35 && imc >= 39.9){
            res = '( Obesidade grau II)'
            msg = msg + imc + res
            mostraResultado(msg, cor.OBESIDADEII)
        }else{
            res = '( Obesidade grau III)'
            msg = msg + imc + res
            mostraResultado(msg, cor.OBESIDADEIII)
        }
    })
}

function mostraResultado(msg, cor){
    let resultado = window.document.querySelector('#resultado')
    resultado.innerHTML = ''
    let mensagem = document.createTextNode(msg)
    resultado.appendChild(mensagem)
    resultado.style.backgroundColor = cor
    resultado.style.display = 'block'
}