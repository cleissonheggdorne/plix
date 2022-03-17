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