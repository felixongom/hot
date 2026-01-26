<h1>Hi, i am <?= self::e($name) ?></h1>
<?= self::renderComponent('card', ['name' => $name, 'age' => $age], '
    <div>Action div</div>
') ?>
<!--  -->
<?php foreach ($list as $value): ?>
    <ul>
        <?= self::renderComponent('list', ['item' => $value]) ?>
    </ul>
<?php endforeach ?>
