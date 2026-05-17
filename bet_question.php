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
                    <div class="m-heading-1 border-green m-bordered">
                        <h3>Manage Bet Questions</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN EXAMPLE TABLE PORTLET-->
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <!-- <div class="caption font-dark">
                                        <i class="icon-settings font-dark"></i>
                                        <span class="caption-subject bold uppercase"> Managed Table</span>
                                    </div> -->
                                    <div class="actions">
                                        <div class="btn-group">
                                            <button
                                            data-toggle="modal"
                                            data-target="#depositAccountModal"
                                            id="sample_editable_1_new"
                                            class="btn sbold green"> Add New
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-striped table-bordered table-hover  order-column ajax-tbl">
                                        <thead>
                                            <tr>
                                                <th>Sports Name</th>
                                                <th>Question</th>
                                                <th>Serial</th>
                                                <th>Answer Type</th>
                                                <th>Answers</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($get_data as $val) : ?>
                                                <tr class="odd gradeX">
                                                    <td><?= $val->name; ?></td>
                                                    <td><?= $val->question_title; ?></td>
                                                    <td><?= $val->serial; ?></td>

                                                    <td>
                                                        <?php if($val->answer_type=='NOT_SET') { ?>
                                                            <span class="badge badge-danger">Not Set</span>
                                                        <?php } else if($val->answer_type=='TEAM_NAME') { ?>
                                                            <span class="badge badge-info">Team Name</span>
                                                        <?php } else if($val->answer_type=='TEAM_NAME_WITH_DRAW') { ?>
                                                            <span class="badge badge-primary">Team with Draw</span>
                                                        <?php } else if($val->answer_type=='CUSTOM') { ?>
                                                            <span class="badge badge-success">Custom</span>
                                                        <?php } else if($val->answer_type=='TEAM_NAME_WITH_TIE') { ?>
                                                            <span class="badge badge-info">Team with Tie</span>
                                                        <?php } else if($val->answer_type=='TEAM_NAME_WITH_NOGOAL') { ?>
                                                            <span class="badge badge-warning">Team with No Goal</span>
                                                        <?php } ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                            if($val->answer_type=='CUSTOM') {
                                                                $ans = $this->db->query("SELECT answer FROM bet_answers WHERE question_id='$val->id'")->result();
                                                                if(!empty($ans)) {
                                                                    $ans_data = [];
                                                                    foreach($ans as $v) {
                                                                        $ans_data[] = $v->answer;
                                                                    }
                                                                    echo implode(" | ", $ans_data);
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if($val->status=='1') { ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php } else if($val->status=='0') { ?>
                                                            <span class="badge badge-danger">Inactive</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <a
                                                        class="account-modal btn btn-success btn-xs"
                                                        data-id="<?php echo $val->id; ?>"
                                                        data-serial="<?php echo $val->serial; ?>"
                                                        data-sports_id="<?php echo $val->sports_id; ?>"
                                                        data-question_title="<?php echo $val->question_title; ?>"
                                                        data-status="<?php echo $val->status; ?>"
                                                        data-toggle="modal"
                                                        data-target="#editAccountModal"
                                                        title="Edit Account">
                                                            Edit
                                                        </a>

                                                        <?php if($val->answer_type == 'NOT_SET') : ?>
                                                        <a
                                                        class="answer-modal btn btn-info btn-xs"
                                                        data-id="<?php echo $val->id; ?>"
                                                        data-sports_id="<?php echo $val->sports_id; ?>"
                                                        data-question_title="<?php echo $val->question_title; ?>"
                                                        data-toggle="modal"
                                                        data-target="#createQuestionModal"
                                                        title="Edit Account">
                                                            Add Answer
                                                        </a>
                                                        <?php endif; ?>

                                                        <?php if($val->answer_type != 'NOT_SET') : ?>
                                                            <a
                                                            class="edit-answer-modal btn btn-primary btn-xs"
                                                            href="<?php echo base_url('admin/view_question_answer/'.$val->id.'/'.$val->answer_type); ?>"
                                                            title="View Answers">
                                                                View
                                                            </a>
                                                            
                                                            <a
                                                            class="edit-answer-modal btn btn-danger btn-xs"
                                                            href="<?php echo base_url('admin/remove_question_answer/'.$val->id.'/'.$val->answer_type); ?>"
                                                            onclick="return confirm('Are you sure to remove this question answer?');"
                                                            title="Delete Answer">
                                                                Delete
                                                            </a>
                                                        <?php endif; ?>

                                                        <!-- <a
                                                        class="btn btn-xs btn-danger"
                                                        onclick="return confirm('Are you sure to remove this question?');"
                                                        href="<?php //echo base_url('admin/remove_bet_question/'.$val->id); ?>">
                                                            Delete
                                                        </a> -->
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>
                    </div>
                    
                    <!-- END PAGE BASE CONTENT -->
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->

        </div>
        <!-- END CONTAINER -->

        <div id="depositAccountModal" class="modal fade" tabindex="-1" data-width="400">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Create Bet Question</h4>
                    </div>
                    <div class="modal-body" style="padding:20px !important;">
                        <div class="row">
                            <div class="col-md-12">

                                <form action="<?php echo base_url('admin/create_bet_question'); ?>" method="POST">

                                    <div class="form-group">
                                        <label class="control-label mb-1">Sports Name</label>
                                        <select name="sports_id" class="form-control" required>
                                            <option value="">-- select --</option>
                                            <?php foreach($sports as $val) : ?>
                                                <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label mb-1">Question Title</label>
                                        <input type="text" name="question_title" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label mb-1">Serial</label>
                                        <input type="number" name="serial" class="form-control" required>
                                    </div>

                                    <div>
                                        <input type="submit" class="btn btn-sm btn-info btn-block" name="submit" value="Submit">
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="editAccountModal" class="modal fade" tabindex="-1" data-width="400">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Edit Question</h4>
                    </div>
                    <div class="modal-body" style="padding:20px !important;">
                        <div class="row">
                            <div class="col-md-12">

                                <form action="<?php echo base_url('admin/edit_bet_question'); ?>" method="POST">

                                    <input id="question_id" type="hidden" name="question_id">

                                    <div class="form-group">
                                        <label class="control-label mb-1">Sports Name</label>
                                        <select id="sports_id" name="sports_id" class="form-control" required>
                                            <option value="">-- select --</option>
                                            <?php foreach($sports as $val) : ?>
                                                <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label mb-1">Question Title</label>
                                        <input id="question_title" type="text" name="question_title" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label mb-1">Status</label>
                                        <select id="acc_status" name="status" class="form-control">
                                            <option value="">-- select --</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label mb-1">Serial</label>
                                        <input id="serial" type="number" name="serial" class="form-control" required>
                                    </div>

                                    <div>
                                        <input type="submit" class="btn btn-sm btn-info btn-block" name="submit" value="Submit">
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- question modal -->
        <div id="createQuestionModal" class="modal fade" tabindex="-1" data-width="400">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Create Match Question</h4>
                    </div>
                    <div class="modal-body" style="padding:20px !important;">
                        <div class="row">
                            <div class="col-md-12">

                                <form action="<?php echo base_url('admin/create_answer_option'); ?>" method="post">
                                    <input id="q_id" type="hidden" name="question_id">

                                  <div class="row">

                                    <div class="col-md-10 col-xs-12">
                                        <div class="form-group has-success">
                                            <label for="match_option_name" class="control-label mb-1">Question: </label>
                                            <b><span id="q_title"></span></b>
                                            <!-- <input id="q_title" name="question_name" type="text" class="form-control" required> -->
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xs-8">
                                        <div class="form-group has-success">
                                            <label for="match_option_name" class="control-label mb-1">Answer Type</label>
                                            <select id="match_option_name" name="match_option_name" class="form-control" required>
                                                <!-- <option value="NOT_SET">--- select ---</option> -->
                                                    <option value="CUSTOM">CUSTOM</option>
                                                    <option value="TEAM_NAME">TEAM NAME</option>
                                                    <option value="TEAM_NAME_WITH_DRAW">TEAM NAME WITH DRAW</option>
                                                    <option value="TEAM_NAME_WITH_NOGOAL">TEAM NAME WITH NOGOAL</option>
                                                    <option value="TEAM_NAME_WITH_TIE">TEAM NAME WITH TIE</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-1 col-xs-2 hideClass">
                                      <div class="form-group">
                                        <a id="add" style="color:#fff;margin-top:25px;" class="btn btn-success"> <i class="fa fa-plus"></i> </a>
                                      </div>
                                    </div>
                                    <div class="col-md-1 col-xs-2 hideClass">
                                      <div class="form-group">
                                        <a id="minus" style="color:#fff;margin-top:25px;" class="btn btn-danger"> <i class="fa fa-minus"></i> </a>
                                      </div>
                                    </div>

                                  </div>

                                    <!-- <div class="form-group has-success">
                                        <label for="match_option_serial" class="control-label mb-1">Serial</label>
                                        <input required name="match_option_serial" type="text" class="form-control">
                                    </div> -->

                                    <div class="hideClass">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                                              <div class="form-group has-success">
                                                  <label for="first_option_title" class="control-label mb-1">Answer</label>
                                                  <input name="first_option_title[]" type="text" class="form-control hideReq" value="" required>
                                              </div>
                                            </div>

                                            <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                              <div class="form-group has-success">
                                                  <label for="first_option_coin" class="control-label mb-1">Coin</label>
                                                  <input name="first_option_coin[]" type="text" class="form-control" required>
                                              </div>
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                                              <div class="form-group has-success">
                                                  <label for="first_option_title" class="control-label mb-1">Answer</label>
                                                  <input name="first_option_title[]" type="text" class="form-control hideReq" value="" required>
                                              </div>
                                            </div>

                                            <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                              <div class="form-group has-success">
                                                  <label for="first_option_coin" class="control-label mb-1">Coin</label>
                                                  <input name="first_option_coin[]" type="text" class="form-control" required>
                                              </div>
                                            </div> -->
                                        </div>

                                        <div class="row option-data" style="display:none">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                                              <div class="form-group has-success">
                                                  <label for="first_option_title" class="control-label mb-1">Answer</label>
                                                  <input name="first_option_title[]" type="text" class="form-control">
                                              </div>
                                            </div>

                                            <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                              <div class="form-group has-success">
                                                  <label for="first_option_coin" class="control-label mb-1">Coin</label>
                                                  <input name="first_option_coin[]" type="text" class="form-control">
                                              </div>
                                            </div> -->
                                        </div>

                                        <div class="add-options"></div>
                                    </div>

                                    <div>
                                        <input type="submit" name="submit" value="Submit" class="btn btn-sm btn-info btn-block">
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                    </div>
                </div>
            </div>
        </div>

<?php include(APPPATH."views/admin/common/footer.php"); ?>
<script>
    $(document).ready(function() {
        $('.ajax-tbl').DataTable({
            // "scrollX": true,
            ordering: false
        });
    } );

    $('.account-modal').on('click', function() {
        var data_id = $(this).attr('data-id');
        var data_serial = $(this).attr('data-serial');
        var data_sports_id = $(this).attr('data-sports_id');
        var data_question_title = $(this).attr('data-question_title');
        var data_status = $(this).attr('data-status');

        $('#question_id').attr('value', data_id);
        $('#question_title').attr('value', data_question_title);
        $('#sports_id').val(data_sports_id);
        $('#acc_status').val(data_status);
        $('#serial').val(data_serial);
    });

    $('.answer-modal').on('click', function() {
        var data_id = $(this).attr('data-id');
        var data_question_title = $(this).attr('data-question_title');

        $('#q_id').attr('value', data_id);
        // $('#q_title').attr('value', data_question_title);
        $('#q_title').text(data_question_title);
    });

    $("#add").on("click", function() {
        $('.option-data:first').clone().show().addClass('option-data-new').appendTo('.add-options');
    });
    $("#minus").on("click", function() {
        // $('.option-data:last').remove();
        $('.option-data-new:last').remove();
    });

    $("#match_option_name").on("change", function() {
        var val = $(this).val();
        if(val == 'TEAM_NAME' || val == 'TEAM_NAME_WITH_DRAW' || val == 'NOT_SET' || val == 'TEAM_NAME_WITH_TIE' || val == 'TEAM_NAME_WITH_NOGOAL') {
            $('.hideClass').hide();
            $('.hideReq').removeAttr('required');
        } else if(val == 'CUSTOM') {
            $('.hideClass').show();
            $('.hideReq').attr('required', 'required');
        }
    });


</script>