<?php
    session_start();
    set_time_limit(0);
    if(isset($_POST['matricula']) and !empty($_POST['matricula'])){
        $matricula = $_POST['matricula'];
        if(strlen($matricula) != 10){
            $_SESSION['erroComprimentoMatricula'] = 1;
            header("Location: index.php");
            exit();
        }else{
            $flag = false;
            require 'popula.php';
            if(verificaSeAlunoEstaCadastrado($_POST['matricula']))
                $flag = true;
            if(!$flag){
                $_SESSION['naoEncontrado'] = 1;
                header("Location: index.php");
                exit();
            }
            $encontrou = true;
            $dados = getDados($_POST['matricula']);
        }
    }else{ 
        
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Procura CR</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap');

        body{
            background-color: #adadad;
            font-family: 'Roboto Condensed', sans-serif;
            
        }   
        form{
            border: 0.4px solid #ffc23d;
            padding: 1rem;
            border-radius: 4%;
            background-color: white;
            box-shadow: 0px 0px 5px white;
        }
        #avisoMatriculaErrada{
            display: <?php
                if(isset($_SESSION['erroComprimentoMatricula']) and $_SESSION['erroComprimentoMatricula'] == 1){
                    echo "block;";
                    unset($_SESSION['erroComprimentoMatricula']);
                }else{
                    echo "none;";
                }
            ?>
        }

        #avisoNaoEncontrado{
            display: <?php
                if(isset($_SESSION['naoEncontrado']) and $_SESSION['naoEncontrado'] == 1){
                    echo "block;";
                    unset($_SESSION['naoEncontrado']);
                }else{
                    echo "none;";
                }
            ?>
        }

        #btnPergunta:hover{
            cursor: pointer;
        }
        
        #div-info{
            display: <?php
                if(isset($encontrou) and $encontrou == true){
                    echo "block;";
                    unset($encontrou);
                }else{
                    echo "none;";
                }
            ?>
        }

    </style>
    <script>
        function verificaSubmissao(){
            if(!window.document.querySelector('#textoMatricula').classList.contains('btn-warning')){
                return false;
            }
        }


        window.onload = function(){
            let formulario = document.querySelector('form');
            let altura = ((window.innerHeight / 2) - 100);
            formulario.style.marginTop =  altura + 'px';
        }

        function atualizaBotaoVerificar(e){
            let formulario = window.document.querySelector('#textoMatricula')
            if((e.value).toString().length >= 10){
                if(formulario.classList.contains('btn-secondary')){
                    formulario.classList.remove('btn-secondary')
                }
                formulario.classList.add('btn-warning')            
            }else{
                if((e.value).toString().length < 10){
                    if(formulario.classList.contains('btn-warning'))
                        formulario.classList.remove('btn-warning')
                }
                formulario.classList.add('btn-secondary')
            }
        }
        
    </script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-4 col-xs-4 col-lg-4 col-md-4">
            <form method="POST" onsubmit="return verificaSubmissao()">
                <div class="form-group" style="display: flex !important; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between;" class="mark mb-2">
                        <label for="exampleInputEmail1">Buscador de CR</label>
                        <i id="btnPergunta" class="fa fa-question-circle" data-toggle="modal" data-target="#exampleModalScrollable"></i>
                    </div>
                    <input type="text" maxlength="10"  autocomplete="off" class="form-control" name="matricula" aria-describedby="emailHelp" placeholder="2001046333" required onkeyup="atualizaBotaoVerificar(this)" onpaste="return false">
                    <small id="emailHelp" class="form-text text-muted">O número acima é um exemplo hipotético de matrícula.</small>
                </div>
                <div class="form-group">
                    <div class="alert alert-danger" role="alert" id="avisoMatriculaErrada">
                        A matrícula deve ter 10 caracteres!
                    </div>
                    <div class="alert alert-danger" role="alert" id="avisoNaoEncontrado">
                        Os dados desta matrícula ainda não foram coletados!
                    </div>
                </div>
                <button type="submit" class="btn-secondary btn w-100" id="textoMatricula">Verificar</button>
                <div id="div-info" class="form-group" style="margin-bottom: 0 !important; margin-top: 1rem !important; border: 1px solid green; border-radius: 0.2rem;">
                    <strong>NOME: </strong><?php echo $dados['nome'];?></br>
                    <strong>CR MAIS RECENTE: </strong><?php echo $dados['coeficiente'];?></br>
                    <strong>CURSO:</strong> <?php echo $dados['curso'];?></br>
                    COEFICIENTE COLETADO EM: <strong><?php echo $dados['dataInsercao'];?></strong>
                </div>
                </form>
            </div>
        <div>
    </div>

  


<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable">
  Launch demo modal
</button> -->

<!-- Modal -->
<div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalScrollableTitle">Como funciona?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Aqui ficará o texto explicando o funcionamento dessa ferramenta.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Entendi</button>
    </div>
    </div>
  </div>
</div>

</body>

<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery-3.4.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min-4.0.js"></script>


</html>