<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login form</title>
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
                <h4>Login Form</h4>
            </div>
            <div class="panel-body">
                <?php $attributes = array("name" => "registrationform");
                echo form_open("user/login", $attributes);?>
                <div class="form-group">
                    <label for="name">Roll Number</label>
                    <input class="form-control" name="uname" placeholder="Roll Number" type="text" value="<?php echo set_value('uname'); ?>" />
                    <span class="text-danger"><?php echo form_error('uname'); ?></span>
                </div>

                <div class="form-group">
                    <label for="name">Password</label>
                    <input class="form-control" name="pword" placeholder="Password" type="password" value="<?php echo set_value('pword'); ?>" />
                    <span class="text-danger"><?php echo form_error('pword'); ?></span>
                </div>
				
				<div class="form-group">
                    <label for="check_entered"><?php echo $num1 . '+' . $num2; ?></label>
                    <input class="form-control" name="check_entered" placeholder="Enter the sum" type="text" value="<?php echo set_value('check_entered'); ?>" /><input type="hidden" name="check_sum" value="<?php echo $sum; ?>"/>
                    <span class="text-danger"><?php echo form_error('check_entered'); ?></span>
                </div>
				
				<div class="form-group">
                    <button name="submit" type="submit" class="btn btn-default">Login</button>
                    <button name="cancel" type="reset" class="btn btn-default">Reset</button>
                </div>
				<?php echo form_close(); ?>
                <?php echo $this->session->flashdata('login_status'); ?>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>