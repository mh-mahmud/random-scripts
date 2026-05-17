<?php include(APPPATH."views/admin/common/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/all.css">
<style>
    body {
        background: #ddd !important;
    }
    .main_container {
        margin-left:0px !important;
    }
    .answer_success .tooltiptext2 {
        /*display: block !important;*/
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

<!-- <div id="wrapper"> -->




    <div id="main_container" class="main_container container_16 clearfix ui-sortable">
        <div class="box grid_16" style="opacity: 1;">
            <h2 class="box_head" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">Create Questions [Ball by ball]</h2>

            <?php
                $innings = 1;
                $over = 1;
                $ball = 1;
                foreach($get_question as $val) :
                    $over_data = $this->db->query("SELECT * FROM match_option WHERE match_id='{$val->id}' AND status IN(0,1) AND question_type='BALL_BY_BALL' ORDER BY id DESC LIMIT 1")->row();
                    if(!empty($over_data)) {
                        $over = $over_data->over;
                        $innings = $over_data->innings;
                        $ball = $over_data->ball;

                        if($ball == 6) {
                            $over = $over + 1;
                            $ball = 1;
                        }
                        else {
                            $ball = $ball + 1;
                        }
                    }
                    else {
                        $innings = 1;
                        $over = 1;
                        $ball = 1;
                    }

                    // set answers from sessions
                    $answer_1 = "";
                    $answer_2 = "";
                    $answer_3 = "";
                    $answer_4 = "";
                    $answer_5 = "";
                    $answer_6 = "";
                    $answer_7 = "";
                    $answer_8 = "";
                    $answer_9 = "";

                    $ratios_1 = "";
                    $ratios_2 = "";
                    $ratios_3 = "";
                    $ratios_4 = "";
                    $ratios_5 = "";
                    $ratios_6 = "";
                    $ratios_7 = "";
                    $ratios_8 = "";
                    $ratios_9 = "";
            ?>
                <form action="" method="post" accept-charset="utf-8" class="validate_form" novalidate="novalidate">
                    <input type="hidden" name="match_id" value="<?php echo $val->id ?>">

                    <div class="grid_16">
                        <h2 class="box_head grad_black" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">
                            <img src="<?php echo base_url(); ?>assets/admin/img/cricket.gif" style="float:left;" height="30">
                            <span style="float:left;"><?php echo $val->title; ?></span>
                        </h2>
                        <div class="grid_16" style="margin-top:10px;margin-bottom: 10px;">
                            Over : <input type="number" value="<?= $over; ?>" name="over">
                            &nbsp;&nbsp;&nbsp;Ball : <input type="number" value="<?= $ball; ?>" name="ball" class="valid">
                            &nbsp;&nbsp;&nbsp;Innings : <input type="number" value="<?= $innings; ?>" name="innings">
                        </div>


                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]">
                            <input type="text" placeholder="Ratio.." value="" class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]">
                            <input type="text" placeholder="Ratio.." value="" class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]">
                            <input type="text" placeholder="Ratio.." value="" class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]">
                            <input type="text" placeholder="Ratio.." value="" class="col_160" name="ratios[]">
                        </div>

                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]"><br>
                            <input type="text" placeholder="Ratio.." class="col_160" name="ratios[]">
                        </div>
                        <!-- 1st row -->

                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]"><br>
                            <input type="text" placeholder="Ratio.." class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]"><br>
                            <input type="text" placeholder="Ratio.." class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]"><br>
                            <input type="text" placeholder="Ratio.." class="col_160" name="ratios[]">
                        </div>
                        <div class="col_20" style="margin-top:10px; margin-bottom: 10px;">
                            <input type="text" value="" placeholder="Answer..." name="answers[]"><br>
                            <input type="text" placeholder="Ratio.." class="col_160" name="ratios[]">
                        </div>
                        <!-- 2nd row -->
                    </div>

                    <div class="button_bar clearfix">
                        <input style="background-color:#4c5766;color:#FFF;" class=" btn text_only has_text" type="submit" name="submit" value="Submit">
                    </div>

                </form>
            <?php endforeach; ?>

        </div>

        <form action="https://sporty24.net/admin/ballbyball" method="post" accept-charset="utf-8" class="validate_form" novalidate="novalidate">

            <div id="main_container_ajax" class="box grid_16" style="opacity: 1;">
                <h2 class="box_head" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">Change Ratio</h2>

                <?php $betIds=[]; $betOpIds=[]; ?>
                <?php foreach($get_question as $val) : ?>
                    <div class="grid_16" id="question-id-1841">
                        <h2 class="box_head grad_black match-grid" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">
                            <img src="<?php echo base_url(); ?>assets/admin/img/cricket.gif" style="float:left;" height="30">
                            <span style="float:left;"><?php echo $val->title; ?></span>
                        </h2>

                        <?php
                            
                        $bet_data = $this->db->query("SELECT * FROM match_option WHERE match_id='{$val->id}' AND status IN(0,1) AND question_type='BALL_BY_BALL' ORDER BY id DESC LIMIT 1")->row();
                        if(!empty($bet_data)) :

                        $betIds[] = $bet_data->id;

                        $options = $this->db->query("SELECT * FROM match_option_details WHERE match_option_id='{$bet_data->id}' ORDER BY option_serial ASC")->result();
                        ?>

                        <div class="box light grid_16 question-grid set-win-<?= $bet_data->id; ?>" id="answer-id-15938" style="opacity: 1;">
                            <h2 class="box_head question-head"><?php echo $bet_data->match_option_title; ?>&nbsp;&nbsp;&nbsp;

                                <!-- <b id="question_status_text_15938" style="font-size:14px;"> </b> -->
                                <span style="font-size:14px;font-weight:bold;color:#333" class="bet_status_<?= $bet_data->id; ?>">[ Active ] </span>

                                &nbsp;&nbsp;&nbsp;
                                <select onchange="return updateBetStatus(<?= $bet_data->id ?>, this.value)" name="bet_status">
                                    <option value="1" selected="selected">Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="2">Disabled</option>
                                </select>
                                <?php
                                    $tot_bet = $this->db->query("SELECT SUM(`bet_coin`) AS total_bet FROM matchbit_coin WHERE match_bet_option_id='{$bet_data->id}'")->row();
                                ?>
                                &nbsp;&nbsp;Total Bet : <span class="total-bet-<?php echo $bet_data->id; ?>"><?= $tot_bet->total_bet ?></span>&nbsp;&nbsp;<a href="<?php echo base_url('show_bet_list/'.$bet_data->id) ?>" target="_blank">Bet List </a>
                            </h2>

                            <div class="answer-grid">

                                <?php
                                    foreach($options as $op) :
                                        $id = $op->id; $ratio = $op->option_coin;
                                        $betOpIds[] = $id; 
                                ?>

                                    <div class="col_20" style="margin-top:5px;">
                                        <div style="text-align: center;" class="col_75"><?php echo $op->option_title; ?></div>
                                        <div id="answer_success_46881" class="answer_success">
                                            <input
                                                onfocusout="return updateBetRatio(<?= $id ?>, this.value, <?= $bet_data->id; ?>)"
                                                data-id="<?= $id ?>"
                                                data-span_id="<?= $bet_data->id; ?>"
                                                class="answer-ratio col_160 ratioUpdate"
                                                type="text"
                                                value="<?php echo $op->option_coin; ?>">

                                            <!-- <span class="tooltiptext"></span><span class="tooltiptext2"></span> -->
                                        </div>
                                        <div id="tb-tr-<?= $id ?>" style="text-align: center;" class="col_75">TB:&nbsp;0&nbsp;&nbsp;TR:&nbsp;0</div>
                                    </div>

                                <?php endforeach; ?>
                                <span class="save-msg" id="save-msg-<?= @$bet_data->id; ?>"></span>

                            </div>
                        </div>

                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>



            </div>
            <!-- end change ratio -->

            <div class="box grid_16" style="opacity: 1;">
                <h2 class="box_head" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">Winner Selection</h2>

                <?php foreach($get_question as $val) : ?>
                <div class="grid_16" id="question-id-1841">
                    <h2 class="box_head grad_black match-grid" id="match_abandoned_success_1841" style="float:left;width:99.1%; padding: 4px; margin-bottom: 5px; ">
                        <img src="<?php echo base_url(); ?>assets/admin/img/cricket.gif" style="float:left;" height="30">
                        <span style="float:left;"><?php echo $val->title; ?></span>
                    </h2>

                    <?php
                        
                    $winner_data = $this->db->query("SELECT * FROM match_option WHERE match_id='{$val->id}' AND status IN(0,1,2) AND question_type='BALL_BY_BALL' ORDER BY id DESC")->result();
                    if(!empty($winner_data)) :

                        foreach($winner_data as $data) :

                            $options = $this->db->query("SELECT * FROM match_option_details WHERE match_option_id='{$data->id}' ORDER BY option_serial ASC")->result();
                            ?>

                            <div class="box light grid_16  question-grid div-<?php echo $data->id; ?>" id="answer-id-15934" style="opacity: 1;">
                                <h2 class="box_head question-head"><?php echo $data->match_option_title; ?>
                                    &nbsp;&nbsp;<a href="<?php echo base_url('show_bet_list/'.$data->id) ?>" target="_blank">Bet List </a>

                                    <!-- <input type="button" value="Return" class="btn btn-lg btn-danger question_return button icon_only text_only" style="margin-left: 30px; float:none; padding: 0px 10px;" id="question_return_15934"> -->

                                    <a
                                    style="margin-left: 30px; float:none; padding: 0px 10px;"
                                    class="btn btn-lg btn-danger question_return button icon_only text_only"
                                    onclick="return confirm('Are you sure to cancel this bet?');"
                                    href="<?php echo base_url('admin/bet_match_cancel/'.$data->id) ?>">Return</a>

                                    <span class="tooltiptext"></span>
                                </h2>

                                <div class="answer-grid">

                                    <?php
                                    if(isset($options)) {
                                        foreach($options as $op) { 
                                            
                                        $id = $op->id; 
                                        $ratio = $op->option_coin;
                                        $betOpIds[] = $id;
                                    ?>

                                    <div class="col_20" style="margin-top:5px;">
                                        <div style="text-align: center;" class="col_80"><?= !empty($op->option_title) ? $op->option_title : ''; ?></div>
                                        <div id="answer_success2_46865" class="answer_success col-100">
                                            <!-- <input type="button" value="1.2" class="btn btn-lg answer col_80 button icon_only text_only" id="answer2_46865"> -->

                                            <a
                                            data-match_id="<?php echo $data->id; ?>"

                                            data-option_id="<?php echo $op->id; ?>"
                                            onclick="return setBetWinner('<?php echo $data->id; ?>', '<?php echo $op->id; ?>'); confirm('Are you sure to win this answer?');"
                                            class="btn btn-lg answer col_80 button icon_only text_only bet-winner-btn">
                                                <?php echo $op->option_coin; ?>
                                            </a>

                                            <!-- <span class="tooltiptext"></span><span class="tooltiptext2"></span> -->


                                            <span id="toolTipText_<?= $op->id; ?>" class="kakunii" style=""></span>

                                        </div>
                                    </div>

                                    <?php 
                                            }
                                        }
                                    
                                    ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            </div>
        </form>



    </div>


    <style>
        .answer_success {
            position: relative;
            display: inline-block;
            width: 100%;
            /* If you want dots under the hoverable text */
        }
        .box_head {
            position: relative;
            display: inline-block;
            width: 100%;
            /* If you want dots under the hoverable text */
        }
        .btn
        {
            cursor: pointer;
        }

        /* Tooltip text */
        .answer_success .tooltiptext {
            /*display: none;*/
            width: 120px;
            background-color: #E6ECEF;
            color: green;
            text-align: center;
            padding: 5px 0;
            border-radius: 0px;
            position: absolute;
            z-index: 1000;
            left: -38px;
            top: 35px;
        }
        .answer_success .tooltiptext2 {
            /*display: none;*/
            width: 250px;
            background-color: #000;
            color: white;
            text-align: center;
            padding: 5px 0;
            border-radius: 0px;
            position: absolute;
            z-index: 1000;
            left: -38px;
            top: 35px;
        }
        .container_12 .grid_6, .container_16 .grid_8 {
            width: 47.8%;
        }
        .answer
        {
            text-align: center;
        }
        .match-grid
        {
            cursor: pointer;
        }
        .question-head
        {
            cursor: pointer;
        }
        input
        {
            text-align: center;
        }
        .col_75
        {
            width:65%;
        }


    </style>




<!-- </div> -->

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
                    } else if(status==0) {
                        $("span.bet_status_"+bet_id).text('[Inactive]');
                    } else if(status==2) {
                        $("span.bet_status_"+bet_id).text('[Disabled]');
                        $(".set-win-"+bet_id).hide();
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

    (function() {

        $(".ratioUpdate").on('keyup', function (e) {
            var id = $(this).data('id');
            var coin = $(this).val();
            var span_id = $(this).data('span_id');

            if (e.keyCode === 13) {
                updateBetRatio(id, coin, span_id);
            }
        });
        // ---

        // setInterval(function () {totalBetCount()}, 8000);
        // setInterval(function () {totalRatioCount()}, 15000);

        function totalBetCount() {
            var betIds = <?php echo json_encode($betIds); ?>;

            var url_prefix = "<?php echo base_url(); ?>";
            var bet_id = betIds;

            // ajax request
            $.ajax({
                type: "POST",
                url: url_prefix + 'admin/update_total_bet_count',
                data: {
                    bet_id: bet_id
                },
                dataType: 'json',
                success: function(data) {
                    if(data.error==0) {
                        $.each(data.success_msg, function(index, item) {
                            $(".total-bet-"+index).text(item);
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

        // $(".panel-collapse").hide();

    })(jQuery);

    // -- for winner selection
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
        $(".tooltiptext2").text("");
        $(".kakunii").removeClass("tooltiptext2");
    });

    $(".bet-winner-btn").on('mouseover', function() {
        var match_id = $(this).data('match_id');
        var option_id = $(this).data('option_id');
        var url_prefix = "<?php echo base_url(); ?>";
        // -- if no need then delete
        $(".tooltiptext2").text("");
        $(".kakunii").removeClass("tooltiptext2");

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
                console.log(data);
                if(data.error==0) {
                    // after success
                    $("span#toolTipText_"+option_id).addClass("tooltiptext2").html(data.success_msg);
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