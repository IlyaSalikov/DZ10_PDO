<?php
    include_once ('classes.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <title>Галерея</title>
</head>
<body style="background: ghostwhite;">
    <div class="container">
        <header class="row">
        </header>

        <ul class="nav row">
            <li class="nav-item col-sm-3 col-md-3 col-lg-3">
                <a href="index.php?page=1">
                    Catalog
                </a>
            </li>
            <li class="nav-item col-sm-3 col-md-3 col-lg-3">
                <a href="index.php?page=2">
                    Cart
                </a>
            </li>
            <li class="nav-item col-sm-3 col-md-3 col-lg-3">
                <a href="index.php?page=3">
                    Register
                </a>
            </li>
            <li class="nav-item col-sm-3 col-md-3 col-lg-3">
                <a href="index.php?page=4">
                    Admin
                </a>
            </li>
        </ul>
        <section class="row">
            <?php
                switch ($_GET['page'])
                {
                    case "1":
                    {
                        include_once ("catalog.php");
                        break;
                    }
                    case "2":
                    {
                        include_once ("cart.php");
                        break;
                    }
                    case "3":
                    {
                        include_once ("register.php");

                        break;
                    }
                    case "4":
                    {
                        include_once ("admin.php");

                        break;
                    }
                }
            ?>
        </section>
        <footer class="row">
        </footer>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>
</html>