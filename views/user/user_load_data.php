<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeIgniter User Registration Form Demo</title>
    <link href="<?php echo base_url("bootstrap/css/bootstrap.css"); ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?php echo $this->session->flashdata('verify_msg'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>User Data Update</h4>
            </div>
            <div class="panel-body">
                <?php $attributes = array("name" => "registrationform");
                echo form_open("user/loaduserdata", $attributes);?>
                <div class="form-group">
                    <label for="roll_no">Roll Number : </label><label for="name"><?php echo $roll_no;?></label>
                    <span class="text-danger"></span>
                </div>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" name="name" placeholder="Name" type="text" value="<?php echo $name; ?>" />
                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                </div>
                
                <div class="form-group">
                    <label for="college">College</label>
                    <input class="form-control" name="college" placeholder="College" type="text" value="<?php echo $college; ?>" />
                    <span class="text-danger"><?php echo form_error('college'); ?></span>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <input class="form-control" name="department" placeholder="Department" type="text" value="<?php echo $department; ?>"/>
                    <span class="text-danger"><?php echo form_error('department'); ?></span>
                </div>

                <div class="form-group">
                    <label for="start_year">Year of course start</label>
                    <input class="form-control" name="start_year" placeholder="Year" type="text" value="<?php echo $start_year; ?>" />
                    <span class="text-danger"><?php echo form_error('start_year'); ?></span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-default">Update</button>
                    <button name="cancel" type="reset" class="btn btn-default">Cancel</button>
                </div>
                <?php echo form_close(); ?>
                <?php echo $this->session->flashdata('load_status'); ?>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>