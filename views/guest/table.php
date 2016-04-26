<html>
<head>
    <title><?php echo $type; ?></title>
</head>
<body>
    <center>
        <h2><?php echo $title; ?></h2><br>
        <form action="http://localhost/codeigniter-3.0.6/index.php/guest/result" method="post">
        <?php echo $sub_table; ?><br>
        <?php echo '<input type="hidden" name ="type" value="'.$type.'">'; ?>
        <?php echo '<input type="submit" value="Get my '.$type.'"></form>'; ?>
    </center>
</body>
</html>