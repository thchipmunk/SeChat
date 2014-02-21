<!DOCTYPE html>
<html lang="en">
	<head>
		<title>SeChat</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="./assets/css/main.css">
	</head>
	<body>
		<div class="container">
			<h3>SeChat <span class="connection-status label label-danger">Disconnected</span></h3>
			<div class="form">
				<div class="form-group col-sm-12">
					<label for="passphrase">Passphrase</label>
					<input type="password" class="form-control" id="passphrase" placeholder="Passphrase">
				</div>
				<div class="form-group col-sm-12">
					<button class="btn btn-primary" id="generate-keys">Generate</button>
				</div>
				<div class="form-group col-sm-6">
					<label for="username">Username</label>
					<input type="text" class="form-control" id="username" placeholder="Username" value="">
				</div>
				<div class="form-group col-sm-6">
					<label for="connect_username">Connect To</label>
					<input type="text" class="form-control" id="connect_username" placeholder="Connect To" value="">
				</div>
				<div class="form-group col-sm-12">
					<button class="btn btn-primary hide" id="connect">Connect</button>
				</div>
			<div class="form">
				<div id="chat" class="hide col-sm-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">Chat Window</h3>
						</div>
						<div class="panel-body" id="chat-window"></div>
					</div>
					<div class="form-group" class="hide col-sm-12">
						<label for="msg">Message</label>
						<input id="msg" type="textbox" class="form-control" value="">
					</div>
					<button class="btn btn-primary" id="chat-send">Send</button>
				</div>
			</div>
		</div>
		<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
		<script src="./assets/js/cryptico.min.js"></script>
		<script src="./assets/js/app.js"></script>
	</body>
</html>
