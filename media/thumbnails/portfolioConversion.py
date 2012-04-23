import MySQLdb 
import MySQLdb.cursors
import os
from subprocess import call
from time import sleep
import random
import string


db = MySQLdb.connect(host="localhost", user="root", passwd="asap4u2u", db="newmediaportfolio", cursorclass=MySQLdb.cursors.DictCursor)


def random_key(length):
	key = ''
	for i in range(length):
		key += random.choice(string.lowercase + string.uppercase + string.digits)
	return key

def convert(id, filename):
	c = db.cursor()
	c.execute("SELECT * FROM REPO_Media WHERE media_id = %s", (str(id)))
	logfile = "/srv/nmd_log/"
	rows = c.fetchall()
	video_key = random_key(12)

	for row in rows:
		c.execute("UPDATE REPO_Media SET mimetype = converting, filename = %s WHERE media_id = %s", [video_key, (str(id))])
		call("mkdir /var/www/html/portfolio/videos/" + video_key, shell=True)
		call("ffmpeg -i " + filename + " -vcodec libx264 -threads 16 -b 250k -bt 50k -acodec libfaac -ab 56k -ac 2 -s" + " 720X480 " + "/var/www/html/portfolio/videos/" + video_key + "/" + video_key + ".mp4 2>" + logfile + video_key + ".mp4.txt 3> /dev/null 4>&1 &" , shell=True)
		call("ffmpeg -i " + filename + " -b 250k -vcodec libvpx -threads 16 -acodec libvorbis -ab 160000 -f webm -g 30 -s" + " 720X480 " + "/var/www/html/portfolio/videos/" + video_key + "/" + video_key + ".webm 2>" + logfile + video_key + ".webm.txt 3> /dev/null 4>&1 &" , shell=True)
		call("ffmpeg -i " + filename + " -b 250k -threads 16 -vcodec libtheora -acodec libvorbis -ab 160000 -g 30 -s" + " 720X480 " + "/var/www/html/portfolio/videos/" + video_key + "/" + video_key + ".ogv 2>" + logfile + video_key + ".ogv.txt 3> /dev/null 4>&1 &" , shell=True)
		call("ffmpeg -i " + filename + " -vframes 1 -an -r 1 -an -ss 00:00:10 -y /var/www/html/portfolio/videos/" + video + "/" + video + ".jpg >/dev/null 2>&1 &", shell=True)

def checkprogress(id,video_key):
	#print "Testing " + str(id)
	video_mp4 = "/srv/nmd_log/" + video_key + ".mp4.txt"
	video_webm = "/srv/nmd_log/" + video_key + ".webm.txt"
	video_ogv = "/srv/nmd_log/" + video_key + ".ogv.txt"
	c = db.cursor()
	c.execute("SELECT * FROM REPO_Media WHERE media_id = %s", (str(id)))

	rows = c.fetchall ()
	for row in rows:

		#Because of the 2nd command, we can't check mp4 anymore but that shouldn't matter as it is always the quickest.
		#x = readfile(video_mp4)
		x = 0;
		y = readfile(video_webm)
		z = readfile(video_ogv)
		mimetype = "video/mp4"
		filename = "/videos/" + video_key + "/" + video_key + "out.mp4"

		if (x == 0) and (y == 0) and (z == 0):
			call("qt-faststart /var/www/html/portfolio/videos/" + video_key + "/" + video_key + ".mp4 /var/www/html/portfolio/videos/" + video_key + "/" + video_key + "out.mp4", shell=True)
			c.execute("UPDATE REPO_Media SET mimetype = %s, filename = %s WHERE id = %s", [mimetype, filename, (str(id))])
			print "Done"
		else:
			print "Waiting"

def main():
	cursor = db.cursor()
	qry = "SELECT * FROM REPO_Media WHERE mimetype = unconverted OR mimetype = converting"
	cursor.execute(qry)

	rows = cursor.fetchall()
	for row in rows:
		filename = row['filename']
		id = row['media_id']
		if mimetype == "unconverted":
			convert(id, filename)
		elif mimetype == "converting":
			checkprogress(id, filename)
	cursor.close()

def readfile(a):
	log = open (a, "r")
	listlines = log.readlines()
	log.close()
	listlines = listlines[-1]
	letter = listlines.split()[0][0]

	if letter == "f":
		return 1
	else:
		return 0

while True:
	main()
	sleep(60)

