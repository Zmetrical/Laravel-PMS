# CLDG Office Payroll System

A web-based payroll and HR management system built with Laravel and Bootstrap 5.
Supports multiple roles — Employee, HR, Accounting, and Admin — each with their own set of modules and access.

![Login](screenshots/login.png)

---

## Table of Contents

- [About](#about)
- [Screenshots](#screenshots)
- [Modules](#modules)
- [Tech Stack](#tech-stack)
- [Setup](#setup)
- [Roles](#roles)

---

## About

This system was built to handle the day-to-day payroll and HR operations of a multi-branch office. It covers everything from clocking in and filing leaves to processing payroll periods and generating reports — all in one place with role-based access.

---

## Screenshots

> Screenshots are stored in the `/screenshots` folder.

### General

| Page | Preview |
|------|---------|
| Login | ![Login](screenshots/login.png) |

---

### Employee

| Page | Preview |
|------|---------|
| Schedule | ![Schedule](screenshots/schedule.png) |
| Timekeeping | ![Timekeeping](screenshots/timekeeping.png) |
| Leave Request | ![Leave Request](screenshots/reuquest-leave.png) |
| Payslip | ![Payslip](screenshots/payslip.png) |

---

### Human Resources

| Page | Preview |
|------|---------|
| HR Schedule | ![HR Schedule](screenshots/hr-schedule.png) |
| HR Reports | ![HR Reports](screenshots/hr-reports.png) |

---

### Accounting

| Page | Preview |
|------|---------|
| Payroll Process | ![Payroll Process](screenshots/payroll-process.png) |
| Payroll Summary | ![Payroll Summary](screenshots/payroll-summary.png) |
| Payroll Report | ![Payroll Report](screenshots/payroll-report.png) |
| Salary | ![Salary](screenshots/salary.png) |

---

### Admin

| Page | Preview |
|------|---------|
| Departments | ![Departments](screenshots/departments.png) |
| Branches | ![Branches](screenshots/branches.png) |

---

## Modules

**Employee**
- Schedule, Timekeeping, Leave Request, Payslip

**Human Resources**
- HR Schedule, HR Reports

**Accounting**
- Payroll Process, Payroll Summary, Payroll Report, Salary

**Admin**
- Departments, Branches

---

## Tech Stack

- **Backend** — Laravel (PHP)
- **Frontend** — Bootstrap 5, Bootstrap Icons
- **Database** — MySQL
- **Auth** — Laravel Auth with role-based access control

---

## Setup

```bash
git clone https://github.com/your-username/cldg-payroll-system.git
cd cldg-payroll-system

composer install
npm install && npm run dev

cp .env.example .env
php artisan key:generate
```

Update your `.env` with your database credentials, then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

---

## Roles

| Role | What they can access |
|------|----------------------|
| `employee` | Their own profile, schedule, timekeeping, leaves, overtime, payroll, loans |
| `hr` | All employee records, attendance, schedules, request approvals, loans, reports |
| `accounting` | Payroll periods and salary processing |
| `admin` | Everything above + departments, positions, branches, and system settings |

---

> Built with Laravel & Bootstrap 5
