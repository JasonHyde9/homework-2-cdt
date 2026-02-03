# Vulnerable Redis & Web Stack Deployment

## Overview
This project deploys a deliberately vulnerable infrastructure for the CDT competition. It provisions a LAMP-style stack on Ubuntu 24.04 LTS (Noble Numbat) using Ansible.

The deployment simulates a common real-world misconfiguration where internal NoSQL databases are accidentally exposed to the internet, allowing for unauthorized file writes and full system takeover.

## Vulnerability Description
* **Type:** Unauthenticated Redis Remote Code Execution (RCE)
* **Severity:** **Critical (CVSS 10.0)**

The Redis service is deployed with "Protected Mode" disabled and is bound to `0.0.0.0`. To ensure the exploit works on modern kernels, this project explicitly weakens Systemd sandboxing (using `ReadWritePaths`) and bypasses Redis 7+ security features (via `enable-protected-configs`). This allows an unauthenticated attacker to remotely:

1. Reconfigure the database working directory to the Apache web root.
2. Write a malicious PHP web shell to the disk.
3. Execute arbitrary commands as the `www-data` user.

## Prerequisites
* **Target OS:** Ubuntu 24.04 LTS.
* **Ansible Version:** 2.16+ (Core) utilizing FQCN (Fully Qualified Collection Names).
* **Controller Node:** Kali Linux or Ubuntu with `redis-cli` installed.
* **Network:** Target must have ports **80 (Web)** and **6379 (Redis)** exposed.

## Quick Start

### 1. Clone the repository
```bash
git clone https://github.com/JasonHyde9/homework-2-cdt
cd homework-2-cdt
```

### 2. Configure Inventory
Update `inventory.ini` with your target IP and SSH credentials:

```ini
[targets]
<TARGET-IP> ansible_user=<USER> ansible_ssh_pass=<PASS> ansible_sudo_pass=<PASS>
```

### 3. Run Deployment
```bash
ansible-playbook -i inventory.ini playbook.yml
```

## Documentation
* **[Deployment Guide](./deployment_guide.md):** Detailed instructions on environment setup, Jinja2 template usage, and service verification.
* **[Exploitation Guide](./exploitation_guide.md):** Step-by-step walkthrough for identifying the vulnerability, weaponizing the file write, and upgrading to a Python3 Reverse Shell.

## Technical Details (Advanced Features)
The Ansible playbook demonstrates proficiency in several advanced automation areas:

* **Templates:** Uses Jinja2 to dynamically generate PHP index pages based on host facts.
* **Handlers:** Uses event-driven triggers to restart services only when configuration files are modified.
* **Facts & Conditionals:** Uses `ansible_facts['os_family']` to ensure compatibility and "fail-safe" execution.
* **Sandbox Evasion:** Creates Systemd overrides in `/etc/systemd/system/redis-server.service.d/` to grant Redis write access to the web root—a requirement for exploitation on Ubuntu 24.04.

## Troubleshooting
**Issue: Redis "Protected Config" Error during exploit.**
* **Solution:** Ensure the playbook task "Enable Dangerous Config Changes" ran successfully. Redis 7+ blocks `CONFIG SET dir` by default unless explicitly allowed.

**Issue: Web Shell works, but Reverse Shell fails.**
* **Solution:** Ensure you are using `curl -G` (GET request) instead of POST. PHP's `system($_GET['cmd'])` only reads from the URL parameters.

**Issue: Ansible "Permission Denied" on SSH.**
* **Solution:** Create a dedicated `ansible` user created during setup, as the default cloud user often has password authentication disabled.