<?php
// admin/partials/admin_header.php
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'Admin') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Admin style sederhana, nyambung sama tema oranye kamu -->
  <style>
    :root {
      --orange-main: #F97316;
      --orange-dark: #C2410C;
      --bg-soft: #FFF7ED;
    }
    body {
      font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
      background: #F3F4F6;
    }
    .admin-layout {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 240px 1fr;
      background: linear-gradient(180deg, #FFF5EB 0%, #FFE1C6 40%, #FFFFFF 100%);
    }
    /* SIDEBAR */
    .admin-sidebar {
      background: #FFFFFF;
      border-right: 1px solid #E5E7EB;
      padding: 1rem 1rem 1rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      position: sticky;
      top: 0;
      height: 100vh;
    }
    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-weight: 700;
      color: var(--orange-dark);
    }
    .sidebar-brand .brand-logo {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      background: var(--bg-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    .sidebar-brand small {
      font-weight: 500;
      font-size: 0.7rem;
      color: #6B7280;
    }
    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
      margin-top: 0.5rem;
    }
    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.45rem 0.7rem;
      border-radius: 0.75rem;
      color: #4B5563;
      font-size: 0.9rem;
      text-decoration: none;
      transition: background 0.15s, color 0.15s, transform 0.1s;
    }
    .sidebar-link .icon {
      width: 22px;
      text-align: center;
    }
    .sidebar-link:hover {
      background: #FFF7ED;
      color: var(--orange-dark);
      transform: translateX(1px);
    }
    .sidebar-link.active {
      background: rgba(248, 113, 22, 0.12);
      color: var(--orange-dark);
      font-weight: 600;
    }
    .sidebar-footer {
      margin-top: auto;
      border-top: 1px solid #E5E7EB;
      padding-top: 0.75rem;
    }
    .sidebar-user {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }
    .avatar-circle {
      width: 32px;
      height: 32px;
      border-radius: 999px;
      background: var(--orange-main);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      font-weight: 600;
    }
    .user-name {
      font-size: 0.9rem;
      font-weight: 600;
    }
    .user-role {
      font-size: 0.75rem;
    }

    /* MAIN */
    .admin-main {
      padding: 1.1rem 1.4rem 1.4rem;
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }
    .admin-main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .admin-page-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--orange-dark);
      margin-bottom: 0;
    }
    .admin-main-body {
      background: rgba(255,255,255,0.9);
      border-radius: 1rem;
      padding: 1.1rem 1.3rem;
      box-shadow: 0 16px 45px rgba(15,23,42,0.1);
    }

    .stat-card {
      border-radius: 0.9rem;
      border: 1px solid #E5E7EB;
      padding: 0.8rem 1rem;
      background: #FFFFFF;
    }
    .stat-label {
      font-size: 0.8rem;
      color: #6B7280;
    }
    .stat-value {
      font-size: 1.1rem;
      font-weight: 700;
      color: #111827;
    }
    .stat-sub {
      font-size: 0.75rem;
      color: #9CA3AF;
    }

    @media (max-width: 992px) {
      .admin-layout {
        grid-template-columns: 1fr;
      }
      .admin-sidebar {
        position: static;
        height: auto;
        flex-direction: row;
        align-items: center;
        overflow-x: auto;
        gap: 0.75rem;
      }
      .sidebar-nav {
        flex-direction: row;
      }
      .sidebar-footer {
        margin-top: 0;
        border-top: none;
      }
    }
  
    .btn-main {
        background:#F97316 !important;
        color:#fff !important;
        border:none !important;
        border-radius:999px !important;
        padding:8px 16px !important;
        font-weight:600 !important;
    }

  </style>
</head>
<body>