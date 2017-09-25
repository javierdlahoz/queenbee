<div class="cupg_stat_table">

    <ul class="cupg_table cupg_table_title">
        <li>Upgrade Title</li>
        <li>Visitors</li>
        <li>Clicks</li>
        <li>Optins</li>
        <li>Clicks/Visitors</li>
        <li>Optins/Clicks</li>
        <li>Optins/Visitors</li>
    </ul>

    <div class="cupg_table_content">
        <?php 
            $totals = array('visits' => 0, 'popups' => 0, 'subscriptions' => 0); 
            foreach ($page_data['data']['table'] as $key => $value) :
        ?>
                <ul class="cupg_table">
                    <li><?= get_the_title($key) ?></li>
                    <li><?= $value['visits'] ?></li>
                    <li><?= $value['popups'] ?></li>
                    <li class="blue"><?= $value['subscriptions'] ?></li>
                    <li><?= ($value['visits'] != 0)? round($value['popups'] / $value['visits'] * 100, 2) : 0; ?>%</li>
                    <li><?php echo ($value['popups'] != 0) ? round($value['subscriptions'] / $value['popups'] * 100, 2) : 0; ?>%</li>
                    <li><?php echo ($value['visits'] != 0) ? round($value['subscriptions'] / $value['visits'] * 100, 2) : 0; ?>%</li>
                </ul>
        <?php 
                $totals['visits'] += $value['visits'];
                $totals['popups'] += $value['popups'];
                $totals['subscriptions'] += $value['subscriptions'];
            endforeach;
        ?>  
    </div>

    <ul class="cupg_table cupg_table_totals">
        <li></li>
        <li><?= $totals['visits'] ?></li>
        <li><?= $totals['popups'] ?></li>
        <li><?= $totals['subscriptions'] ?></li>
        <li><?= ($totals['visits'] != 0) ? round($totals['popups'] / $totals['visits'] * 100, 2) : 0; ?>%</li>
        <li><?= ($totals['popups'] != 0) ? round($totals['subscriptions'] / $totals['popups'] * 100, 2) : 0; ?>%</li>
        <li><?= ($totals['visits'] != 0) ? round($totals['subscriptions'] / $totals['visits'] * 100, 2) : 0; ?>%</li>
    </ul>
    
</div>