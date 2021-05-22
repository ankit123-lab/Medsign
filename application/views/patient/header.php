<header class="header">
	<div class="container">
		<div class="row flexbox-center">
			<div class="col-lg-2 col-md-3 col-6 <?= !isset($isVideoCallActive) ? '' : 'text-center'; ?>">
				<div class="logo">
					<span style="width:133px;height:22px;font-size: 22px;font-weight: bold;color: white;">MedSign</span>
				</div>
			</div>
			<?php if (!isset($isVideoCallActive)) : ?>
				<div class="col-lg-10 col-md-9 col-6">
					<div class="responsive-menu"></div>
					<div class="mainmenu">
						<ul id="primary-menu">
							<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient">Home</a></li>
							<?php
							if ($this->patient_auth->is_logged_in()) { ?>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/appointment_list">Appointments</a></li>
								<!-- <li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Appointments</a>
									<div class="dropdown-menu" aria-labelledby="navbarDropdown">
										<a class="dropdown-item" href="<?= DOMAIN_URL; ?>patient/appointment_list">My Appointments</a>
										<a class="dropdown-item" href="<?= DOMAIN_URL; ?>patient/appointment_book">Book Appointment</a>
									</div>
								</li> -->
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/report">Records</a></li>
								<!-- <li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/vitals">Vitals</a></li> -->

								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Analytics</a>
									<div class="dropdown-menu" aria-labelledby="navbarDropdown">
										<a class="dropdown-item" href="<?= DOMAIN_URL; ?>patient/analytics_list">Health Tracker</a>
										<a class="dropdown-item" href="<?= DOMAIN_URL; ?>patient/utilities_list">Health Diary</a>
									</div>
								</li>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/share_data">Share Records</a></li>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/profile/update">Profile</a></li>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/add_issue">Help & Support</a></li>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/logout">Logout</a></li>
							<?php } else { ?>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/register">Register</a></li>
								<li class="nav-item"><a class="nav-link" href="<?= DOMAIN_URL; ?>patient/login">Login</a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</header>
