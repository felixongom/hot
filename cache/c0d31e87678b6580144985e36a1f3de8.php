<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>May App</title>
</head>
<body>

    <h2>welcome to my app</h2>
    <!--  -->
    <?= self::renderComponent('card', ['name'=>'Tom Lii'], '
        <x-button text="yes" num="$num"> 
            <input type="text">
        </x-button>
        <x-button text="yes" num="$num"> 
            <input type="text">
        </x-button>
    ') ?> c
    <?=  self::raw($slot)  ?>
</body>
</html>