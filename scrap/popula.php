<?php
    //============================DADOS DO USUÁRIO DO SIGAA - PARA PODER LOGAR NO SIGAA=========================
    $GLOBALS['usuario_sigaa'] = ''; //ex.: paulo.moraes
    $GLOBALS['senha_sigaa'] = '';
    //==========================================================================================================


    //============================DADOS DO BANCO DE DADOS - ONDE AS INFORMAÇÕES SERÃO GUARDADAS=================
    $host = ''; //ex.: 127.0.0.1 
    $nomeDoBanco = '';//ex.: meuBanco
    $usuario = ''; //ex.: root
    $senha = '';

    $_GLOBALS['host'] = $host;
    $_GLOBALS['nomeDoBanco'] = $nomeDoBanco;
    $_GLOBALS['usuario'] = $usuario;
    $_GLOBALS['senha'] = $senha;
    //==========================================================================================================
    function login($url,$data){
        $fp = fopen("cookie.txt", "w");
        fclose($fp);
        $login = curl_init();
        curl_setopt($login, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($login, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($login, CURLOPT_TIMEOUT, 40000);
        curl_setopt($login, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($login, CURLOPT_URL, $url);
        curl_setopt($login, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($login, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($login, CURLOPT_POST, TRUE);
        curl_setopt($login, CURLOPT_POSTFIELDS, $data);
        ob_start();
        return curl_exec ($login);
        ob_end_clean();
        curl_close ($login);
        unset($login);    
    }                   
 
    function recortaPagina($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        return curl_exec ($ch);
        ob_end_clean();
        curl_close ($ch);
    }
 
    function post_data($site,$data){
        $datapost = curl_init();
        $headers = array("Expect:");
        curl_setopt($datapost, CURLOPT_URL, $site);
        curl_setopt($datapost, CURLOPT_TIMEOUT, 40000);
        curl_setopt($datapost, CURLOPT_HEADER, TRUE);
        curl_setopt($datapost, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($datapost, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($datapost, CURLOPT_POST, TRUE);
        curl_setopt($datapost, CURLOPT_POSTFIELDS, $data);
        curl_setopt($datapost, CURLOPT_COOKIEFILE, "cookie.txt");
        ob_start();
        return curl_exec ($datapost);
        ob_end_clean();
        curl_close ($datapost);
        unset($datapost);    
    }

    function linha(&$string){
        $i = 0;
        $entrouNoIf = false;
        $vezes = 0;
        do{
            if($string[$i].$string[$i+1].$string[$i+2].$string[$i+3] == '<td>'){
                $j = $i + 4;
                while($string[$j] != '<'){
                    $linha[] = $string[$j];
                    $j++;
                }
                $linha[] = "*";
                $vezes++;
                $i = $j;
                $entrouNoIf = true;
            }
            if($entrouNoIf == false){
                $i++;
            }else{
                $entrouNoIf = false;
            }
            if($vezes == 7){
                break;
            }
        }while($i <= count($string));    
        array_pop($linha); 
        //agora, eliminar da string original o que já foi transformado em linha
        //e será retornado. O que foi transformado em linha é o conteúdo até 
        //a posição $i da string. 
        $string = array_slice($string, $i);
        //retorna a linha
        return $linha;
    }

    function insereAluno($aluno, $idPagina){
        if(count($aluno) == 7){
            //$aluno[2] -> nome
            //$aluno[1] -> matrícula
            //$aluno[6] -> cr
            //$aluno[3] -> curso 
            if(verificaSeAlunoEstaCadastrado($aluno[1]) != 1){
                cadastraAluno($aluno[2], $aluno[1], $aluno[3]);
            }
            cadastraCr($idPagina, $aluno[6], $aluno[1], pegaDataDeHoje());
        }
    }

    function coletaDados($idPagina){
        if(verificaSeFoiIdConsultada($idPagina) == 1){
            echo "Página já coletada!!";
        }else{
            $dados = "user.login=".$GLOBALS['usuario_sigaa']."&user.senha=".$GLOBALS['senha_sigaa'];
            login("https://sigaa.ufma.br/sigaa/logar.do?dispatch=logOn", $dados);
            $pagina = recortaPagina("https://sigaa.ufma.br/sigaa/relatorioProcessamento?idTurma=$idPagina");

            if ($pagina[0].$pagina[1] == '<s'){
                echo "TURMA INEXISTENTE!";
            }else{
                cadastraAcesso($idPagina, pegaDataDeHoje());
                $pagina = str_split($pagina); //transforma a string em um array
                $j = 0;
                $vetor = [];
                do{
                    if ($pagina[$j].$pagina[$j+1].$pagina[$j+2].$pagina[$j+3].$pagina[$j+4] == '<td>1'){
                        while($pagina[$j+1].$pagina[$j+2].$pagina[$j+3].$pagina[$j+4].$pagina[$j+5].$pagina[$j+6].$pagina[$j+7].$pagina[$j+8] != '</table>'){
                            $vetor[] = $pagina[$j++];
                        }
                    }
                    $j++;
                }while($j <= count($pagina));
            

                while(count($vetor) > 3){
                    $aluno = explode("*", implode(linha($vetor)));
                    insereAluno($aluno, $idPagina);                    
                }
            }
        }

    }

    if(isset($_GET['idPagina']) and !empty($_GET['idPagina'])){
       coletaDados($_GET['idPagina']);
    }


   //FUNÇÕES DO BANCO DE DADOS E UTILITÁRIAS

   function verificaSeFoiIdConsultada($idPagina){
        $conexao = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $sql = "SELECT * FROM ACESSO WHERE idTurma = '$idPagina'";
        $consulta = $conexao->prepare($sql);
        $consulta->execute();
        if($consulta->rowCount() > 0){
            return 1;
        }
        return 0;
   } 

   function verificaSeAlunoEstaCadastrado($matricula){
        $conexao = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $sql = "SELECT * FROM ALUNO WHERE matricula = '$matricula'";
        $consulta = $conexao->prepare($sql);
        $consulta->execute();
        if($consulta->rowCount() > 0){
            return 1;
        }
        return 0;
   }

   function cadastraAluno($nome, $matricula, $curso){
        $conexao = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $sql = "INSERT INTO ALUNO (nome, matricula, curso) VALUES ('$nome', '$matricula', '$curso')";
        $consulta = $conexao->prepare($sql);
        $consulta->execute();
   }

   function cadastraCr($pagina, $coeficiente, $matricula, $dataInsercao){
        $conexao = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $sql = "INSERT INTO COEFICIENTE (pagina, coeficiente, matricula, dataInsercao) VALUES ('$pagina', '$coeficiente', '$matricula', '$dataInsercao')";
        $consulta = $conexao->prepare($sql);
        $consulta->execute();
   }

   function cadastraAcesso($idTurma, $dataAcesso){
        $conexao = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $sql = "INSERT INTO ACESSO (idTurma, dataAcesso) VALUES ('$idTurma', '$dataAcesso')";
        $consulta = $conexao->prepare($sql);
        $consulta->execute();
   }

   function pegaDataDeHoje(){
        date_default_timezone_set('America/Sao_Paulo');
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        return $ano."-".$mes."-".$dia;   
   }

   function getDados($matricula){
        $pdo = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['nomeDoBanco'], $GLOBALS['usuario'], $GLOBALS['senha']);
        $consulta = $pdo->query("SELECT nome, curso FROM ALUNO WHERE matricula = '$matricula'");
        $consulta = $consulta->fetch(PDO::FETCH_ASSOC);
        $dados = [
            'nome' => $consulta['nome'],
            'curso' => $consulta['curso'],
            'coeficiente' => 0,
            'dataInsercao' => '0'
        ];

        $consulta = $pdo->query("SELECT coeficiente, dataInsercao FROM COEFICIENTE WHERE matricula = '$matricula' ORDER BY dataInsercao DESC");
        $consulta = $consulta->fetch(PDO::FETCH_ASSOC);

        $dados['coeficiente'] = $consulta['coeficiente'];
        $dados['dataInsercao'] = $consulta['dataInsercao'];
        
        return $dados;
    }
?>