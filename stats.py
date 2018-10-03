#!/usr/bin/python3

import sys, socket, json

CONTROLLER_IP = '192.168.210.1'
CONTROLLER_PORT = 10101

runid = None
pretty = None

for arg in sys.argv:
    if arg.startswith("--runid="): runid = arg.split("=")[1]
    elif arg == "--pretty": pretty = True
    elif arg.startswith("--controller="): 
        argval = arg.split("=")[1]
        if ":" in argval:
            CONTROLLER_IP, CONTROLLER_PORT = argval.split(":")
            CONTROLLER_PORT = int(CONTROLLER_PORT)
        else:
            CONTROLLER_IP = argval

if runid is None:
    print("Argument error; missing --runid=<unique runid for this test>")

if None in (runid,):
    print("Usage:\n\t./stats.py --runid=<unique run id> [--pretty] [--controller=<controller_ip[:controller_port]>]")
    sys.exit(0)

cmd = {}
cmd["command"] = "STATS"
cmd["runid"] = runid

pretty = False if pretty is None else pretty

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

    if pretty:
        print()
        stats = json.loads(response)
        for s in ("sn", "init", "startGame", "play", "purchase"):
            print("%15s (%4s clients): %9.2f ms (%d errors)" % (s, stats[s + "Clients"], float(stats[s]), stats[s + "_errors"]))
        percent = float(stats["percentComplete"])
        percent *= 100
        print("\nComplete: %.2f%%\n" % percent)
    else:
        print(response)
else:
    print("Controller wasn't ready: %s" % ready)

