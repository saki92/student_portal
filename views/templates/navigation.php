</article>								
		</main>
<nav id="left" class="column">
			<h3>Left heading</h3>
			<ul>
				
				<?php echo '<li><a href="'.site_url("user/home").'">Home</a></li>';
				if (empty($this->session->userdata('Roll number')))
				{ echo '<li><a href="'.site_url("user/login").'">Login</a></li><li><a href="'.site_url("user/Guest").'">Guest</a></li>';}
				else { echo '<li><a href="'.site_url("user/logout").'">Logout</a></li><li><a href="'.site_url("user/updateuserdata").'">View or update user details</a></li><li><a href="'.site_url("user/updatemarklist").'">View or mark list</a></li>';} ?>
			</ul>
		</nav>