<?php include "./view/estrutura/cabecalho.php";
?>

<?php
if(!isset($_SESSION)){
    session_start(); 
}

require_once "./util/mensagem.php";
$filmesController = new FilmesController();
$destaques = $filmesController->destaques();

if (!isset($_SESSION['busca']) or ($_SESSION['busca'] == "")) {
    $filmes = $_SESSION['filmes'];
    $num_paginas = $filmes[0];
} else {
    $filmes = $_SESSION['buscaRetornada'];
}

?>
<head>
    <meta name="description" content="Assistir filmes online gratis a qualquer momento e qualquer hora.">
    <meta name="keywords" content="filmes, filmes online, assistir filmes, filmes hd, melhores filmes, filmes <?=date ("Y") ?>, filmes lancamento">
    <title>Plix - Assista a Filmes na Web em HD, Online e Grátis (<?=date ("Y") ?>)</title>
</head>
<body class="purple darken-1">

   <?php
        /* Barra de Navegação e Menu móvel */
        REQUIRE "./view/estrutura/sidenav-e-menuMobile.php"; 
    ?>

    <main>

    <div class="carousel">
        <?php foreach($destaques as $destaque) :?>
            <a class="carousel-item hoverable" href="/assistir/<?= str_replace(' ', '-', $destaque->titulo) . "?id=" . ($destaque->id) ?>">
                <img id="img-carrossel" src="<?= (str_contains($destaque->img_wide_1, 'imagens/posters')) ?  '/' . $destaque->img_wide_1 : $destaque->img_wide_1 ?>">
                <h3 class="title-destaque"><?= $destaque->titulo ?></h3>
            </a>
        <?php endforeach ?>
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
  
    <?php
        /* Mostra grid de filmes*/ 
        include "./view/estrutura/gridFilmes.php"; 
    ?>
   <div class="fundo-paginacao purple darken-1">
    <ul class="pagination container center">
        <?php if ($_GET['pagina'] >= 2) { ?>
            <li class="waves-effect"><a href="/inicio?pagina=<?= $_GET['pagina']-1 ?>"><i class="material-icons white-text">chevron_left</i></a></li>
        <?php }else{ ?>
            <li class="disable"><a href="#"><i class="material-icons">chevron_left</i></a></li>
        <?php } ?>
        <?php for ($cont = 1; $cont <= $num_paginas; $cont++) : ?>
            <?php if ($_SERVER["REQUEST_URI"] == "/inicio?pagina=$cont") {?>
                <li class="waves-effect active black"><a href="/inicio?pagina=<?= $cont ?>"><?= $cont ?></a></li>
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
   </div>
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
    include "./view/estrutura/rodape.php";
    ?>
</body>

</html>