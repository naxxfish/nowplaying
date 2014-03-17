nowplaying
==========

Now Playing Processor thing

# Overview

The Now Playing Processor thing uses a Message Queue server to accept, filter and deliver Now Playing data from a playout system or other source to various destinations.  For example:

Myriad OCP -> Input (OCP) -> MusicBrainz -> iTunes Matcher -> Icecast / TuneIn / Last.fm / RadioVIS

It is multi-processed and so can do all of these things in parallel. 

# Installation

Copy the directory somewhere, and do `composer install` on it to get the required libraries.  

Point your web server at ./wwwroot/ as the Document Root.  You should be able to access input.php but not much else.

You will need PHP 5.3 or greater.  You'll also need the Stomp extension
http://www.php.net/manual/en/book.stomp.php

You'll need a Message Queue server, like ActiveMQ.  

# Configuration

Configuration files live in ./config/

## defaults.php
Contains the default options for the Now Playing thingy

## custom.php
Look in defaults.php, and copy the section at the end into a new file called custom.php.  Edit these lines to have the right settings (should be self documented)


## stomp.php
This file contains all of the ActiveMQ queues and settings.  Edit these to match your installation.  The queues are used by the sources, filters and sinks.  


# Running It

To run the services that convey messages, run runservices.php in the root.  This will start up several php processes which will attach themselves to the queues.  When a message enters, they will 
process that object and perform any actions configured to deliver them out the other end.  

You will need to point your playout system's metadata output at /input.php, via HTTP GET.  Use the query string:

/input.php?source=ocp&track=<trackname>&artist=<artistname>

This will be the entry point to the system.  