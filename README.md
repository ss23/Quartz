PHP Bot utilizing IPC (Message Queues)

Roadmap
=======

For now, only the listener is semi-functional, but it should give an idea of how a receiver would attach and handle messages.

Currently, the only problem I can forsee is that it's a queue, once you read a message, it's removed from the queue. I could work around this by establishing a heirachy of recievers, or make every receiver enter a message into the queue after receiving it.

TODO
- [ ] write IRC part of this
- [ ] figure out a way to have more than one listener
- [ ] nice way of starting the bot(s) and listeners

Architecture
============

*A lot of this is probably wrong -- I can't say I've read any of the POSIX/System V message queue docs*

This project is the code for a multi _process_ IRC bot. That is, running a single bot, connected to a single network, will result in more than one process.

Message Queues
--------------

*Other IPC could probably be used just fine, I just thought this might make an interesting project to learn MQ for specifically*

To do the communication between processes, we'll be using [System V Message Queues (on Linux)](http://linux.die.net/man/2/msgsnd).
If you're familiar with sockets, you'll likely be used to how a socket identifier looks. `127.0.0.1:1337` is an example of what we might use to specify a socket. In the case of System V MQ, the identifier is a file, more like UNIX sockets.
We use [int ftok(string $pathname, string $proj)](http://www.php.net/manual/en/function.ftok.php) to get an identifier to use with the MQ functions.

The various MQ functions we use depend on whether we're the listener or the reciever. In general, we'll use the identifier we got with `ftok` to either read in messages, or write messages to the queue.

At this point, the "messages" that are being recieved (by `recieve.php`) are almost the same as you would expect from a socket, however, there is the added feature of message 'types'.
Because we're not connected ourselves, we need some way of knowing whether we disconnected from the server, and related information. The different types are documented in `constants.php`.
