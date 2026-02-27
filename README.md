# 🏢 BPDA Personnel Directory

A modern **Workforce Management System** developed for the **Bangsamoro Planning and Development Authority (BPDA)**. This system is designed to streamline the management, filtering, and monitoring of all personnel across various Bureaus and Divisions.



## 🌟 Key Features

* **Real-time AJAX Filtering:** Fast "Search-as-you-type" functionality and filtering for employees by ID, Name, Bureau, and Status without page reloads.
* **Dynamic Cascading Dropdowns:** Intelligent selection logic where *Division* and *Position* options automatically update based on the selected *Bureau*.
* **Smart Salary Logic:** Automatically disables the salary input when the Employment Type is set to "Permanent" (adhering to standard scales) and enables it for other types.
* **Responsive UI:** Built with **Tailwind CSS** and **Alpine.js** for a clean, professional interface that works seamlessly on both desktop and mobile devices.
* **Visual Status Indicators:** Easily identify Active and Inactive personnel using color-coded badges and pulse animations.

## 🛠️ Tech Stack

* **Backend:** [Laravel 10+](https://laravel.com/) (PHP 8.2+)
* **Frontend:** [Tailwind CSS](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/)
* **Interactivity:** Vanilla JavaScript (Fetch API / AJAX)
* **Icons:** [Bootstrap Icons](https://icons.getbootstrap.com/)
* **Database:** MySQL



## 📥 Installation & Setup

Follow these steps to set up the project on your local machine:

### 1. Prerequisites
* PHP 8.2 or higher
* Composer
* Node.js & NPM
* MySQL Server

### 2. Clone the Repository
```bash
git clone [https://github.com/username/bpda-personnel-directory.git](https://github.com/username/bpda-personnel-directory.git)
cd bpda-personnel-directory