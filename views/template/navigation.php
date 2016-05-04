</article>								
		</main>
<nav id="left" class="column">
			<h3>Navigation</h3>
			<ul>
				
				<?php echo '';
				if (empty($this->session->userdata('Roll number')))
				{ echo '<li><a href="'.site_url("home").'">Home</a></li><li><a href="'.site_url("user/login").'">Login</a></li><li><a href="'.site_url("guest").'">Guest</a></li>';}
				else { echo '<li><a href="'.site_url("user/home").'">Home</a></li><li><a href="'.site_url("user/logout").'">Logout</a></li><li><a href="'.site_url("user/loaduserdata").'">View or update user details</a></li>';}//<li><a href="'.site_url("user/updatemarkslist").'">View or mark list</a></li>';} ?>
			</ul>
		</nav>