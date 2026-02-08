<p align="center">
  <img src="https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge&logo=git&logoColor=white" alt="Status">
  <img src="https://img.shields.io/github/license/Mohsin-Developer/LocateTRGT?style=for-the-badge" alt="License">
  <img src="https://img.shields.io/badge/Powered%20by-PHP%20%7C%20JavaScript-blueviolet?style=for-the-badge" alt="Tech">
</p>

<h1 align="center">LocateTRGT</h1>

<p align="center">
  <strong>Silent real-time location & photo acquisition tool</strong><br>
  Designed for authorized penetration testing, red-team exercises, security research, and ethical demonstrations.
</p>

<p align="center">
  <img src="https://via.placeholder.com/1000x500/0a0a0a/00f2fe?text=LocateTRGT+-+Intelligence+Dashboard" alt="Dashboard Preview" width="900">
</p>

## ⚠️ Important Legal & Ethical Warning

**This tool is for EDUCATIONAL AND AUTHORIZED USE ONLY.**

- Use only with **explicit written permission** from the device owner/target organization.
- Unauthorized tracking, photo capture, or location collection is **illegal** in most jurisdictions (GDPR, Dutch AVG/Wbp, Dutch Criminal Code Art. 139a–139e, wiretapping laws, etc.).
- Misuse may result in **criminal prosecution** and/or civil liability.

**The author assumes no responsibility** for any illegal or unethical application of this code.

## Core Features

- Stealth rear-camera snapshot capture
- High-accuracy GPS coordinates + accuracy radius
- Device fingerprinting (model, platform, screen resolution, user-agent)
- Silent data exfiltration via POST to C2 server
- Dark sci-fi admin dashboard with:
  - Live Google Maps view of multiple targets
  - Timestamped photo gallery with metadata (time, device, IP)
  - Raw activity log + visit tracking
- Command polling support (future remote trigger capability)

## Tech Stack

| Layer          | Technology                          |
|----------------|-------------------------------------|
| Backend        | PHP 7.4+ / 8.x                      |
| Frontend       | HTML5 + CSS (glassmorphism + neon)  |
| Client-side    | Vanilla JavaScript + async/await    |
| Storage        | Filesystem (`captures/` + `location.txt`) |
| Maps           | Google Maps Embed API               |
| Fonts          | Orbitron + Rajdhani                 |

## Project Structure
LocateTRGT/
├── show.php           ← Receiver + admin dashboard
├── login.php          ← Admin authentication
├── captures/          ← Saved photos + metadata .txt files
├── location.txt       ← GPS logs + visit history
└── client.html        ← Silent capture page (rename as needed)
text## Quick Setup (for testing / authorized use only)

1. Upload files to a PHP-capable web server
2. Create empty `location.txt` → `chmod 666 location.txt`
3. Make `captures/` writable → `chmod 777 captures/`
4. Access `login.php` → default password: `odmin1` (CHANGE THIS!)
5. Open `client.html` on target device (disguise/link it as needed)
6. Watch data appear in dashboard

**HTTPS strongly recommended** — many browsers block camera + geolocation on HTTP.

## Security & Best Practices

- **Change admin password** immediately
- Use strong authentication (consider adding 2FA or IP whitelisting)
- Never deploy on public internet without protection
- Rotate logs / delete old captures regularly
- Test in isolated lab environment first

## License

MIT License — but **only for lawful, authorized, and ethical use**.

Any illegal application voids any implied permission.

## Author & Contact

**Mohsin**  
Rotterdam, Netherlands  
GitHub: [Mohsin-Developer](https://github.com/MohsinHabibmsk)

For legitimate security research inquiries only.

---

<p align="center">
  <i>Built for learning. Use responsibly.</i><br>
  2026
</p>
