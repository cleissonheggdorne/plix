<?php include "cabecalho.php";
?>

<?php

require_once "./util/mensagem.php";
session_start();
$filmesController = new FilmesController();
$destaques = $filmesController->destaques();
//$idDestaque;


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
                            <?php } ?>
                        </ul>

                    <?php } else { ?>
                        <li><a href="/login">Entrar</a></li>
                    <?php } ?>
                </ul>
            </div>

            <div class="nav-content">
                <ul class="tabs tabs-transparent">
                    <li class="tab"><a class="modal-trigger" href="#modal-novo-filme">Novo Filme</a></li>
                    <li class="tab"><a class="active" href="#test2">Importar Arquivo</a></li>
                    <li class="tab disabled"><a href="#test3">Editar Filmes</a></li>
                    <li class="tab"><a class="modal-trigger" href="#modal-edit-slides">Editar Slides</a></li>
                    <li class="tab"><a href="#test4">Editar Parallax</a></li>
                </ul>
            </div>
        </nav>
        <?php
        if ($_SESSION['usuario'] != "") { ?>
            <!--Verifica se usuario existe-->
            <ul class="sidenav  purple lighten-3" id="mobile-demo">
                <li class="center purple darken-2"><?= $_SESSION['usuario'] ?></li>
                <li><a href="/favoritos">Favoritos</a></li>
                <?php if ($_SESSION['usuario'] != "" && $_SESSION['admin'] == true) { ?>
                    <li><a href="/syscontrol">Painel de Controle</a></li>
                <?php } ?>
                <li><a onclick="sair()" href="#">Sair</a></li>
            </ul>
        <?php } else { ?>
            <ul class="sidenav purple lighten-3" id="mobile-demo">
                <li class="purple darken-2"><a href="/login">Entrar</a></li>
            </ul>
        <?php } ?>

        <?php ?>
        <div class="carousel">
            <?php foreach ($destaques as $destaque) : ?>
                <a class="carousel-item hoverable" href="/assistir/<?= str_replace(' ', '-', $destaque->titulo) . "?id=" . ($destaque->id) ?>">
                    <img src="<?= (str_contains($destaque->img_wide_1, 'imagens/posters')) ?  '/' . $destaque->img_wide_1 : $destaque->img_wide_1 ?>">
                    <h3><?= $destaque->titulo ?></h3>
                    <!--Botão Editar Slides -->
                    <button id="edit-slide" onclick="abreModal();" data_filme_id="<?= $destaque->id ?>" data-target="modal-edit-slides" class="btn-edit btn-floating btn modal-trigger halfway-fab waves-effect waves-light black">
                        <i class="material-icons">edit</i>
                    </button>
                </a>
            <?php endforeach ?>
        </div>

        <!-- Modal Para Cadastro de Novo Filme -->
        <div class="row">
            <!-- Modal Structure -->
            <div id="modal-novo-filme" class="modal modal-fixed-footer">

                <!--Início Form-->
                <div class="modal-content">

                    <div class="card-content white-text">
                        <span class="card-title black-text">Cadastrar Filme</span>
                        <div class="row">
                            <div class="col s12">
                                <div class="row">
                                    <!-- <form method="GET"> -->
                                    <div class="input-field col s12">
                                        <i class="material-icons">search</i>
                                        <input type="text" name="titulo-para-salvar" id="titulobuscado" class="autocomplete" value="" onkeyup="pesquisaTitulos(this.value)">
                                        <label for="titulobuscado">Pesquise Um Filme Para Preencher as Informações Automaticamente</label>
                                        <h1 id="res"></h1>
                                        <button class="waves-effect waves-light btn purple">Auto Preencher</a>
                                            <!-- <span id="resultado_pesquisa"></span> -->
                                    </div>
                                    <!-- </form> -->
                                </div>
                            </div>
                        </div>

                        <form method="POST" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col s12 m12 l12">
                                    <div class="col s6 m6 l6">
                                        <!--input do título -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="titulo" type="text" class="validate" name="titulo" value="" required>
                                                <label for="titulo">Título do Filme</label>
                                            </div>
                                        </div>

                                        <!--input da sinopse -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <textarea id="sinopse" class="materialize-textarea" name="sinopse" required></textarea>
                                                <label for="sinopse">Sinopse</label>
                                            </div>
                                        </div>

                                        <!--input da nota -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s3">
                                                <input id="nota" type="number" step=".1" min=0 max=10 class="validate" value="" required name="nota">
                                                <label for="nota">Nota</label>
                                            </div>
                                        </div>

                                        <!--input da capa do filme-->
                                        <div class="row input-cadastro-filme file-field input-field">
                                            <div class="btn purple lighten-2 black-text">
                                                <span>Capa</spam>
                                                    <input type="file" name="poster_file">
                                            </div>
                                            <div class="input-field col s12">
                                                <input class="file-path validate" id="url-poster" type="text" name="poster" value="" required>
                                                <label for="url-poster">Url Poster</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col s6 m6 l6">
                                        <!--input do trailer -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="chave-trailer" type="text" class="validate" name="trailer" value="" required>
                                                <label for="chave-trailer">Link Trailer</label>
                                            </div>
                                        </div>

                                        <!--input do player -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="url" type="text" class="validate" name="player">
                                                <label for="url">URL Player do Filme</label>
                                            </div>
                                        </div>
                                        <!--input do background 1 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="back-1" type="text" class="validate" name="back_1" value="" required>
                                                <label for="back-1">Plano de Fundo 1</label>
                                            </div>
                                        </div>
                                        <!--input do background 2 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="back-2" type="text" class="validate" name="back_2">
                                                <label for="back-2">Plano de Fundo 2</label>
                                            </div>
                                        </div>
                                        <!--input do background 3 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input id="back-3" type="text" class="validate" name="back_3">
                                                <label for="back-3">Plano de Fundo 3</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <a class="waves-effect waves-light btn grey" href="/syscontrol">Cancelar</a>
                    <button type="submit" class="waves-effect waves-light btn purple">Salvar</a>
                </div>
                </form>
            </div>
        </div>

        <!-- Modal Editar Slides -->
        <div class="row">
            <!-- Modal Structure -->
            <div id="modal-edit-slides" class="modal modal-fixed-footer" data_id_anterior="<?= $_POST['filme_id'] ?>">

                <?php

                require_once "./util/mensagem.php";
                session_start();

                if (!isset($_SESSION['busca']) or ($_SESSION['busca'] == "")) {
                    $filmes = $_SESSION['filmes'];
                    $num_paginas = $filmes[0];
                } else {
                    $filmes = $_SESSION['buscaRetornada'];
                }

                ?>
                <input id="input_modal_id_anterior" type="hidden"></input>

                <!--Início Form-->
                <div class="modal-content">
                    <div class="card-content white-text">
                        <span class="card-title black-text">Selecionar Filme </span>
                        <div class="row">
                            <div class="col s12">
                                <div class="row">
                                    <!-- <form method="GET"> -->
                                    <div class="input-field col s12">
                                        <i class="material-icons">search</i>
                                        <input type="text" name="busca" id="titulobuscadodestaque" class="autocomplete" value="" onkeyup="buscaDestaque(this.value)">
                                        <label for="titulobuscado">Pesquise Um Filme Para Preencher as Informações Automaticamente</label>
                                    </div>
                                    <!-- </form> -->
                                </div>
                            </div>
                        </div>
                        <!-- <input id="id_slide_anterior" type="hidden" name="filme_slide_id" /> -->

                        <!-- <div class="container"> -->
                        <div class="row poster-filmes">
                            <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>" ?>
                            <?php $c = 0;
                            foreach ($filmes as $filme) : ?>
                                <!--Percorre filme a filme-->
                                <?php if (!is_int($filme)) : ?>
                                    <!--Verifica filme ou int-->
                                    <?php $c++ ?>

                                    <div class="col s2 m2 l2 xl2" id="card-filme">
                                        <!--Define tamanho dos cards de acordo com o tamanho da coluna-->

                                        <!--Posters -->
                                        <div class="card hoverable">

                                            <div class="card-image" id="card-imagem">
                                                <img id="img-slide" onclick="selecionaSlide(<?= $filme->id ?>)" class="activator" src="<?= (str_contains($filme->poster, 'imagens/posters')) ?  '/' . $filme->poster : $filme->poster //Verifica se é url ou diretorio 
                                                                                                                                        ?>">
                                                <!-- Título  -->
                                                <span class="card-title" id="titulo-content"><?= $filme->titulo ?></span>
                                            </div>
                                            <!-- </a> -->

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
                </div>
                <div class="modal-footer">
                    <a class="waves-effect waves-light btn grey" href="/syscontrol">Fechar</a>
                </div>
            </div>

        </div> 

    <!-- Modal Editar Filme  -->
    <div class="row">
            <!-- Modal Structure -->
            <div id="modal-editar-filme" class="modal modal-fixed-footer">
                
                <!-- <input id="input_modal_id_filme" type="hidden"></input> -->
                
                <div class="modal-content">

                    <div class="card-content white-text">
                        <span class="card-title black-text">Editar Filme</span>

                        <form method="POST" enctype="multipart/form-data">
                            <input name="id" id="id-edt" type="hidden" value=""></input>
                            <div class="row">
                                <div class="col s12 m12 l12">
                                    <div class="col s6 m6 l6">
                                        <!--input do título -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="titulo-edt" type="text" class="validate" name="titulo" value="" required>
                                                <label for="titulo-edt">Título do Filme</label>
                                            </div>
                                        </div>

                                        <!--input da sinopse -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <textarea placeholder="" id="sinopse-edt" class="materialize-textarea" name="sinopse" value="" required></textarea>
                                                <label for="sinopse-edt">Sinopse</label>
                                            </div>
                                        </div>

                                        <!--input da nota -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s3">
                                                <input placeholder="" id="nota-edt" type="number" step=".1" min=0 max=10 class="validate" value="" required name="nota">
                                                <label for="nota-edt">Nota</label>
                                            </div>
                                        </div>

                                        <!--input da capa do filme-->
                                        <div class="row input-cadastro-filme file-field input-field">
                                            <div class="btn purple lighten-2 black-text">
                                                <span>Capa</spam>
                                                    <input type="file" name="poster_file">
                                            </div>
                                            <div class="input-field col s12">
                                                <input placeholder="" class="file-path validate" id="url-poster-edt" type="text" name="poster" value="" required>
                                                <label for="url-poster-edt">Url Poster</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col s6 m6 l6">
                                        <!--input do trailer -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="chave-trailer-edt" type="text" class="validate" name="trailer" value="" required>
                                                <label for="chave-trailer-edt">Link Trailer</label>
                                            </div>
                                        </div>

                                        <!--input do player -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="url-edt" type="text" class="validate" name="player">
                                                <label for="url-edt">URL Player do Filme</label>
                                            </div>
                                        </div>
                                        <!--input do background 1 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="back-1-edt" type="text" class="validate" name="back_1" value="" required>
                                                <label for="back-1-edt">Plano de Fundo 1</label>
                                            </div>
                                        </div>
                                        <!--input do background 2 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="back-2-edt" value="" type="text" class="validate" name="back_2">
                                                <label for="back-2-edt">Plano de Fundo 2</label>
                                            </div>
                                        </div>
                                        <!--input do background 3 -->
                                        <div class="row input-cadastro-filme">
                                            <div class="input-field col s12">
                                                <input placeholder="" id="back-3-edt" value="" type="text" class="validate" name="back_3">
                                                <label for="back-3-edt">Plano de Fundo 3</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="waves-effect waves-light btn purple">Salvar Alterações</a>
                </div>
                </form>
            </div>
        </div>
    

    <div class="fundo-pesquisa purple darken-1">
        <nav class="busca container purple lighten-3">
            <div class="nav-wrapper center">
                <form method="GET">
                    <div class="input-field  hoverable">
                        <input id="search" name="busca" type="search" value="<?= $_SESSION['busca'] ?>">
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
                                <!-- <a href="/editar/?id=<?= $filme->id ?>"> -->
                                    <button id="edit-filme" onclick="editarFilme(<?= $filme->id ?>)" data_filme_id="<?= $filme->id ?>" data-target="modal-editar-filme" class="btn modal-trigger btn-edit btn-floating halfway-fab waves-effect waves-light black">
                                        <i class="material-icons">edit</i>
                                        <!--ícone Edit-->
                                    </button>      
                                <!-- </a> -->
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

    </main>

    <?= Mensagem::mostrar(); ?>

    <script>
        //jquery 
        //Adiciona id destaque selecionado em input para posterior consulta
        $(document).ready(function() {
            $(document).on('click', '#edit-slide', function() {
                var filme_id = $(this).attr("data_filme_id")
                $('#input_modal_id_anterior').val(filme_id)
            })
        })

       // $(document).ready(function() {
         //   $(document).on('click', '#edit-filme', function() {
               // $('.modal').modal();
               //var instance = M.Modal.getInstance($('#modal-edit-slides'));
               //instance.open();
                //var filme_id = $(this).attr("data_filme_id");
                //console.log(filme_id);
               // $('#input_modal_id_filme').val(filme_id)

           // })
        //})
    </script>


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