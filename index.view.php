<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="style.css" />
	<link rel="icon" href="https://madata.dev/logo.svg" />
	<title>Madata Official Authentication Provider</title>
</head>

<body>
	<h1>
		<img src="https://madata.dev/logo.svg" alt="Madata" />
		Madata Official Authentication Provider
	</h1>

	<?php if ($is_login): ?>
		<section id="auth">
			<p>Are you sure you want to login with <?= $backend ?> on <a href="<?= $url ?>"><?= $url ?></a>?</p>

			<footer class="buttons">
				<button class="yes" onclick='opener.postMessage({"backend": "<?= $backend ?>", "token": "<?= $token ?>"}, "<?= $url ?>"); window.close();'>Yes, log in</button>
				<button class="no" onclick="window.close();">No, I don’t recognize this site</button>
			</footer>
		</section>
	<?php else: ?>
		<section>
			Supported backends
			<ul id="backend_list">
				<?php if (!$backends): ?>
					<li>Fetching…</li>
				<?php else: ?>
					<?php foreach ($backends as $name => $meta): ?>
						<?php $id = strtolower($name); ?>
						<li class="<?= $id ?> backend">
							<img src="<?= $meta["icon"] ?? "img/default-logo.svg" ?>" alt="<?= $name ?> logo" />
							<a href="https://madata.dev/backends/<?= $id ?>"><?= $name ?></a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>

			<p>Want to learn more about Madata? Visit <a href="https://madata.dev">madata.dev</a>!</p>
		</section>
	<?php endif; ?>
</body>

</html>
