#!/usr/bin/python3

import sys, socket, json

CONTROLLER_IP = '192.168.210.1'
CONTROLLER_PORT = 10101

gsip = None
sim_time = None
n = None
lab = None
runid = None
snip = None

for arg in sys.argv:
    if arg.startswith("--gsip="): gsip = arg.split("=")[1]
    elif arg.startswith("--snip="): snip = arg.split("=")[1]
    elif arg.startswith("--time="): sim_time = arg.split("=")[1]
    elif arg.startswith("--n="): n = arg.split("=")[1]
    elif arg.startswith("--lab="): lab = arg.split("=")[1]
    elif arg.startswith("--runid="): runid = arg.split("=")[1]
    elif arg.startswith("--controller="): 
        argval = arg.split("=")[1]
        if ":" in argval:
            CONTROLLER_IP, CONTROLLER_PORT = argval.split(":")
            CONTROLLER_PORT = int(CONTROLLER_PORT)
        else:
            CONTROLLER_IP = argval

if gsip is None:
    print("Argument error: missing --gsip=<game server IP>")
#if snip is None:
#    print("Argument error: missing --snip=<social network IP>")
if sim_time is None:
    print("Argument error: missing --time=<simulating time in seconds>")
if lab is None:
    print("Argument error: missing --lab=<lab folder>")
if n is None:
    print("Argument error: missing --n=<number of simulation clients>")
if runid is None:
    print("Argument error; missing --runid=<unique runid for this test>")

if None in (gsip, sim_time, lab, n, runid):
    print("Usage:\n\t./sim.py --gsip=<gsip> --snip=<snip> --lab=<lab> --time=<num_seconds> --n=<num_clients> [--controller=<controller_ip[:controller_port]>]")
    sys.exit(0)

cmd = {}
cmd["command"] = "SIM"
cmd["gsip"] = gsip
cmd["snip"] = CONTROLLER_IP if snip is None else snip
cmd["time"] = sim_time
cmd["n"] = n
cmd["lab"] = lab
cmd["runid"] = runid

try:
    s = socket.socket()
    s.connect((CONTROLLER_IP, CONTROLLER_PORT))
except:
    print("Unable to connect to controller at %s:%s" % (CONTROLLER_IP, CONTROLLER_PORT))
    sys.exit(0)

ready = s.recv(1024).decode("UTF-8")
if ready == "READY\n":
    cmd_json = json.dumps(cmd)
    s.send((cmd_json + "\n").encode("UTF-8"))
    response = ""
    while "\n" not in response:
        response += s.recv(1024).decode("UTF-8")

    print("CONTROLLER RESPONSE: %s" % response)

else:
    print("Controller wasn't ready: %s" % ready)

