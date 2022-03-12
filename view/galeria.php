<?php include "cabecalho.php";
?>

<?php

require_once "./util/mensagem.php";
session_start();
$filmesController = new FilmesController();
$destaques = $filmesController->destaques();

if (!isset($_SESSION['busca']) or ($_SESSION['busca'] == "")) {
    $filmes = $_SESSION['filmes'];
    $num_paginas = $filmes[0];
} else {
    $filmes = $_SESSION['buscaRetornada'];
}

?>

<body class="purple darken-1">
    <main>
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
                            <li><a href="/syscontrol">Painel de Controle</a></li>
                        <?php }?>
                    </ul>  

                <?php } else { ?>
                    <li><a href="/login">Entrar</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <?php 
    if ($_SESSION['usuario'] != "") { ?>
        <!--Verifica se usuario existe-->
        <ul class="sidenav  purple lighten-3" id="mobile-demo">
        <li class="center purple darken-2"><?= $_SESSION['usuario'] ?></li>
            <li><a href="/favoritos">Favoritos</a></li>
            <li><a href="/syscontrol">Controle</a></li>
            <li><a onclick="sair()" href="#">Sair</a></li>
        </ul>
    <?php } else { ?>
        <ul class="sidenav purple lighten-3" id="mobile-demo">
            <li class="purple darken-2"><a href="/login">Entrar</a></li>
        </ul>
    <?php } ?>
    
    <div class="carousel">
        <?php foreach($destaques as $destaque) :?>
            <a class="carousel-item hoverable" href="/assistir/<?= str_replace(' ', '-', $destaque->titulo) . "?id=" . ($destaque->id) ?>">
                <img src="<?= (str_contains($destaque->img_wide_1, 'imagens/posters')) ?  '/' . $destaque->img_wide_1 : $destaque->img_wide_1 ?>">
                <h3><?= $destaque->titulo ?></h3>
            </a>
        <?php endforeach ?>
    </div>       

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

    <!-- <div class="container"> -->
    <div class="row poster-filmes">
        <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>" ?>
        <?php $c = 0;
        foreach ($filmes as $filme) : ?>
            <!--Percorre filme a filme-->
            <?php if (!is_int($filme)) : ?>
                <!--Verifica filme ou int-->
                <?php $c++ ?>

                <div class="col s12 m6 l1 x1" id="card-filme">
                    <!--Define tamanho dos cards de acordo com o tamanho da coluna-->

                    <!--Posters -->
                    <div class="card hoverable">
                        <a href="/assistir/<?= str_replace(' ', '-', $filme->titulo) . "?id=" . ($filme->id) ?>">
                            <div class="card-image" id="card-imagem">
                                <img class="activator" src="<?= (str_contains($filme->poster, 'imagens/posters')) ?  '/' . $filme->poster : $filme->poster //Verifica se é url ou diretorio 
                                                            ?>">
                                <!-- Título  -->
                                <span class="card-title" id="titulo-content"><?= $filme->titulo ?></span>
                            </div>
                        </a>
                        <!-- Verifica usuário -->
                        <?php if ($_SESSION['usuario'] != "") { ?>
                            <div>
                                <!-- Verificação de usuário logado-->
                                <?php $dados = ['id_filme' => $filme->id, 'id_usuario' => $_SESSION['id_usuario']]; ?>
                                <!--Botão favorito -->
                                <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red" data-id="<?= urlencode(serialize($dados)) ?>">
                                    <i class="material-icons"><?= ($controller->controlVerificaFavorito($dados)) ? "favorite" : "favorite_border" ?></i>
                                    <!--ícone Favorito-->
                                </button>
                            </div>
                        <?php }
                        if ($_SESSION['usuario'] != "" && $_SESSION['admin'] == true) { ?>
                            <!--Botão Editar -->
                            <div>
                                <a href="/editar/?id=<?= $filme->id ?>">
                                    <button class="btn-edit btn-floating halfway-fab waves-effect waves-light black">
                                        <i class="material-icons">edit</i>
                                        <!--ícone Edit-->
                                    </button></a>
                            </div>
                        <?php } ?>
                        <div class="btn-nota halfway-fab valign-wrapper">
                            <!--Classe para alinhamento de elementos-->
                            <i class="imdb material-icons black-text" id="nota">star</i><b><?= $filme->nota ?><a href="https://www.imdb.com/"> IMDB</a></b>
                        </div>
                    </div>
                </div>

                <?php if ($c == 14) : ?>
                    <div class="parallax-meio parallax-container">
                        <div class="parallax"><img src="https://www.themoviedb.org/t/p/original/3dUByTea97X3XzznN8ZPFX9c7J7.jpg"></div>
                    </div>
                    <?php $c = 0; ?>
                <?php endif ?>

            <?php endif ?>
        <?php endforeach ?>
    </div>
   
    <ul class="pagination container center">
        <?php if ($_GET['pagina'] >= 2) { ?>
            <li class="waves-effect"><a href="/inicio?pagina=<?= $_GET['pagina']-1 ?>"><i class="material-icons white-text">chevron_left</i></a></li>
        <?php }else{ ?>
            <li class="disable"><a href="#"><i class="material-icons">chevron_left</i></a></li>
        <?php } ?>
        <?php for ($cont = 1; $cont <= $num_paginas; $cont++) : ?>
            <?php if ($_SERVER["REQUEST_URI"] == "/inicio?pagina=$cont") {?>
                <li class="waves-effect active purple lighten-3"><a href="/inicio?pagina=<?= $cont ?>"><?= $cont ?></a></li>
            <?php }else{ ?>
                <li class="waves-effect"><a href="/inicio?pagina=<?= $cont ?>"><?= $cont ?></a></li>
            <?php }?>
        <?php endfor ?>
        <?php if ($_GET['pagina'] < $num_paginas) { ?>
            <li class="waves-effect"><a href="/inicio?pagina=<?= $_GET['pagina']+1 ?>"><i class="material-icons white-text">chevron_right</i></a></li>
        <?php }else{ ?>
            <li class="waves-effect"><a href="#"><i class="material-icons white-text">chevron_right</i></a></li>
        <?php } ?>
    </ul>
    <?= Mensagem::mostrar(); ?>
    </main>

    

     <script>
        //Favoritar
        document.querySelectorAll(".btn-fav").forEach(btn => {
            btn.addEventListener("click", e => { //Mudança do texto do html para modificar o ícone
                const id = btn.getAttribute("data-id") //informação do id do botão clicado
                //console.log(id);
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
        //Delete
        document.querySelectorAll(".btn-delete").forEach(btn => {
            btn.addEventListener("click", e => {
                const id = btn.getAttribute("data-id") //informação do id do botão clicado
                const requestConfig = {
                    method: "DELETE",
                    headers: new Headers()
                }
                fetch(`/filmes/${id}`, requestConfig) //faz a solicitação de deletar
                    .then(response => response.json()) //quando tiver a resposta, converto para json
                    .then(response => { //após a conversão
                        if (response.success === "ok") { //Verifico o atributo success, se OK
                            const card = btn.closest(".col") //pegar elemento mais próximo do botão
                            card.classList.add("fadeOut") //Adiciona classe com efeito de FadeOut
                            setTimeout(() => card.remove(), 1000) //executa a função após certo tempo
                        }
                    })
                    .catch(error => {
                        M.toast({
                            html: "Erro ao Apagar"
                        })
                    })
            });
        });
    </script> 

    <?php
    include "rodape.php";
    ?>
</body>

</html>