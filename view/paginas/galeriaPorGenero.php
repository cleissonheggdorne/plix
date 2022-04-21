<?php include "./view/estrutura/cabecalho.php";
?>

<?php

require_once "./util/mensagem.php";
session_start();
$genero = $_GET['genero'];
$filmesController = new FilmesController();

if (!isset($_SESSION['busca']) or ($_SESSION['busca'] == "")) {
    $filmes = $_SESSION['filmes'];
    $num_paginas = $filmes[0];
} else {
    $filmes = $_SESSION['buscaRetornada'];
}

?>

<body class="purple darken-1">

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
            <li class="waves-effect"><a href="/genero/?genero=<?=$genero?>&pagina=<?= $_GET['pagina']-1 ?>"><i class="material-icons white-text">chevron_left</i></a></li>
        <?php }else{ ?>
            <li class="disable"><a href="#"><i class="material-icons">chevron_left</i></a></li>
        <?php } ?>
        <?php for ($cont = 1; $cont <= $num_paginas; $cont++) : ?>
            <?php if ($_SERVER["REQUEST_URI"] == "/genero/?genero=$genero&pagina=$cont") {?>
                <li class="waves-effect active black"><a href="/genero/?genero=<?=$genero?>&pagina=<?= $cont ?>"><?= $cont ?></a></li>
            <?php }else{ ?>
                <li class="waves-effect"><a href="/genero/?genero=<?=$genero?>&pagina=<?= $cont ?>"><?= $cont ?></a></li>
            <?php }?>
        <?php endfor ?>
        <?php if ($_GET['pagina'] < $num_paginas) { ?>
            <li class="waves-effect"><a href="/genero/?genero=<?=$genero?>&pagina=<?= $_GET['pagina']+1 ?>"><i class="material-icons white-text">chevron_right</i></a></li>
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