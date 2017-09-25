<table class="cupg_bonuses_table">
    <tr>
        <th><?= $settings['column1'] ?></th>
        <th><?= $settings['column2'] ?></th>
    </tr>
    
    <?php 
        $row = (count($bonuses) % 2 === 0)? 1 : 0;
        foreach ($bonuses as $bonus):
    ?>
                    <tr<?= ($row % 2 === 0)? ' class="grey_row"' : '' ?>>

                        <td>
                            <?php if ($bonus['article_url'] != ''): ?>
                                <a href="<?= $bonus['article_url'] ?>" target="_blank">
                                    <?= $bonus['arcticle_title'] ?>
                                </a>
                            <?php else: ?>
                                <?= $bonus['arcticle_title'] ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($bonus['filename'] != ''): 
                                    $target = '';
                                    $images = array('jpg', 'gif', 'png', 'jpeg', 'jpe', 'tiff', 'bmp');
                                    if (in_array(pathinfo($bonus['filename'], PATHINFO_EXTENSION), $images)) {
                                        $target = ' target="_blank"';
                                    }
                            ?>
                            <a href="<?= $bonus['filename'] ?>"<?= $target ?>>
                                <?= $settings['download'] ?>
                            </a>
                            <?php endif; ?>
                        </td>

                    </tr>
    <?php
            $row++;
        endforeach; 
    ?>
</table>