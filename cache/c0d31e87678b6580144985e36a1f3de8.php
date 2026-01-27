<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>May App</title>
</head>
<body>

    <h2>welcome to my app</h2>
    <?=  self::raw($head)  ?>
    <?=  self::raw($slot)  ?>
    <?= $list->name ?>
    <?= $list->age ?>
    <?= self::renderComponent('button', ['text'=>'yes','num'=>$num], ' 
        <input type="text">
    ') ?>
    <img src="<?= self::out($img) ?>" alt="">
</body>
</html>