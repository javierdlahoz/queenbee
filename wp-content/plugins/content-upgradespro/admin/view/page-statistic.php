<?php 
$show_export_button = $this->email_service->get_properties('csv_export');
?>
<div class="cupg_page_wrapper">
    <h2 class="cupg_page_title">Statistics</h2>

    <div class="cupg_content_wrapper cupg_clearfix">
        
            <form id='cupg_statistic' method='post'>
                <div class="cupg_tabs">
                    <input id="cupg_cu" name="cupg_tabs" type="radio" value="cu" <?= checked($page_data['selected_statistic'], 'cu', false) ?>>
                    <label for="cupg_cu">Content Upgrades</label>
                    <input id="cupg_popup" name="cupg_tabs" type="radio" value="popup" <?= checked($page_data['selected_statistic'], 'popup', false) ?>>
                    <label for="cupg_popup">Sitewide Popup</label>
                </div>

                <div class="cupg_stat_wrapper">
                    <div class="cupg_stat_options cupg_clearfix">
                        
                        <p class="cupg_left">New optins</p>

                        <div class="cupg_right">
                            <a href="#" id="all" class="stat_interval<?= ($page_data['interval'] === 'all')? ' active_stat_interval':'' ?>">All</a>
                            <a href="#" id="days7" class="stat_interval<?= ($page_data['interval'] === 'days7')? ' active_stat_interval':'' ?>">Last 7 Days</a>
                            <a href="#" id="days14" class="stat_interval<?= ($page_data['interval'] === 'days14')? ' active_stat_interval':'' ?>">Last 14 Days</a>   

                            <div class="datepicker">
                                <span>Date range:</span>
                                <input type="text" name="date_from" id='datetimepicker1' value="<?= (isset($_POST['date_from']))? $_POST['date_from'] : '' ?>"/>
                                <input type="text" name="date_to" id='datetimepicker2' value="<?= (isset($_POST['date_to']))? $_POST['date_to'] : '' ?>" />
                            </div>

                            <button type="submit">Apply</button>
                        </div>

                    </div>
                </div>
            </form>

            <div class="cupg_stat_wrapper">
                <div class="cupg_stat_canvas<?= ($page_data['data']['chart'])? '': ' cupg_hidden' ?>">
                    <canvas id='cupg_canvas' height="450" width="1145"></canvas>
                </div>
            </div>
        
            <div class="cupg_stat_wrapper">
                <?php 
                    if ($page_data['selected_statistic'] === 'popup') {
                        include_once 'statistic-table-popup.php';
                    }
                    else {
                        include_once 'statistic-table-cu.php';
                    }
                ?>
            </div>
                
            <div>
                <?php if ($show_export_button): ?>

                    <button id="cupg_export" class="button button-primary" role="button">
                        <?= ($page_data['interval'] === 'all')? 'Get all subscribers' : 'Get subscribers for selected interval'?>
                    </button>

                <?php endif; ?>
            </div>
                    
    </div>
</div>
<script>
var lineChartData = {
    labels: [<?= isset($page_data['data']['chart']['days'])? $page_data['data']['chart']['days'] : ''; ?>],
    datasets: [
        {
            label: "Statistic dataset",
            fillColor: "rgba(151,187,205,0.5)",
            strokeColor: "rgba(151,187,205,0.8)",
            highlightFill: "rgba(151,187,205,0.75)",
            highlightStroke: "rgba(151,187,205,1)",
            data: [<?= isset($page_data['data']['chart']['values']) ? $page_data['data']['chart']['values'] : ''; ?>]
        }
    ]
};
</script>