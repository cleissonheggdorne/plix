<?php 


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="/styles/style_login.css">
    <title>Login</title>
</head>


<body>
   <?php if($logado) {?>
    <main class="login">
        <div class="login__container">
            <h1 class="login__title">Login</h1>
            <form class="login__form" method="POST">
                <input class="login__input" type="email" name = "usuario" placeholder="e-mail" required/>
                <span class="login__input-border"></span>
                <input class="login__input" type="password" name="senha" placeholder="senha" required/>
                <span class="login__input-border"></span>
                <button type="submit" class="login__submit">Login</button>
                <a class="login__reset" href="#">Esqueci a Senha</a>
            </form>
        </div>
    </main>
    <?php }else require "./view/galeria.php" ?>
</body>

</html>