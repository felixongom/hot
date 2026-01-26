<div style="padding-left:5px ;border-bottom: 1px solid #ddd; display:flex;">
    <div style="width: 50px; height:50px; border-radius: 50%; background: #4d4dff; display:flex;align-items:center;justify-content:center; text-align:center; font-size:20px; font-weight:bold; color:white;margin-right:10px"><?= self::e($item) ?></div>
    <?= self::renderComponent('list-container', ['num' => $item]) ?>
</div>