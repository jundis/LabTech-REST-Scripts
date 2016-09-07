# LabTech-REST-API

Various proof of concept PHP scripts using the undocumented REST API for LabTech.

Expect little to no updates to these scripts once posted.

## API Key Setup

1. Visit http://justanotherpsblog.com/2016/04/01/428/ and complete the powershell script variables at the top.  
Also available at https://gist.github.com/hematic/4286a68b3ba1d3835c7608e726b1e8d8#file-gistfile1-txt if site is down.
2. Run the Powershell script.
3. Copy the resulting key.

## offine-table.php

This file lists all machines with "Server" in their OS name which have been offline for at a minimum 10 minutes and at most 7 days, and outputs them to a poorly formatted HTML table.