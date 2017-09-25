$j(document).ready(function() {
    
    //Date-time picker
    
    $j('#datetimepicker1').datepicker({
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showOtherMonths: true,
        selectOtherMonths: true,
        maxDate: new Date(),
        onClose: function (selectedDate) {
            $j('#datetimepicker2').datepicker("option", "minDate", selectedDate);
        }
    });
    $j('#datetimepicker2').datepicker({
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showOtherMonths: true,
        selectOtherMonths: true,
        maxDate: new Date(),
        onClose: function (selectedDate) {
            $j('#datetimepicker1').datepicker("option", "maxDate", selectedDate);
        }
    });
    
    
    //Submit time interval for statistic

    $j('#all').click(function() {
        $j('#datetimepicker1').val('');
        $j('#datetimepicker2').val('');
        $j('form#cupg_statistic').submit();
    });

    $j('#days7').click(function() {
        setDates(7);
    });

    $j('#days14').click(function() {
        setDates(14);
    });
    
    function setDates(interval) {
        var today = new Date();
        $j('#datetimepicker2').val(today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ("0" + today.getDate()).slice(-2));
        today.setDate( today.getDate()-(interval-1) );
        $j('#datetimepicker1').val(today.getFullYear() + "-" + ("0" + (today.getMonth() + 1)).slice(-2) + "-" + ("0" + today.getDate()).slice(-2));
        $j('form#cupg_statistic').submit();
    }
    
    var ctx = document.getElementById('cupg_canvas').getContext("2d");
    window.myLine = new Chart(ctx).Bar(lineChartData, {
        responsive: true
    });
    
    //Submit statistic form on submit click or on change of statistic type
    $j('form#cupg_statistic button[type="submit"]').on('click', function(e) {
        e.preventDefault();
        checkBeforeSubmit();
        $j('form#cupg_statistic').submit();
    });
    
    $j('input[type="radio"]').on('change', function() {
        checkBeforeSubmit();
        $j('form#cupg_statistic').submit();
    });
    
    function checkBeforeSubmit() {
        var dateFrom = $j('#datetimepicker1').val();
        var dateTo = $j('#datetimepicker2').val();
        
        if ((dateFrom && !dateTo) || (!dateFrom && dateTo)) {
            $j('#datetimepicker1').val('');
            $j('#datetimepicker2').val('');
        }
    }
    
    //Feed subscriber emails from data base
    
    $j('#cupg_export').on('click', function (e){
        e.preventDefault();
        var request = {
            date_from: $j('#datetimepicker1').val(), 
            date_to: $j('#datetimepicker2').val(), 
            action: "cupg_export_to_csv"
        };
        $j.ajax({
            type: "post", 
            dataType: "json", 
            url: Cupg_Ajax.ajaxurl,
            data: request, 
            success: function (data) {
                if (data.result === 'success') {
                    window.location.href = data.path;
                }
                else if (data.result === 'error'){
                    alert(data.error);
                }
            }
        });
    });

});