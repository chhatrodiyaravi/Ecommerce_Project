<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Camera Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

</head>

<body>
    <div class="row">
        <?php
        if (isset($title_page)) {
        ?>
            <h1><?= $title_page ?></h1>
        <?php
        }
        if (isset($content)) {
            echo $content;
        }
        ?>
    </div>
</body>

</html>