<?php include(APPPATH."views/admin/common/header.php"); ?>
<style>
    .panel-default>.panel-heading {
        color: #34495e;
        background-color: #E8ECEF;
        border-color: #ddd;
    }
    .caption {
        font-size:13px !important;
    }
    .portlet-box {
        background-color:#2C4259 !important;
    }
    .custom-panel {
        background-color:#4B5765;
        color:#fff;
        cursor:pointer;
        padding:10px;
        border-bottom:1px solid #ddd;
    }
    .select_match_status {
        margin-bottom:10px;
        color:#4B5765
    }
    .save-msg {
        background-color:green;
        color:#fff;
        font-size:12px;
        padding: 0px 15px;
        display: none;
    }
    .save-msg-error {
        background-color:red;
        color:#fff;
        font-size:12px;
        padding: 0px 15px;
        display: none;
    }
    .save-notification {
        background-color:green;
        color:#fff;
        font-size:12px;
        padding: 0px 15px;
        display: none;
    }
    .bet-list-btn {
        font-size: 11px;
        background-color: #4B5765;
        color: #fff;
    }
    .bet-list-btn:hover {
        color: #fff;
    }
    .match-btn {
        /*font-size: 11px;*/
        background-color: #4B5765;
        color: #fff;
        border: 1px solid #000;
    }
    .match-btn:hover {
        color: #fff;
        background-color: #4B5765;
        border: 1px solid #fff;
    }
    .bet-winner-btn {
        font-size: 11px;
        background-color: #4B5765;
        color: #fff;
        width: 100%;
    }
    .bet-winner-btn:hover {
        color: #fff;
    }
</style>
<style>
.tooltiptext2 {
    /* display: none; */
    width: 210px !important;
    background-color: #000;
    color: white;
    text-align: center;
    padding: 5px 5px;
    border-radius: 0px;
    position: absolute;
    z-index: 1000;
    /* left: -38px; */
    /* top: 155px; */
    margin-bottom: 10px !important;
    font-size: 10px;
}
</style>


    <body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo">
        <!-- top-nav -->
        <?php include(APPPATH."views/admin/common/top_nav.php"); ?>

        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->


        <!-- BEGIN CONTAINER -->
        <div class="page-container">

            <!-- sidebar link -->
            <?php include(APPPATH."views/admin/common/sidebar.php"); ?>


<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">

        <!-- FLASH MESSAGE -->
        <?php include(APPPATH."views/admin/common/flash.php"); ?>


        <!-- BEGIN PAGE BASE CONTENT -->
            <?php $betIds=[]; $betOpIds=[]; ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-pencil font-sunglo"></i>
                        <span class="caption-subject font-sunglo bold uppercase">Winner Selection</span>
                    </div>
                </div>
                <div class="portlet-body form">

                <!-- start row -->
                <div class="row">

                    <div class="col-md-12">
                        <!-- BEGIN ACCORDION PORTLET-->
                        <div class="portlet box yellow">
                            <div class="portlet-title portlet-box">
                                <div class="caption">Completed Matches </div>
                                <div class="tools">
                                    <a href="javascript:void(0);" class="collapse"> </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="panel-group accordion" id="accordion1">

                                    <?php
                                        $i=1;
                                        foreach($matches as $val) {
                                            $ac_status = $val->active_status;
                                    ?>
                                    <div class="panel panel-default">

                                        <div style="" class="custom-panel">
                                            <div style="margin-bottom:10px" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_<?= $i; ?>">

                                                <?php if($val->sportscategory_id==6) { ?>
                                                    <img style="width:20px" src="<?php echo base_url(); ?>assets/admin/img/football.gif">
                                                <?php } else if($val->sportscategory_id==2 || $val->sportscategory_id==14 || $val->sportscategory_id==15) { ?>
                                                    <img style="width:20px" src="<?php echo base_url(); ?>assets/admin/img/cricket.gif">
                                                <?php } ?>

                                                <?= $val->title; ?> =&gt; <?= $val->league_title; ?> &lt;= <?php echo $val->match_date_var ?> =&gt; <?php echo $val->match_time_var ?>


                                            <!-- <a style="margin-left:15px" onclick="return confirm('Are you sure to cancel this match?')" href="<?php //echo base_url('admin/match_abandoned/'.$val->id); ?>" class="btn btn-xs btn-info match-btn">Match Abandoned</a> -->

                                            <?php $full_url = base_url('admin/match_abandoned/'.$val->id); ?>
                                            <a style="margin-left:15px" onclick="return matchAbandoned('<?php echo $full_url; ?>')" class="btn btn-xs btn-info match-btn">Match Abandoned</a>

                                            <?php if($val->active_status==4) : ?>
                                                <a style="margin-left:15px" href="<?php echo base_url('admin/final_result_done/'.$val->id); ?>" onclick="return confirm('Are you sure to close this match finally?')" class="btn btn-xs btn-danger">Result Published</a>
                                            <?php endif; ?>

                                            </div>
                                            
                                        </div>
                                        <?php
                                        // $get_bet_data = get_bet_data_at_winner($val->id);
                                        // dd($get_bet_data);
                                        ?>

                                        <!-- <div id="collapse_<?php //echo $i; ?>" class="panel-collapse <?php //echo $i==1 ? "in" : "collapse";  ?>"> -->
                                        <div id="collapse_<?= $i; ?>" class="panel-collapse <?= $i==1 ? "collapse" : "collapse";  ?>">

                                        <?php
                                            // $get_bet_data = $this->db->query("SELECT * FROM match_option WHERE match_id='{$val->id}' AND status IN(0,1,2) ORDER BY match_option_serial ASC")->result();
                                            $get_bet_data = get_bet_data_at_winner($val->id);
                                            //dd($get_bet_data);
                                            
                                            //die();
                                            foreach($get_bet_data as $data) :
                                                $active_bet = $data->status;
                                                // $options = $this->db->query("SELECT * FROM match_option_details WHERE match_option_id='{$data->id}' ORDER BY option_serial ASC")->result();
                                                $options = get_bet_match_option_details($data->id);
                                                //dd($options);
                                                // echo count($options);
                                        ?>

                                        <div class="panel panel-default div-<?php echo $data->id; ?>" style="margin:5px;">
                                            <div class="panel-heading" style="padding:5px">

                                                <div style="cursor:pointer;" onclick="return showPanel(<?= $data->id; ?>)" class="pull-left" style="font-size:12px">
                                                    <?php echo $data->match_option_title; ?>
                                                </div>

                                                <div class="pull-left" style="font-size:12px;margin-left:50px">
                                                    <a class="btn btn-xs bet-list-btn" href="<?php echo base_url('show_bet_list/'.$data->id) ?>">Bet List</a>
                                                    <a onclick="return confirm('Are you sure to cancel this bet?');" class="btn btn-xs bet-list-btn" href="<?php echo base_url('admin/bet_match_cancel/'.$data->id) ?>">Return</a>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Note: &nbsp;
                                                </div>
                                                <div>
                                                    <input type="text" name="" class="" style="width: 400px;border:1px solid #555;">
                                                </div>
                                            </div>

                                            <div style="display:none;" id="panel-id-<?= $data->id; ?>" class="panel-body">

                                                <?php
                                                    // dd($options);
                                                    if(isset($options)) {
                                                        foreach($options as $op) { 
                                                            
                                                        $id = $op->id; 
                                                        $ratio = $op->option_coin;
                                                        $betOpIds[] = $id;
                                                        /* echo $op->id."<br>";
                                                        echo $op->match_option_id."<br>";
                                                        echo $op->option_title."<br>";
                                                        echo $op->option_coin."<br>";
                                                        echo $op->option_serial."<br>";
                                                        echo $op->status."<br>"; */
                                                ?>
                                                <div class=" row option-data" style="margin-bottom: 10px">
                                                    <div class="col-md-3 text-center col-sm-4 col-xs-6" style="font-size:11px;margin-bottom:5px">
                                                        <?= !empty($op->option_title) ? $op->option_title : ''; ?>
                                                    <span class="tb_tr_section" style="margin-left:10px;font-weight:bold">
                                                    
                                                    </span>
                                                    <?php if($data->status==1 || $data->status==0 || $data->status==2) { ?>

                                                        <!-- <a onclick="return confirm('Are you sure to win this answer?');" href="<?php //echo base_url('admin/set_bet_winner_old/'.$data->id.'/'.$op->id) ?>" class="btn btn-xs bet-winner-btn">
                                                            <?php //echo $op->option_coin; ?>
                                                        </a> -->

                                                        <a
                                                        data-match_id="<?php echo $data->id; ?>"
                                                        data-option_id="<?php echo $op->id; ?>"
                                                        onclick="return setBetWinner('<?php echo $data->id; ?>', '<?php echo $op->id; ?>'); confirm('Are you sure to win this answer?');"
                                                        class="btn btn-xs bet-winner-btn">
                                                            <?php echo $op->option_coin; ?>
                                                        </a>

                                                        <?php } else if($data->status==3) { ?>
                                                            <a href="javascript:void(0);" class="btn btn-xs bet-winner-btn">
                                                            <?php echo $op->option_coin; ?>

                                                            <?php if($op->status==2) { ?>
                                                                <span style="color:green;font-weight:bold">(Win)</span>
                                                            <?php } ?>
                                                        </a>
                                                        <?php } else if($data->status==4) { ?>
                                                            <a href="javascript:void(0);" class="btn btn-xs bet-winner-btn">
                                                            <?php echo $op->option_coin; ?>
                                                            <span style="color:red;font-weight:bold">(Cancel)</span>
                                                            </a>
                                                        <?php } ?>

                                                    </div>

<div class="answer_success" style="display:none;" id="answer_success_<?= $op->id; ?>">
    <span id="toolTipText_<?= $op->id; ?>" class="tooltiptext2">Total Bet: <?= $op->id; ?>, Total Bet Amount: 0 TK, Total Return Amount: 0 TK, Total Bet On This Question: 0 TK</span>
</div>

                                                </div>
                                                <?php 
                                                        }
                                                    }
                                                
                                                ?>
                                                <span class="save-msg" id="save-msg-<?= $data->id; ?>"></span>

                                            </div>

                                        </div>

                                        

                                        <?php endforeach; ?>


                                        </div>

                                    </div>
                                    <?php $i++; } ?>

                                </div>
                            </div>
                        </div>
                        <!-- END ACCORDION PORTLET-->
                    </div>

                </div>
                <!-- end -->


                </div>
            </div>

        <!-- END PAGE BASE CONTENT -->
    </div>
    <!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->
            


        </div>
        <!-- END CONTAINER -->

<?php include(APPPATH."views/admin/common/footer.php"); ?>
<script>

    function updateBetRatio(id, coin, span_id) {
        var url_prefix = "<?php echo base_url(); ?>";

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/update_bet_ratio',
            data: {
                option_details_id: id,
                option_coin: coin
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    $("span#save-msg-"+span_id).show().text("saving...");
                    setTimeout(function() {
                        $("span#save-msg-"+span_id).hide();
                    }, 500);
                }
                else if(data.error==0) {
                    $("span#save-msg-"+span_id).show().addClass("save-msg-error").text("failed!");
                    setTimeout(function() {
                        $("span#save-msg-"+span_id).removeClass("save-msg-error").hide();
                    }, 500);
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });
    }

    function updateBetScore(id, score) {
        var url_prefix = "<?php echo base_url(); ?>";

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/update_bet_score',
            data: {
                match_id: id,
                notification: score
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    console.log("success");
                    $("span#save-notify-"+id).show().text("saving...");
                    setTimeout(function() {
                        $("span#save-notify-"+id).hide();
                    }, 500);
                }
                else if(data.error==0) {
                   console.log("failed!"); 
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });

    }

    function updateMatchStatus(match_id, active_status) {
        var url_prefix = "<?php echo base_url(); ?>";
        var match_id = match_id;
        var active_status = active_status;

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/update_match_status',
            data: {
                match_id: match_id,
                active_status: active_status
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    console.log("success");
                    if(active_status==1) {
                        $("span.match_status_"+match_id).text('[In Play]');
                    } else if(active_status==2) {
                        $("span.match_status_"+match_id).text('[Live]');
                    }
                    else if(active_status==3) {
                        $("span.match_status_"+match_id).text('[Upcoming]');
                    }
                }
                else if(data.error==0) {
                   console.log("failed!"); 
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });
    }

    function updateBetStatus(bet_id, status) {
        console.log(bet_id);
        console.log(status);

        var url_prefix = "<?php echo base_url(); ?>";
        var bet_id = bet_id;
        var status = status;

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/update_bet_status',
            data: {
                bet_id: bet_id,
                status: status
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    console.log("success");
                    if(status==1) {
                        $("span.bet_status_"+bet_id).text('[Active]');
                    } else if(status==2) {
                        $("span.bet_status_"+bet_id).text('[Inactive]');
                    }
                }
                else if(data.error==0) {
                   console.log("failed!"); 
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });
    }

    function showPanel(id) {
        $(".panel-body").slideUp();
        $("#panel-id-"+id).toggle();
        $(".bet-list-btn").css('color', '#fff');
    }

    function totalRatioCount() {
        var betOpIds = <?php echo json_encode($betOpIds); ?>;

        var url_prefix = "<?php echo base_url(); ?>";
        var bet_option_id = betOpIds;

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/update_total_ratio_count',
            data: {
                bet_option_id: bet_option_id
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    $.each(data.success_msg, function(index, item) {
                        $("#tb-tr-"+index).text(item);
                    });
                }
                else if(data.error==0) {
                   console.log("failed!"); 
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });
    }
    //setInterval(function () {totalRatioCount()}, 5000);

    function matchAbandoned(url) {
        var x = confirm("Are you sure to cancel this match?");
        if(x) {
            window.location = url;
        }
        return;
    }

    function setBetWinner(data_id, op_id) {
        var bet_id = data_id;
        var bet_option_id = op_id;
        var url_prefix = "<?php echo base_url(); ?>";

        var x = confirm("Are you sure to win this answer?");
        if(x==false) {
            return;
        }

        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/set_bet_winner',
            data: {
                bet_option_id: bet_option_id,
                bet_id: bet_id,
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    $(".div-"+data_id).hide();
                    alert("Result done successfully");
                }
                else if(data.error==0) {
                    $(".div-"+data_id).hide();
                    alert("failed!"); 
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });

    }

    // mouse over effect
    $(".bet-winner-btn").on("mouseout", function() {
        $(".answer_success").hide();
    });

    $(".bet-winner-btn").on('mouseover', function() {
        var match_id = $(this).data('match_id');
        var option_id = $(this).data('option_id');
        var url_prefix = "<?php echo base_url(); ?>";
        $(".answer_success").hide();


        // ajax request
        $.ajax({
            type: "POST",
            url: url_prefix + 'admin/get_bet_amount_data',
            data: {
                match_id: match_id,
                option_id: option_id
            },
            dataType: 'json',
            success: function(data) {
                if(data.error==0) {
                    // after success
                    $("#answer_success_"+option_id).show();
                    $("span#toolTipText_"+option_id).html(data.success_msg);
                }
                else if(data.error==1) {
                    alert("Error");
                }
            },
            error:function(exception){
                console.log(exception);
            }
        });
    });



</script>