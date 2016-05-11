<center>
        <form action="<?php echo site_url('user/updatemarkslist');?>" method="post">
        <?php $i = 1; while ($i <= 8) { echo '<br>'.$data['table'.$i].'<br>'.'GPA for sem '.$i.' is '.$data['gpa_'.$i].'<br>'; $i++;} echo 'CGPA is '.$data['cgpa'];?><br>
        <?php echo '<input type="submit" value="Update"></form>'; ?>
    </center>
</body>
</html>