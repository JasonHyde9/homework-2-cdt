# Vulnerable Redis & Web Stack Deployment

## Overview
This project deploys a deliberately vulnerable infrastructure for the CDT competition. It provisions a LAMP-style stack (Linux, Apache, Redis, PHP) on Ubuntu 24.04 LTS.

The deployment is designed to demonstrate misconfigurations in NoSQL databases that can lead to full system compromise. It is fully automated using Ansible.

## Vulnerability Description
**Type:** Unauthenticated Redis Remote Code Execution (RCE)
**Severity:** Critical

The Redis service is deployed with "Protected Mode" disabled and is bound to `0.0.0.0`, exposing it to the external network. Additionally, the Systemd sandbox and Redis 7 security features have been explicitly weakened. This allows an unauthenticated attacker to connect remotely, manipulate the database configuration to write a PHP web shell into the Apache web root (`/var/www/html`), and achieve Remote Code Execution (RCE).

## Prerequisites
* **Target OS:** Ubuntu 24.04 LTS (Noble Numbat)
* **Ansible Version:** 2.16+ (Core)
* **Required Python Packages:** standard python3 library on target.
* **Network:** Target must be reachable via SSH and have ports 80 and 6379 exposed in OpenStack/AWS Security Groups.

## Quick Start
1.  **Clone the repository:**
    ```bash
    git clone https://github.com/JasonHyde9/homework-2-cdt
    cd homework2
    ```

2.  **Configure Inventory:**
    Edit `inventory.ini` with your target IP and SSH user:
    ```ini
    [targets]
    100.65.3.183 ansible_user=ansible ansible_password=Student123! ansible_sudo_pass=Student123!
    ```

3.  **Run Deployment:**
    ```bash
    ansible-playbook -i inventory.ini playbook.yml
    ```

## Documentation
* **[Deployment Guide](docs/DEPLOYMENT.md):** Detailed instructions on setting up the environment and verifying services.
* **[Exploitation Guide](docs/EXPLOITATION.md):** Step-by-step walkthrough for identifying and exploiting the Redis vulnerability to gain a Reverse Shell.

## Competition Use Cases
* **Red Team:** Can be used to practice service enumeration, database interaction (Redis CLI), and web shell upload techniques.
* **Blue Team:** Ideal for practicing log analysis (identifying anomalous Redis config commands), configuring AppArmor profiles to restrict file writes, and hardening Systemd service units.
* **Grey Team:** Useful for testing automated vulnerability scanners to see if they detect the exposed Redis service and the "Protected Mode" misconfiguration.

## Technical Details
The Ansible playbook performs the following key actions:
1.  **Installation:** Installs Apache2, PHP, Redis Server, and networking tools.
2.  **Configuration:**
    * Binds Redis to `0.0.0.0` (External access).
    * Disables "Protected Mode" in `redis.conf`.
    * Enables "Dangerous Configs" (bypassing Redis 7+ security).
3.  **Sandbox Evasion:** Creates a Systemd override file to allow the Redis process to write to `/var/www/html` and sets a permissive UMask.
4.  **Service Management:** Ensures all services are enabled and restarted to apply changes.

## Troubleshooting
**Issue: Redis "Protected Config" Error during exploit.**
* **Solution:** Ensure the playbook task "Enable Dangerous Config Changes" ran successfully. Redis 7+ blocks `CONFIG SET dir` by default unless explicitly allowed.

**Issue: Web Shell works, but Reverse Shell fails.**
* **Solution:** Ensure you are using `curl -G` (GET request) instead of POST. PHP's `system($_GET['cmd'])` only reads from the URL parameters.

**Issue: Ansible "Permission Denied" on SSH.**
* **Solution:** Create a dedicated `ansible` user created during setup, as the default cloud user often has password authentication disabled.
