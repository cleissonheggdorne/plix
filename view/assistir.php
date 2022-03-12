<?php include "cabecalho.php";

?>

<?php

require "./util/mensagem.php";
session_start();
unset($_SESSION['busca']);

$controller = new FilmesController();
$filmes = $controller->index(20, 0);

$dados = $controller->buscaInfoFilme(['pag_assistir'=>$_GET['id']]);
$infoFilme = $dados['dados'];
?>

<body>
<nav class="nav-extended purple darken-2">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <a href="/inicio" class="brand-logo left">PLIX</a>

            <a href="#" data-target="mobile-demo" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <!--Responsividade, esconde a barra quando a tela for média ou pequena-->
                <li class="active"><a href="/inicio">Galeria</a></li>
               
                <?php 
                if ($_SESSION['usuario'] != "") { ?>
                    <!--Verifica se usuario existe-->
                    
                  <a class='dropdown-trigger btn transparent' href='#' data-target='dropdown1'><?= $_SESSION['usuario'] ?></a>
                    <ul id='dropdown1' class='dropdown-content'>
                        <li><a onclick="sair()" href="#">Sair</a></li>
                        <li><a href="/favoritos">Favoritos</a></li>
                        <?php if ($_SESSION['usuario'] != "" && $_SESSION['admin'] == true) { ?>
                            <li><a href="/novo">Cadastrar Mídia</a></li>
                        <?php }?>
                    </ul>  

                <?php } else { ?>
                    <li><a href="/login">Entrar</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <div class="fundo-pesquisa purple darken-1">
        <nav class="busca container purple lighten-3">
            <div class="nav-wrapper center">
                <form method="GET">
                    <div class="input-field  hoverable">
                        <input id="search" name="busca" type="search" required value="<?= $_SESSION['busca'] ?>">
                        <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons">close</i>
                    </div>
                </form>
            </div>
        </nav>
    </div>
    <?php foreach ($infoFilme as $info) : ?>
    <div class="section apresentacao-filme purple darken-2" style="background-image: url('<?= $info->img_wide_1?>')">
        
        <div class="divider transparent"></div>
               
                    <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
                        <div class="row container valign-wrapper">
                            <div class="col s12 m6 l6 xl6">
                                <!-- Imagem do pôster-->
                                <div class="card-image assistir">
                                    <img class ="image-assistir" src="<?= (str_contains($info->poster, 'imagens/posters')) ?  '/' . $info->poster : $info->poster //Verifica se é url ou diretorio 
                                                ?>">
                                    <!--Aqui tem que entrar a imagem do pôster-->
                                </div>
                            </div>
                            <!--Sinopse -->
                            <div class="col s12 m6 l6 xl6">
                                <div class="col s12 m12 l12 xl12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="btn-nota-assistir halfway-fab valign-wrapper">
                                                <!--Classe para alinhamento de elementos-->
                                                <i class="imdb-assistir material-icons black-text" id="nota">star</i><b><?= $info->nota ?><a class="hoverable" href="https://www.imdb.com/"> IMDB</a></b>
                                            </div>    
                                            <span class="card-title activator grey-text text-darken-4"><?= $info->titulo ?>
                                                <?php if(strlen($info->sinopse)>400) { ?>
                                                    <i class="material-icons right">more_vert</i>
                                                <?php } ?>
                                                </span>
                                                    <p><?= (strlen($info->sinopse) >400)? substr($info->sinopse, 0, 400)."..." : $info->sinopse ?></p>
                                        </div>
                                   
                                        <!-- Verifica tamanho da Sinopse para mostrar restante -->
                                        <?php if(strlen($info->sinopse) > 400){?>
                                            <div class="card-reveal">
                                                <span class="card-title grey-text text-darken-4"><?= $info->titulo ?><i class="material-icons right">close</i></span>
                                                    <p><?= $info->sinopse ?></p>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                                <!-- Assistir mais tarde e Favorito -->
                                <div class="col s12 m12 l12 xl12">
                                        <!-- card com botões Favorito e Salvar -->
                                        <div class="card-panel purple darken-3 botoes-save-favorito">
                                        <div class="valign-wrapper">
                                            <a href="#" class="material-icons white-text hoverable" id="save">save</a>
                                                <label class="label-icon white-text" for="save"><b>Assistir Mais Tarde</b></label>
                                            <a href="#" class="material-icons red-text hoverable" id="favorito">favorite</a>
                                                <label class="label-icon white-text" for="favorito"><b>Favoritar</b></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endforeach ?>
            <div class="divider transparent"></div>
        </div>
    
    <!-- Sessão Trailer -->
    <div class="section apresentacao-trailer purple darken-2" style="background-image: url('https://www.themoviedb.org/t/p/original/190zDbmDGapsvbwOwC0uML2aKH7.jpg')">
        <div class="row">
            <div class="col s12 m6 l6 xl6 offset-m6 offset-l6 offset-xl6">
                <div class="video-container">
                    <iframe src="//www.youtube.com/embed/<?= $info->trailer ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessão Player -->
        <!-- <div class="divider"></div> -->
        <div class="section apresentacao-player purple darken-2">
            <div class="row">
                <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
                <div class="col s12 m6 l6 xl6">
                    <div class="video-container">
                        <iframe class="player" src="<?= $info->url ?>" type="video/mp4" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
        <div class="row">
            <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>" ?>
            <?php
            foreach ($filmes as $filme) : ?> <!--Percorre filme a filme-->
               <?php if (!is_int($filme)) :?> <!--Verificação de Número de páginas-->
                    
                
                <div class="col s12 m6 l4 xl3" id="card-filme">
                    <!--Define tamanho dos cards de acordo com o tamanho da coluna-->

                    <!--Posters -->
                    <div class="card">
                        <a href="/assistir/<?= str_replace(' ', '-', $filme->titulo) . "?id=" . ($filme->id) ?>">
                            <div class="card-image" id="card-imagem">
                                <img class="activator" src="<?= (str_contains($filme->poster, 'imagens/posters')) ?  '/' . $filme->poster : $filme->poster //Verifica se é url ou diretorio 
                                                            ?>">
                        
                                <!-- Título  -->
                                <span class="card-title" id="titulo-content"><?= $filme->titulo?></span>
                                
                            </div>
                        </a>

                        <?php if ($_SESSION['usuario'] != "") { ?>
                            <div>  <!-- Verificação de usuário logado e permissão de admin 1=true e 0=false-->
                                <?php $dados = ['id_filme'=>$filme->id, 'id_usuario'=> $_SESSION['id_usuario']];?>
                                <!--Botão favorito -->
                                <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red" data-id="<?= urlencode(serialize($dados)) ?>">
                                    <i class="material-icons"><?= ($controller->controlVerificaFavorito($dados)) ? "favorite" : "favorite_border" ?></i>
                                    <!--ícone Favorito-->
                                </button>
                            </div>
                        <?php } ?>
                            <div class="btn-nota halfway-fab valign-wrapper">
                                    <!--Classe para alinhamento de elementos-->
                                    <i class="imdb material-icons black-text" id="nota">star</i><b><?= $filme->nota ?><a href="https://www.imdb.com/">  IMDB</a></b>
                            </div>
                    </div>

                </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
    <ul class="pagination container">
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
            <?php for($cont = 1; $cont<$num_paginas; $cont++) : ?>
                <li class="waves-effect" active><a class="active" href="/inicio?pagina=<?=$cont?>"><?=$cont?></a></li>
            <?php endfor?>
        <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    </ul>

    <?= Mensagem::mostrar(); ?>
    <?php 
include "rodape.php";
?>
</body>

<script>
    //Favoritar
    document.querySelectorAll(".btn-fav").forEach(btn => {
        btn.addEventListener("click", e => { //Mudança do texto do html para modificar o ícone
            const id = btn.getAttribute("data-id") //informação do id do botão clicado
            fetch(`/favoritar/${id}`) //faz a solicitação delete
                .then(response => response.json()) //quando tiver a resposta, converto para json
                .then(response => { //após a conversão
                    if (response.success === "ok") { //Verifico o atributo success, se OK
                        if (btn.querySelector("i").innerHTML === "favorite") { //Faço a troca
                            btn.querySelector("i").innerHTML = "favorite_border"
                        } else {
                            btn.querySelector("i").innerHTML = "favorite"
                        }
                    }
                })
                .catch(error => {
                    M.toast({
                        html: "Erro ao Favoritar"
                    })
                })
        });
    });
</script>