<?php include "./view/estrutura/cabecalho.php";

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

    <?php
        /* Barra de Navegação e Menu móvel */
        REQUIRE "./view/estrutura/sidenav-e-menuMobile.php"; 
    ?>
    <main>
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
                                        <?php $dados1 = ['id_filme'=>$info->id, 'id_usuario'=> $_SESSION['id_usuario']];?>
                                            <a href="#" class="material-icons white-text hoverable" id="save">save</a>
                                                <label class="label-icon white-text" for="save"><b>Assistir Mais Tarde</b></label>
                                            <button class="btn-fav-detalhes" id="favorito" data-id="<?= urlencode(serialize($dados1)) ?>"><i class="material-icons red-text"><?= ($controller->controlVerificaFavorito($dados)) ? "favorite" : "favorite_border" ?></i></button>
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

        <?php
            /* Mostra grid de filmes*/ 
            REQUIRE "./view/estrutura/gridFilmes.php"; 
        ?>
        
    <ul class="pagination container">
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
            <?php for($cont = 1; $cont<$num_paginas; $cont++) : ?>
                <li class="waves-effect" active><a class="active" href="/inicio?pagina=<?=$cont?>"><?=$cont?></a></li>
            <?php endfor?>
        <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    </ul>

    <?= Mensagem::mostrar(); ?>
    </main>

    <?php 
        include "./view/estrutura/rodape.php";
    ?>

</body>

<script>
    //Favoritar
    document.querySelectorAll(".btn-fav, .btn-fav-detalhes").forEach(btn => {
        btn.addEventListener("click", e => { //Mudança do texto do html para modificar o ícone
            const id = btn.getAttribute("data-id") //informação do id do botão clicado
            console.log(id)
            fetch(`/favoritar/${id}`) //faz a solicitação favorite
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
                    console.log(response+"")
                    M.toast({
                        html: "Erro ao Favoritar"
                    })
                })
        });
    });
</script>