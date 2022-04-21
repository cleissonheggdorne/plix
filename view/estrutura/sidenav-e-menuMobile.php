<nav class="nav-extended purple darken-2">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <a href="/inicio" class="brand-logo left">PLIX</a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <!--Responsividade, esconde a barra quando a tela for média ou pequena-->
                <li class=""><a href="/genero/1">Animação</a></li>
                        <li class=""><a href="/genero/2">Ação</a></li>
                        <li class=""><a href="/genero/3">Suspense</a></li>
                        <li class=""><a href="/genero/4">Terror</a></li>
                        <li class=""><a href="/genero/5">Ficção Científica</a></li>
                        <li class=""><a href="/genero/6">Comédia</a></li>
                <!-- <a class='dropdown-trigger btn transparent' href='#' data-target='dropdown1'>Gêneros</a>
                    <ul id='dropdown1' class='dropdown-content'>
                        <li class=""><a href="/genero/1">Animação</a></li>
                        <li class=""><a href="/genero/2">Ação</a></li>
                        <li class=""><a href="/genero/3">Suspense</a></li>
                        <li class=""><a href="/genero/4">Terror</a></li>
                        <li class=""><a href="/genero/5">Ficção Científica</a></li>
                        <li class=""><a href="/genero/6">Comédia</a></li>
                    </ul>   -->
                <?php 
                if (isset($_SESSION['usuario']) AND isset($_SESSION['usuario']) != "") { ?>
                    <!--Verifica se usuario existe-->
                    
                  <a class='dropdown-trigger btn transparent' href='#' data-target='dropdown2'><?= $_SESSION['usuario'] ?></a>
                    <ul id='dropdown2' class='dropdown-content'>
                        <li><a onclick="sair()" href="#">Sair</a></li>
                        <li><a href="/favoritos">Favoritos</a></li>
                        <li><a href="/salvos">Salvos</a></li>
                        <?php if ($_SESSION['usuario'] != "" && $_SESSION['admin'] == true) { ?>
                            <li><a href="/syscontrol">Painel de Controle</a></li>
                        <?php }?>
                    </ul>  

                <?php } else { ?>
                    <li><a href="/login"><i class="material-icons">login</i></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
  
    <ul class="sidenav  purple lighten-3" id="mobile-demo">
        <?php
        if (isset($_SESSION['usuario']) AND $_SESSION['usuario'] != "") { ?>
            <li><a href="#"><i class="material-icons">face</i><b><?= $_SESSION['usuario'] ?></b></a></li>
            <li><a href="/favoritos"><i class="material-icons">star</i>Favoritos</a></li>
            <li><a href="/salvos"><i class="material-icons">bookmark</i>Salvos</a></li>
            <li><a href="/syscontrol"><i class="material-icons">build</i>Painel de Controle</a></li>
            <li><a onclick="sair()" href="#"><i class="material-icons">logout</i>Sair</a></li>
        <?php } else { ?>
            <li class="purple darken-2"><a href="/login">Entrar</a></li>
        <?php } ?>
            <li><a class="subheader">Gêneros</a></li>
            <li><a href="/genero/1"><i class="material-icons">apps</i>Animação</a></li>
            <li><a href="/genero/2"><i class="material-icons">apps</i>Ação</a></li>
            <li><a href="/genero/3"><i class="material-icons">apps</i>Suspense</a></li>
            <li><a href="/genero/4"><i class="material-icons">apps</i>Terror</a></li>
            <li><a href="/genero/5"><i class="material-icons">apps</i>Ficção Científica</a></li>
            <li><a href="/genero/6"><i class="material-icons">apps</i>Comédia</a></li>
        </ul>
