<div>
    
    <button><?= self::out($text) ?></button>
    <?php foreach ($num as $value): ?>
        <div><?= self::out($value) ?></div>
    <?php endforeach ?>
    <?=  self::raw($slot)  ?>
    <?= self::renderComponent('small', [], ' i am here') ?>
</div>