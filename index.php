<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	$backends = file_get_contents('services.json');
	$backends = json_decode($backends, true);

	$keys = file_get_contents('.secret.json');
	$keys = json_decode($keys, true);

	$is_login = isset($_REQUEST['state']);
	$token = '';

	if ($is_login) {
		$state = json_decode($_REQUEST['state'], true);
		$url = $state['url'];
		$backend = $state['backend'];
		$code = $_REQUEST['code'];
		$info = $backends[$backend] + $keys[$backend];

		if($code) {
			$ch = curl_init($info['url']);

			$params = array_key_exists('fields', $info) ? ($info['fields'] . '&') : '';
			$params = $params . 'client_id=$info[client_id]&client_secret=$info[client_secret]&code=$code';

			if (isset($_REQUEST['redirect_uri'])) {
				$redirect_uri = $_REQUEST['redirect_uri'];
			} else {
				$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
				$redirect_uri = $protocol . $_SERVER['HTTP_HOST'];
			}

			$params = $params . '&redirect_uri=$redirect_uri';

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$response = curl_exec($ch);

			curl_close($ch);

			$token = json_decode($response, true);
			$token = $token['access_token'];
		}
	}
?><!DOCTYPE html>
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
						<?php $id = strtolower($name) ?>
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
