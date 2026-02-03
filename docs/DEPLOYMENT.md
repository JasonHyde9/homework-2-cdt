# Deployment Guide: Vulnerable Redis & Web Stack

## 1. Prerequisites
Before deploying this infrastructure, ensure the following requirements are met:

### Controller Node (Your Machine)
* **OS:** Kali Linux or Ubuntu 20.04+
* **Software:** Ansible Core 2.16+ (`ansible --version`)
* **Network:** Must have SSH access to the target IP address.

### Target Node (The Victim)
* **OS:** Ubuntu 24.04 LTS (Noble Numbat)
* **User:** A user with `sudo` privileges (passwordless sudo recommended).
* **Firewall:** Ensure the following ports are allowed:
    * **22 (SSH):** For Ansible management.
    * **80 (HTTP):** For the web application.
    * **6379 (Redis):** For the vulnerable database service.

## 2. Configuration
The deployment is customizable via variables defined in `vars/main.yml`.

| Variable | Default Value | Description |
| `web_root` | `/var/www/html` | The directory where Apache serves files. |
| `redis_port` | `6379` | The port Redis listens on. |
| `redis_bind_interface` | `0.0.0.0` | **Security Risk:** The interface Redis binds to. |

**To customize:** Edit `vars/main.yml` before running the playbook.

## 3. Installation & Deployment
Follow these steps to deploy the vulnerable stack.

### Step 1: Clone the Repository
Clone the project files to your Ansible control node:
```bash
git clone https://github.com/JasonHyde9/homework-2-cdt
cd ansible-vuln-deployment
```

### Step 2: Configure Inventory
Edit the `inventory.ini` file to include your target IP address and SSH user credentials.

### Step 3: Run the Playbook
Execute the deployment using the standard `ansible-playbook` command:
```bash
ansible-playbook -i inventory.ini playbook.yml
```

**Expected Output:**
* Tasks executing with `changed` or `ok` status.
* The final **PLAY RECAP** must show `failed=0`.

## 4. Verification

### Service Status
Verify that both Apache2 and Redis are active and running:
```bash
sudo systemctl status apache2 redis-server
```

### Web Interface
Ensure the PHP application is serving content:
```bash
curl -I http://<TARGET_IP>
```

### Network Port Verification
Perform an `nmap` scan to verify the services:
```bash
nmap -p 80,6379 <TARGET_IP>
```

## 5. Troubleshooting

| Issue | Cause | Solution |
| **Permission denied** | Incorrect SSH credentials. | Verify `ansible_user` and `ansible_ssh_pass` in `inventory.ini`. |
| **Connection Timeout** | Firewall/Security Group blocking traffic. | Ensure ports 22, 80, and 6379 are open on the target. |
| **SSH Failure** | Target unreachable. | Manually test connection: `ssh user@IP`. |