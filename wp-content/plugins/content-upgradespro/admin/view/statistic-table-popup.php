<div class="cupg_stat_table cupg_stat_table_popup">
    
    <ul class="cupg_table cupg_table_title">
        <li>Sitewide Pop-up</li>
        <li>Views</li>
        <li>Optins</li>
        <li>Optins/Views</li>
    </ul>

    <div class="cupg_table_content">
        <?php 
            $totals = array('popups' => 0, 'subscriptions' => 0); 
            foreach ($page_data['data']['table'] as $key => $value) :
        ?>
                <ul class="cupg_table">
                    <li><?= get_the_title($key) ?></li>
                    <li><?= $value['popups'] ?></li>
                    <li class="blue"><?= $value['subscriptions'] ?></li>
                    <li><?php echo ($value['popups'] != 0) ? round($value['subscriptions'] / $value['popups'] * 100, 2) : 0; ?>%</li>
                </ul>
        <?php
                $totals['popups'] += $value['popups'];
                $totals['subscriptions'] += $value['subscriptions'];
            endforeach;
        ?>  
    </div>

    <ul class="cupg_table cupg_table_totals">
        <li></li>
        <li><?= $totals['popups'] ?></li>
        <li><?= $totals['subscriptions'] ?></li>
        <li><?= ($totals['popups'] != 0) ? round($totals['subscriptions'] / $totals['popups'] * 100, 2) : 0; ?>%</li>
    </ul>
                
</div>