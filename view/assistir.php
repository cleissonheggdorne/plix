<?php include "cabecalho.php";
?>

<?php
//session_destroy();
require "./util/mensagem.php";
session_start();


$controller = new FilmesController();
$filmes = $controller->index();
//$controller = new FilmesController();
$assistirFilme = $controller-> pageFilme($_GET['id']);
?>

<body>

    <nav class="nav-extended purple lighten-3">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <ul id="nav-mobile" class="right">
                <!--Responsividade, esconde a barra quando atlea for média ou pequena-->
                <li class="active"><a href="/">Galeria</a></li>
                <li><a href="/novo">Cadastrar</a></li>
            </ul>
        </div>
        <div class="nav-header center">
            <h1>CLOROCINE</h1>
        </div>
        <div class="nav-content">
            <ul class="tabs tabs-transparent purple darken-1 active">
                <!--Define a cor da segunda barra de navegação com-->
                <li class="tab"><a class="active" href="/">TODOS</a></li>
                <li class="tab"><a class="" href="/favoritos">FAVORITOS</a></li>
            </ul>
        </div>
    </nav>

    <div class="section">
        <div class="row">
        <?php foreach ($assistirFilme as $assistir) : ?>
             <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
            <div class="col s12 m4 l4 xl3">
                
                <!-- Imagem do pôster-->
                <div class="card-image">
                    <img src="<?= $assistir->poster ?>">
                    <!--Aqui tem que entrar a imagem do pôster-->
                </div>
            </div>
            <!--Sinopse -->
            <div class="col s12 m4 l4 xl3">
                <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
                <div class="card-content">
                    <p class="valign-wrapper">
                        <!--Classe para alinhamento de elementos-->
                        <i class="material-icons amber-text">star</i><?= $assistir->nota ?>
                        <p><span class="card-title"><strong><?= $assistir->titulo?></strong></span></p>
                    </p>
                    <!--Aqui tem que entrar a nota do filme-->
                    <span class="card-title"><strong>
                        <?= $assistir->sinopse?>
                    </strong></span> <!--Aqui tem que entrar o link do player-->
                    <p class="valign-wrapper"><a href="<?= $assistir->url?>" type="video/mp4"><button class="btn waves-effect purple">Assistir
                                <i class="material-icons right"><span class="material-icons">play_circle_outline</span></i></a></p>
                    </button>
                </div>
            </div>

            <div class="col s12 m4 l4 xl3">
                <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
                <iframe width="950" height="534" src="https://www.youtube.com/watch?v=2Ny6EJlsFoU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>   
            </div>
        <?php endforeach ?>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>" ?>
            <?php
            foreach ($filmes as $filme) : ?>
                <div class="col s12 m4 l4 xl3">
                    <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
                <a href="/assistir/<?= "?id=".($filme->id) ?>">
                    <div class="card">
                        <div class="card-image">
                            <img class="activator" src="<?= $filme->poster ?>">
                            <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red" data-id="<?= $filme->id ?>">
                                <i class="material-icons"><?= ($filme->favorito) ? "favorite" : "favorite_border" ?></i></button>
                            <!--ícone Favorito-->
                        </div>
                        <div class="card-content">
                            <p class="valign-wrapper">
                                <!--Classe para alinhamento de elementos-->
                                <i class="material-icons amber-text">star</i><?= $filme->nota ?>
                            </p>
                            <span class="card-title"><strong><?= $filme->titulo ?></strong></span>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4"><?= $filme->titulo ?><i class="material-icons right">close</i></span>
                            <p><?= substr($filme->sinopse, 0, 500) . "..." ?></p>
                            <button class="waves-effect waves-light btn-small right red accent-2 btn-delete" data-id="<?= $filme->id ?>">
                                <i class="material-icons">delete</i></button>
                        </div>
                    </div>
                </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>



    <?= Mensagem::mostrar(); ?>
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