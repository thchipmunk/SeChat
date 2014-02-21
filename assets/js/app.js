var	user_keys,
	id,
	chat_window,
	user_name,
	to_user,
	to_user_key,
	socket,
	host = 'wss://yourhost.com/ws';

$(function() {
	$('#chat-send').on('click', onChatSend);
	$('#generate-keys').on('click', onGenerateKeys);
	$('#connect').on('click', onConnect);
	
	$('#chat-send').on('keyup', onChatSend);
	
	
});

function onGenerateKeys() {
	var	pass_phrase = $('#passphrase').val(),
		bits = 1024,
		salt = Math.seedrandom();
	
	user_keys = cryptico.generateRSAKey(pass_phrase + salt, bits);
	
	public_key = cryptico.publicKeyString(user_keys);
	user_name = cryptico.publicKeyID(public_key);
	
	$('#public_key').val(public_key);
	$('#username').val(user_name);
	
	$('#connect').removeClass('hide');
}

function onChatOpen(msg) {
	$('.connection-status').text('Connected').removeClass('label-danger').addClass('label-success');
	$('#chat').removeClass('hide');
	
	socket.send(JSON.stringify({
		'status': 'connection',
		'user': user_name,
		'connect_user': to_user,
		'public_key': cryptico.publicKeyString(user_keys)
	}));
}

function onChatMessage(msg) {
	var returned = JSON.parse(msg.data);
	
	switch (returned.status) {
		case 'system':
			switchSystemMessage(returned);
			break;
		case 'client':
			switchUserMessage(returned);
			break;
	}
}

function switchUserMessage(data) {
	var decrypt_message = cryptico.decrypt(data.message, user_keys);
	
	data.message = decrypt_message.plaintext;
	
	updateChatWindow(data);
}

function switchSystemMessage(data) {
	if (data.public_key !== undefined) {
		to_user_key = data.public_key;
	}
}

function updateChatWindow(data) {
	var	div = document.createElement('div'),
		para = document.createElement('p'),
		user = document.createElement('strong');
	
	user.appendChild(document.createTextNode(data.from_user + ': '));
	para.appendChild(user);
	para.appendChild(document.createTextNode(data.message));
	
	$('#chat-window').append(para);
}

function onChatClose(msg) {
	$('.connection-status').text('Disconnected').removeClass('label-success').addClass('label-danger');
	$('#connect').removeClass('btn-danger').addClass('btn-primary').text('Connect');
	$('#chat').addClass('hide');
}

function onConnect() {
	if (
		(socket) &&
		(
			(socket.readyState === socket.OPEN) ||
			(socket.readyState === socket.CLOSING)
		)
	) {
		onChatDisconnect();
		return;
	}
	
	to_user = $('#connect_username').val();

	try {
		socket = new WebSocket(host);
		socket.onopen = onChatOpen;
		socket.onmessage = onChatMessage;
		socket.onclose = onChatClose;
	}
	catch(ex) {
		alert('Could not connect to socket');
		//Chat not connect
	}
	
	$('#connect').removeClass('btn-primary').addClass('btn-danger').text('Disconnect')
	$("#msg").focus();
}

function onChatSend() {
	var txt = $("#msg"),
		encrypted_message = cryptico.encrypt(txt.val(), to_user_key);
	
	var send_msg = {
		status: 'client',
		from_user: user_name,
		to_user: $('#connect_username').val(),
		message: encrypted_message.cipher
	};
	
	updateChatWindow({
		status: 'client',
		from_user: user_name,
		to_user: $('#connect_username').val(),
		message: txt.val()
	});
	
	if(!send_msg) {
		alert("Message can not be empty");
		return;
	}
	
	txt.val('');
	txt.focus();
	
	try {
		socket.send(JSON.stringify(send_msg));
	} catch(ex) {
		//Send exception
	}
}

function onChatDisconnect() {
	socket.close();
	socket = null;
	$('#chat-window').empty();
	$('#connect').removeClass('btn-danger').addClass('btn-primary').text('Connect');
}