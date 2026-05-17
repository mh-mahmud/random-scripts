<?php include(APPPATH."views/admin/common/header.php"); ?>


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

                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-red-sunglo bold uppercase">Static Answers: </span>
                                <span><?php echo $get_data[0]->serial.". ".$get_data[0]->question_title . " => ".$all_sports[$get_data[0]->sports_id]; ?></span>
                                
                            </div>
                            <span>
                                <a class="btn btn-danger pull-right" href="<?php echo base_url(); ?>bet_question">Back</a>
                            </span>
                        </div>
                        <div class="portlet-body form">
                            

                            <!-- BEGIN FORM-->
                            <form action="" class="form-horizontal" method="POST">

                                <div class="form-body">

                                    <?php if($answer_type=='TEAM_NAME') { ?>
                                        <input type="hidden" name="answer_id" value="<?php echo $get_data[0]->id ?>">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">1st Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_1" required class="form-control" value="<?php echo $get_data[0]->rate_1; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">2nd Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_2" required class="form-control" value="<?php echo $get_data[0]->rate_2; ?>">
                                            </div>
                                        </div>

                                    <?php } else if($answer_type=='TEAM_NAME_WITH_TIE') { ?>
                                        <input type="hidden" name="answer_id" value="<?php echo $get_data[0]->id ?>">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">1st Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_1" required class="form-control" value="<?php echo $get_data[0]->rate_1; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">2nd Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_2" required class="form-control" value="<?php echo $get_data[0]->rate_2; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Tie</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_3" required class="form-control" value="<?php echo $get_data[0]->rate_3; ?>">
                                            </div>
                                        </div>

                                    <?php } else if($answer_type=='TEAM_NAME_WITH_DRAW') { ?>
                                        <input type="hidden" name="answer_id" value="<?php echo $get_data[0]->id ?>">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">1st Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_1" required class="form-control" value="<?php echo $get_data[0]->rate_1; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">2nd Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_2" required class="form-control" value="<?php echo $get_data[0]->rate_2; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Draw</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_3" required class="form-control" value="<?php echo $get_data[0]->rate_3; ?>">
                                            </div>
                                        </div>

                                    <?php } else if($answer_type=='TEAM_NAME_WITH_NOGOAL') { ?>
                                        <input type="hidden" name="answer_id" value="<?php echo $get_data[0]->id ?>">
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">1st Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_1" required class="form-control" value="<?php echo $get_data[0]->rate_1; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">2nd Team</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_2" required class="form-control" value="<?php echo $get_data[0]->rate_2; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">No Goal</label>
                                            <div class="col-md-4">
                                                <input type="text" name="rate_3" required class="form-control" value="<?php echo $get_data[0]->rate_3; ?>">
                                            </div>
                                        </div>
                                    <?php } else if($answer_type=='CUSTOM') { ?>

                                    <?php } ?>


                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-4">
                                            <!-- <button type="submit" name="submit" class="btn green">Submit</button> -->
                                            <input type="submit" name="submit" value="Submit" class="btn green">
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>

                    <!-- END PAGE BASE CONTENT -->
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            

            <!-- BEGIN QUICK SIDEBAR -->
                <!-- deleted all the codes of this section -->
            <!-- END QUICK SIDEBAR -->
        </div>
        <!-- END CONTAINER -->

<?php include(APPPATH."views/admin/common/footer.php"); ?>
<script>
    $(document).ready(function() {
        $('.ajax-tbl').DataTable({
            // "scrollX": true,
            ordering: false
        });
    } );
</script>