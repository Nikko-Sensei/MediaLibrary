<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Media Library') ?></title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>

<body>

    <div class="page-container">
        <div class="content">

            <header class="header">
                <div class="wrapper">

                    <!-- LOGO -->
                    <h1 class="logo">
                        <a href="<?= BASE_URL ?>/Public/index.php">
                            <img src="<?= BASE_URL ?>/img/Brand-title.png" alt="Media Library">
                        </a>
                    </h1>

                    <!-- NAVIGATION -->
                    <ul class="nav">
                        <li class="<?= ($section === 'books') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=books">
                                <img src="<?= BASE_URL ?>/img/book.png"> Books
                            </a>
                        </li>

                        <li class="<?= ($section === 'movies') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=movies">
                                <img src="<?= BASE_URL ?>/img/movie.png"> Movies
                            </a>
                        </li>

                        <li class="<?= ($section === 'music') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=catalog&cat=music">
                                <img src="<?= BASE_URL ?>/img/music.png"> Music
                            </a>
                        </li>

                        <li class="<?= ($section === 'suggest') ? 'on' : '' ?>">
                            <a href="<?= BASE_URL ?>/Public/index.php?page=suggest">
                                <img src="<?= BASE_URL ?>/img/suggestion.png"> Suggest
                            </a>
                        </li>

                        <?php if (isset($_SESSION['user'])): ?>
                            <li class="user-name">
                                <img src="<?= BASE_URL ?>/img/admin.png">
                                <?= htmlspecialchars($_SESSION['user']['username']) ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['user'])): ?>
                            <li class="<?= ($section === 'logout') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=logout">
                                    <img src="<?= BASE_URL ?>/img/logout.png">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="<?= ($section === 'login') ? 'on' : '' ?>">
                                <a href="<?= BASE_URL ?>/Public/index.php?page=login">
                                    <img src="<?= BASE_URL ?>/img/login.png">Login</a>
                            </li>
                            <!-- <li class="<?= ($section === 'register') ? 'on' : '' ?>">
                    <a href="<?= BASE_URL ?>/Public/index.php?page=register">Register</a>
                </li> -->
                        <?php endif; ?>
                    </ul>

                </div>
            </header>

            <!-- SEARCH BAR -->
            <?php if (empty($hideSearch)): ?>
                <div class="search">
                    <div class="wrapper">
                        <form method="get" action="<?= BASE_URL ?>/Public/index.php">
                            <input type="hidden" name="page" value="catalog">

                            <?php if (!empty($section)): ?>
                                <input type="hidden" name="cat" value="<?= htmlspecialchars($section) ?>">
                            <?php endif; ?>

                            <label for="s">Search:</label>
                            <input type="text" name="s" id="s">
                            <input type="submit" value="Go">
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <main id="content">