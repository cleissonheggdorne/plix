<?php include "cabecalho.php";
?>

<?php
//session_destroy();
require "./util/mensagem.php";
session_start();


$controller = new FilmesController();
$filmes = $controller->fav($_SESSION['id_usuario']);


?>

<body>
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
                            <li><a href="/syscontrol">Controle</a></li>
                        <?php } ?>
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
        <ul class="sidenav purple lighten-3" id="mobile-demo">
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

    <div class="container">
        <div class="row">
            <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes favoritados</p>" ?>
            <?php
            foreach ($filmes as $filme) : ?>
                <!--Percorre filme a filme-->
                <?php if (!is_int($filme)) : ?>
                    <!--Verificação de Número de páginas-->


                    <div class="col s12 m6 l4 xl3" id="card-filme">
                        <!--Define tamanho dos cards de acordo com o tamanho da coluna-->

                        <!--Posters -->
                        <div class="card">
                            <a href="/assistir/<?= str_replace(' ', '-', $filme->titulo) . "?id=" . ($filme->id) ?>">
                                <div class="card-image" id="card-imagem">
                                    <img class="activator" src="<?= (str_contains($filme->poster, 'imagens/posters')) ?  '/' . $filme->poster : $filme->poster //Verifica se é url ou diretorio 
                                                                ?>">

                                    <!-- Título  -->
                                    <span class="card-title" id="titulo-content"><?= $filme->titulo ?></span>

                                </div>
                            </a>

                            <?php if ($_SESSION['usuario'] != "") { ?>
                                <div>
                                    <!-- Verificação de usuário logado e permissão de admin 1=true e 0=false-->
                                    <?php $dados = ['id_filme' => $filme->id, 'id_usuario' => $_SESSION['id_usuario']]; ?>
                                    <!--Botão favorito -->
                                    <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red" data-id="<?= urlencode(serialize($dados)) ?>">
                                        <i class="material-icons"><?= ($controller->controlVerificaFavorito($dados)) ? "favorite" : "favorite_border" ?></i>
                                        <!--ícone Favorito-->
                                    </button>
                                </div>
                            <?php } ?>

                            <div class="btn-nota halfway-fab valign-wrapper">
                                <!--Classe para alinhamento de elementos-->
                                <i class="imdb material-icons black-text" id="nota">star</i><b><?= $filme->nota ?><a href="https://www.imdb.com/"> IMDB</a></b>
                            </div>

                        </div>

                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>

    <ul class="pagination container">
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
        <?php for ($cont = 1; $cont < $num_paginas; $cont++) : ?>
            <li class="waves-effect" active><a class="active" href="/inicio?pagina=<?= $cont ?>"><?= $cont ?></a></li>
        <?php endfor ?>
        <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    </ul>
    </main>
    <?= Mensagem::mostrar(); ?>

    <!--Sair do Login -->
    <script>
        function sair() {
            var confirmar = confirm("Deseja Sair?");
            if (confirmar == true) {
                window.location.href = "/sair";
            }
        }
    </script>

    <script>
        //DESFavoritar
        document.querySelectorAll(".btn-fav").forEach(btn => {
            btn.addEventListener("click", e => { //Mudança do texto do html para modificar o ícone
                const id = btn.getAttribute("data-id") //informação do id do botão clicado
                fetch(`/favoritar/${id}`) //faz a solicitação delete
                    .then(response => response.json()) //quando tiver a resposta, converto para json
                    .then(response => { //após a conversão
                        if (response.success === "ok") { //Verifico o atributo success, se OK
                            btn.querySelector("i").innerHTML = "favorite_border"
                            const card = btn.closest(".col") //pegar elemento mais próximo do botão
                            card.classList.add("fadeOut") //Adiciona classe com efeito de FadeOut
                            setTimeout(() => card.remove(), 1000) //executa a função após certo tempo  
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

    <script>
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