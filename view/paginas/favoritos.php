<?php include "./view/estrutura/cabecalho.php";
?>

<?php
//session_destroy();
require "./util/mensagem.php";
session_start();


$controller = new FilmesController();
$filmes = $controller->fav(['pagina'=>'favoritos', 'id'=>$_SESSION['id_usuario']]);


?>

<body>
    <main>
    <?php
        /* Barra de Navegação e Menu móvel */
        REQUIRE "./view/estrutura/sidenav-e-menuMobile.php"; 
    ?>
    <section class='card-panel save-fav purple lighten-3'>
    <h4 class='subtitle card-panel purple darken-2'>Favoritos</h4>
    <div class="container">
        
        <div class="row">
            <?php if (!$filmes) echo "<p class='card-panel purple lighten-3'>Não há filmes favoritados</p>" ?>
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
        </section>
    </div>
    <div class="fundo-paginacao purple darken-1">
    <ul class="pagination container">
        <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
        <?php for ($cont = 1; $cont < $num_paginas; $cont++) : ?>
            <li class="waves-effect" active><a class="active" href="/inicio?pagina=<?= $cont ?>"><?= $cont ?></a></li>
        <?php endfor ?>
        <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
    </ul>
    </div>
    </main>
    <?= Mensagem::mostrar(); ?>
    
    <?php
        /* Barra de Navegação e Menu móvel */
        REQUIRE "./view/estrutura/rodape.php"; 
    ?>

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

</body>