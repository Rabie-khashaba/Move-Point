<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="maryinparis" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Move Point</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/Favicon.webp') }}" />

    <!-- Google Font Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <!-- Vendors -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/jquery-jvectormap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/jquery.time-to.min.css') }}">	
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify-data.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/quill.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tui-calendar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tui-theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tui-time-picker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tui-date-picker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/emojionearea.min.css') }}">	
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/dataTables.bs5.min.css') }}">	

    <!-- Theme & Custom -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body, html {
            font-family: 'Cairo', sans-serif !important;
        }
        
        /* Notification Badge Styles */
        .notification-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        .notification-badge.hidden {
            display: none !important;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Sidebar notification styling */
        .nxl-link {
            position: relative;
        }
        
        .nxl-link .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            z-index: 10;
        }
        
        /* Header notification dropdown styling */
        .nxl-header-notification .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            z-index: 10;
            font-size: 9px;
            min-width: 16px;
            height: 16px;
        }
        
        .nxl-notification-dropdown {
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }
        
        .notification-icon.leave_request {
            background-color: #17a2b8;
        }
        
        .notification-icon.advance_request {
            background-color: #28a745;
        }
        
        .notification-icon.resignation_request {
            background-color: #dc3545;
        }
        
        .notification-icon.delivery_deposit {
            background-color: #ffc107;
            color: #000;
        }
        
        .notification-icon.general {
            background-color: #6c757d;
        }
        
        .notification-text {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
        }
        
        .notification-body {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        
        .notification-time {
            font-size: 11px;
            color: #999;
        }
        
        .notification-actions {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }
        
        .notification-actions .btn {
            font-size: 11px;
            padding: 2px 6px;
        }
    </style>
</head>
