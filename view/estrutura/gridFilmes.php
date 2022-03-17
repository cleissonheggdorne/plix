<!-- <div class="container"> -->
<div class="row poster-filmes">
        <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>" ?>
        <?php // $c = 0;
        //Percorre filme a filme
        foreach ($filmes as $filme) : ?>
           
            <?php if (!is_int($filme)) : ?>
                <!--Verifica filme ou int-->
                <?php //$c++ ?>

                <div class="col s12 m6 l1 xl1" id="card-filme">
                    <!--Define tamanho dos cards de acordo com o tamanho da coluna-->

                    <!--Posters -->
                    <div class="card">
                        <a href="/assistir/<?= str_replace(array(':',';', '?', ',', '.', '!'), '-', $filme->titulo) . "?id=" . ($filme->id) ?>">
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
                        <?php } ?>
                        <div class="btn-nota halfway-fab valign-wrapper">
                            <!--Classe para alinhamento de elementos-->
                            <i class="imdb material-icons black-text" id="nota">star</i><b><?= $filme->nota ?><a href="https://www.imdb.com/"> IMDB</a></b>
                        </div>
                    </div>
                </div>

                 <?php  //if ($c == 14) : ?>
                    <!-- <div class="parallax-meio parallax-container">
                        <div class="parallax"><img src="https://www.themoviedb.org/t/p/original/3dUByTea97X3XzznN8ZPFX9c7J7.jpg"></div>
                    </div> -->
                    <?php  //$c = 0; ?>
                <?php // endif ?> 

            <?php endif ?>
        <?php endforeach ?>
    </div>