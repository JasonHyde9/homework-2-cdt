The deployment is initiated using the standard ansible-playbook command.
```
ansible-playbook playbook.yml
```

### Verification Steps
Verifying that both the Apache server and Redis database are up and running.

```
sudo systemctl status apache2 redis-server
```
Refer to screenshot service-running.png

Web interface running:
The web server was tested to ensure the PHP application is serving content on Port 80.
```
curl -l http://100.65.3.183
```
Refer to screenshot web-curl.png

Alternatively you can access from a browser:
Refer to screenshot website-proof.png

Network port verification:
An nmap scan is performed on the ports to verify that the application is running.
```
nmap -p 80,6379 100.65.3.183
```
Refer to screenshot nmap-scan.png
