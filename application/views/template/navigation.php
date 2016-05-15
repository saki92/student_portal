</article>								
		</main>

<div class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm">
      <a class="navmenu-brand visible-md visible-lg" href="#">GPA CALC*</a>
      <ul class="nav navmenu-nav">
        <li>
        <div class="container">
        <div class="row">
          <div class="col-xs-3" align="center"><?php echo '<a href="'.site_url("user/home").'"';?>><img src="./img/ak.jpg" class="img-rounded" alt="pic" width="74" height="70"></a></div>
          <div class="col-xs-1"></div>
          <div class="col-xs-8 pull-right text-capitalize"><h4 style="color:#ecaf88;">Welcome back</h4><h4 style="color:#ecaf88;">User</br> Name</h4></div>
        </div>
      </div><h4 style="color:white;">
        </li>
        
				<?php if (empty($this->session->userdata('Roll number')))
				{ echo '<li><a href="'.site_url("home").'">Home</a></li><li><a href="'.site_url("user/login").'">Login</a></li><li><a href="'.site_url("user/register").'">Sighnup</a></li><li><a href="'.site_url("guest").'">Guest</a></li>';}
				else { echo '<li><a href="'.site_url("user/home").'">Home</a></li><li><a href="'.site_url("user/logout").'">Logout</a></li><li><a href="'.site_url("user/loaduserdata").'">View or update user details</a></li><li><a href="'.site_url("user/updatemarkslist").'">View or update mark list</a></li>';} ?>
			<li><a href="#">About us</a></li>
				<li><a href="#">Contact us</a></li>
    </div>

    <div class="navbar navbar-default navbar-fixed-top hidden-md hidden-lg">
      <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".navmenu">
        <span class="icon-bar"></span>
        <span class="icon-bar">
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">GPA CALCULATOR</a>
    </div>