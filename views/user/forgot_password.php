<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="<?php echo base_url("bootstrap/css/bootstrap.css"); ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <?php echo $this->session->flashdata('msg'); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Login Form</h4>
            </div>
            <div class="panel-body">
                <?php $attributes = array("name" => "forgotpassword");
                echo form_open("user/forgot_password", $attributes);?>
                <div class="form-group">
                    <label for="name">Email ID</label>
                    <input class="form-control" name="email" placeholder="Email ID" type="text" value="" />
                    <span class="text-danger"><?php echo form_error('email'); ?></span>
                </div>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>