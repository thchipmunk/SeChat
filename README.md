# SeChat - The Secure Chat

## About

### The Fancy
A simple chat interface utilizing websockets and assymetric crypto to facilitate secure communications (sounds fancy, huh?)

### The Basic
Basically, it uses the (Cryptico)[https://github.com/wwwtyro/cryptico/] JavaScript library to create public/private key-pair. The library also creates a username from the public key to send to a friend. The friend does the same. Once all "names" have been exchanged the two users connect. The public keys are exchanged via a JavaScript WebSocket and the two participants begin chatting. Their messages are encrypted using the recipients public key, sent to the server, and decrypted in the browser of the recipient using their private key.
