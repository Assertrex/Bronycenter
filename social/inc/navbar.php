<aside id="aside-navbar">
	<div id="aside-navbar-userbox">
		<a href="profile.php?u=<?php echo $_SESSION['account']['id']; ?>">
			<div id="aside-userbox">
				<div id="aside-avatarbox">
					<img src="../media/avatars/default-x256.jpg" alt="Your avatar" />
				</div>
				<div id="aside-namebox">
					<span id="aside-displayname"><?php echo $_SESSION['user']['displayName']; ?></span>
					<span id="aside-username">@<?php echo $_SESSION['user']['username']; ?></span>
				</div>
			</div>
		</a>
	</div>

	<div id="aside-navbar-links">
		<ul style="margin: 0; padding: 0 1rem; list-style: none;">
			<a href="index.php"><li id="aside-link-feed">Public feed</li></a>
			<a href="messages.php"><li id="aside-link-messages">Messages</li></a>
			<a href="settings.php"><li id="aside-link-settings">Settings</li></a>
			<a href="../index.php"><li id="aside-link-homepage">Homepage</li></a>
			<a href="../logout.php"><li id="aside-link-logout">Logout</li></a>
		</ul>
	</div>

	<div id="aside-navbar-copyright">
		BronyCenter © 2017<br />
		22.05.2017 (v0.1.0 dev)
	</div>
</aside>
