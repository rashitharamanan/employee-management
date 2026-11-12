<?php
// frontend/pages/employee/employee-documents.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mindazzle — Documents &amp; Payroll</title>
    <meta name="description" content="Access your important documents and payroll information. View contracts, IDs, tax forms, salary slips, and manage bank account details.">

    <!-- GOOGLE FONTS (Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- STYLES -->
    <link rel="stylesheet" href="../../assets/css/employee-sidebar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/employee-documents.css?v=<?= time() ?>">
</head>

<body>

<!-- LOADING OVERLAY -->
<div class="emp-loading-overlay" id="empLoadingOverlay">
    <div class="emp-spinner"></div>
</div>

<!-- TOAST NOTIFICATIONS CONTAINER -->
<div class="emp-toast-container" id="empToastContainer"></div>

<!-- APP LAYOUT -->
<div class="emp-app-layout">

    <!-- SIDEBAR -->
    <?php include '../../includes/employee-sidebar.php'; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="emp-main-wrapper">

        <!-- =====================================================
             TOPBAR
        ====================================================== -->
        <?php include '../../includes/employee-topbar.php'; ?>

        <!-- =====================================================
             MAIN CONTENT
        ====================================================== -->
        <main class="emp-content-container">

            <!-- -------------------------------------------------
                 ROW 1: 4 TOP METRIC CARDS
            -------------------------------------------------- -->
            <section class="doc-kpi-grid">
                <!-- KPI 1: Total Documents -->
                <div class="doc-kpi-card" onclick="document.getElementById('empSearchInput').focus()">
                    <div class="doc-kpi-inner">
                        <div class="doc-kpi-icon-circle kpi-circle-blue">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <line x1="10" y1="9" x2="8" y2="9"></line>
                            </svg>
                        </div>
                        <div class="doc-kpi-text-block">
                            <span class="doc-kpi-label">Total Documents</span>
                            <div class="doc-kpi-value" id="kpiTotalDocsVal">12</div>
                            <span class="doc-kpi-subtext" id="kpiTotalDocsSub">Employment, ID proof, certificates</span>
                        </div>
                    </div>
                    <span class="doc-kpi-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </div>

                <!-- KPI 2: Latest Salary -->
                <div class="doc-kpi-card" onclick="openPayslipModal('2025-08')">
                    <div class="doc-kpi-inner">
                        <div class="doc-kpi-icon-circle kpi-circle-green">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                                <circle cx="16" cy="12" r="2"></circle>
                                <path d="M6 10h.01M6 14h.01"></path>
                            </svg>
                        </div>
                        <div class="doc-kpi-text-block">
                            <span class="doc-kpi-label">Latest Salary</span>
                            <div class="doc-kpi-value" id="kpiLatestSalaryVal">₹58,000</div>
                            <span class="doc-kpi-subtext" id="kpiLatestSalarySub">for August 2025</span>
                        </div>
                    </div>
                    <span class="doc-kpi-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </div>

                <!-- KPI 3: Next Payday -->
                <div class="doc-kpi-card">
                    <div class="doc-kpi-inner">
                        <div class="doc-kpi-icon-circle kpi-circle-red">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"></path>
                            </svg>
                        </div>
                        <div class="doc-kpi-text-block">
                            <span class="doc-kpi-label">Next Payday</span>
                            <div class="doc-kpi-value" id="kpiNextPaydayVal">30 Sep 2025</div>
                            <span class="doc-kpi-subtext" id="kpiNextPaydaySub">12 days to go</span>
                        </div>
                    </div>
                    <span class="doc-kpi-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </div>

                <!-- KPI 4: Payslips -->
                <div class="doc-kpi-card" onclick="document.getElementById('payrollOverviewCard').scrollIntoView({behavior:'smooth'})">
                    <div class="doc-kpi-inner">
                        <div class="doc-kpi-icon-circle kpi-circle-purple">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </div>
                        <div class="doc-kpi-text-block">
                            <span class="doc-kpi-label">Payslips</span>
                            <div class="doc-kpi-value" id="kpiPayslipsVal">12</div>
                            <span class="doc-kpi-subtext" id="kpiPayslipsSub">Available for download</span>
                        </div>
                    </div>
                    <span class="doc-kpi-chevron">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </div>
            </section>

            <!-- -------------------------------------------------
                 ROW 2: TWO COLUMNS (My Documents & Payroll Overview)
            -------------------------------------------------- -->
            <section class="doc-main-grid">
                
                <!-- LEFT COLUMN: MY DOCUMENTS TABLE -->
                <div class="doc-card doc-table-card">
                    <div class="doc-card-header">
                        <div class="doc-card-title-group">
                            <div class="doc-card-header-icon purple-text">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <h2 class="doc-card-title">My Documents</h2>
                        </div>

                        <div class="doc-card-actions">
                            <a href="javascript:void(0)" class="doc-view-all-link" onclick="toggleViewAllDocs()">View All</a>
                        </div>
                    </div>

                    <div class="doc-table-wrapper">
                        <table class="doc-table">
                            <thead>
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Uploaded On</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="documentsTableBody">
                                <!-- Populated dynamically by employee-documents.js -->
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:30px;color:#94a3b8;">
                                        Loading documents...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- RIGHT COLUMN: PAYROLL OVERVIEW -->
                <div class="doc-card doc-payroll-card" id="payrollOverviewCard">
                    <div class="doc-card-header">
                        <div class="doc-card-title-group">
                            <div class="doc-card-header-icon purple-bg-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                    <line x1="2" y1="10" x2="22" y2="10"></line>
                                </svg>
                            </div>
                            <h2 class="doc-card-title">Payroll Overview</h2>
                        </div>

                        <!-- Month Selector Dropdown -->
                        <div class="payroll-month-select-wrap">
                            <select id="payrollMonthSelect" onchange="onMonthChange(this.value)">
                                <option value="2025-08" selected>August 2025</option>
                                <option value="2025-07">July 2025</option>
                                <option value="2025-06">June 2025</option>
                                <option value="2025-05">May 2025</option>
                            </select>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="select-chevron">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Net Salary Section -->
                    <div class="payroll-salary-section">
                        <div class="salary-header-row">
                            <div class="salary-left-block">
                                <div class="salary-label">Net Salary</div>
                                <div class="salary-amount-row">
                                    <span class="salary-amount" id="payrollNetSalary">₹58,000</span>
                                    <div class="salary-trend-wrapper">
                                        <div class="salary-trend-badge" id="payrollTrendPill">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="18 15 12 9 6 15"></polyline>
                                            </svg>
                                            <span>5%</span>
                                        </div>
                                        <span class="salary-trend-sub">Compared to last month</span>
                                    </div>
                                </div>
                            </div>

                            <button class="doc-btn-solid-purple" onclick="openPayslipModal()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>View Payslip</span>
                            </button>
                        </div>

                        <!-- 3 Stat Metrics with color dots -->
                        <div class="salary-metrics-row">
                            <div class="salary-metric-item">
                                <div class="metric-val" id="payrollGrossVal">₹75,000</div>
                                <div class="metric-lbl">
                                    <span class="dot dot-green"></span>
                                    <span>Gross Salary</span>
                                </div>
                            </div>
                            <div class="salary-metric-item">
                                <div class="metric-val" id="payrollDeductionsVal">₹12,000</div>
                                <div class="metric-lbl">
                                    <span class="dot dot-red"></span>
                                    <span>Deductions</span>
                                </div>
                            </div>
                            <div class="salary-metric-item">
                                <div class="metric-val" id="payrollNetSmallVal">₹58,000</div>
                                <div class="metric-lbl">
                                    <span class="dot dot-purple"></span>
                                    <span>Net Salary</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payslips Subsection -->
                    <div class="recent-payslips-section">
                        <div class="recent-payslips-header">
                            <h3 class="recent-payslips-title">Recent Payslips</h3>
                            <a href="javascript:void(0)" class="doc-view-all-link" onclick="openAllPayslipsModal()">View All</a>
                        </div>

                        <div class="recent-payslips-list" id="recentPayslipsTableBody">
                            <!-- Populated dynamically by employee-documents.js -->
                        </div>
                    </div>
                </div>

            </section>

            <!-- -------------------------------------------------
                 ROW 3: TWO COLUMNS (Quick Actions & Compliance Banner)
            -------------------------------------------------- -->
            <section class="doc-bottom-grid">
                
                <!-- LEFT: QUICK ACTIONS -->
                <div class="doc-card quick-actions-card">
                    <div class="doc-card-header" style="margin-bottom: 16px;">
                        <div class="doc-card-title-group">
                            <div class="qa-header-lightning">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                </svg>
                            </div>
                            <h2 class="doc-card-title">Quick Actions</h2>
                        </div>
                    </div>

                    <div class="quick-actions-row">
                        <!-- Action 1: Upload Document -->
                        <div class="quick-action-tile" onclick="openUploadModal()">
                            <div class="qa-tile-icon qa-circle-blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div class="qa-tile-title">Upload Document</div>
                            <div class="qa-tile-sub">Add new document</div>
                        </div>

                        <!-- Action 2: Download Payslip -->
                        <div class="quick-action-tile" onclick="downloadCurrentPayslip()">
                            <div class="qa-tile-icon qa-circle-green">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </div>
                            <div class="qa-tile-title">Download Payslip</div>
                            <div class="qa-tile-sub">Get latest payslip</div>
                        </div>

                        <!-- Action 3: Request Document -->
                        <div class="quick-action-tile" onclick="openRequestModal()">
                            <div class="qa-tile-icon qa-circle-blue">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <circle cx="12" cy="14" r="2"></circle>
                                    <line x1="12" y1="8" x2="12" y2="10"></line>
                                </svg>
                            </div>
                            <div class="qa-tile-title">Request Document</div>
                            <div class="qa-tile-sub">Raise a request</div>
                        </div>

                        <!-- Action 4: Update Bank Details -->
                        <div class="quick-action-tile" onclick="openBankModal()">
                            <div class="qa-tile-icon qa-circle-purple">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 21h18"></path>
                                    <path d="M3 10h18"></path>
                                    <path d="M5 6l7-3 7 3"></path>
                                    <path d="M4 10v11"></path>
                                    <path d="M20 10v11"></path>
                                    <path d="M8 14v4"></path>
                                    <path d="M12 14v4"></path>
                                    <path d="M16 14v4"></path>
                                </svg>
                            </div>
                            <div class="qa-tile-title">Update Bank Details</div>
                            <div class="qa-tile-sub" id="qaBankDesc">Manage your account</div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: COMPLIANCE BANNER -->
                <div class="doc-card doc-compliance-banner">
                    <div class="compliance-left">
                        <h3 class="compliance-title">Keep your documents up to date</h3>
                        <p class="compliance-desc">
                            Ensure your personal and financial information is always current.
                        </p>
                    </div>
                    <div class="compliance-right">
                        <div class="compliance-art-wrapper">
                            <!-- Paper with verified shield / check -->
                            <div class="art-paper">
                                <div class="art-paper-line" style="width:70%;"></div>
                                <div class="art-paper-line" style="width:85%;"></div>
                                <div class="art-paper-line" style="width:60%;"></div>
                                <div class="art-check-circle">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <!-- Cartoon character avatar with purple top -->
                            <div class="art-person">
                                <div class="person-head"></div>
                                <div class="person-body"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </main>
    </div>
</div>

<!-- =========================================================
     MODAL 1: VIEW DOCUMENT PREVIEW MODAL
========================================================== -->
<div class="doc-modal-backdrop" id="viewDocumentModal">
    <div class="doc-modal-content">
        <div class="doc-modal-header">
            <div class="doc-modal-title-wrap">
                <div class="emp-doc-icon-badge offer" id="viewModalIconWrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 9h-2v5H9v-7h4v2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="doc-modal-title" id="viewModalTitle">Offer Letter</h3>
                    <p class="doc-modal-subtitle" id="viewModalSubtitle">Employment • Uploaded on 12 Jan 2024</p>
                </div>
            </div>
            <button class="doc-modal-close" onclick="closeModal('viewDocumentModal')" aria-label="Close modal">&times;</button>
        </div>

        <div class="doc-modal-body">
            <div class="doc-preview-container">
                <div class="doc-preview-watermark">MINDAZZLE VERIFIED</div>
                <div class="doc-preview-card" id="docPreviewCardContent">
                    <!-- Dynamic preview content -->
                </div>
            </div>

            <div class="doc-preview-meta" id="docPreviewMeta">
                <!-- Dynamic meta details -->
            </div>
        </div>

        <div class="doc-modal-footer">
            <button class="doc-btn doc-btn-outline" onclick="closeModal('viewDocumentModal')">Close</button>
            <button class="doc-btn doc-btn-primary" id="btnDownloadPreviewDoc">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Download Document</span>
            </button>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL 2: FULL PAYSLIP BREAKDOWN & PRINT MODAL
========================================================== -->
<div class="doc-modal-backdrop" id="payslipModal">
    <div class="doc-modal-content doc-modal-lg">
        <div class="doc-modal-header">
            <div class="doc-modal-title-wrap">
                <div class="emp-doc-icon-badge pan" style="background:#ede9fe; color:#6366f1;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="doc-modal-title" id="payslipModalTitle">Salary Slip — August 2025</h3>
                    <p class="doc-modal-subtitle">Mindazzle Technologies Pvt. Ltd. • Employee ID: EMP1024</p>
                </div>
            </div>
            <button class="doc-modal-close" onclick="closeModal('payslipModal')" aria-label="Close modal">&times;</button>
        </div>

        <div class="doc-modal-body" id="payslipPrintableArea">
            <div id="payslipSlipContent">
                <!-- Dynamically generated printable payslip -->
            </div>
        </div>

        <div class="doc-modal-footer">
            <button class="doc-btn doc-btn-outline" onclick="closeModal('payslipModal')">Close</button>
            <button class="doc-btn doc-btn-primary" id="btnPrintPayslip" onclick="printPayslip()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print / Save PDF</span>
            </button>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL 3: UPLOAD DOCUMENT MODAL
========================================================== -->
<div class="doc-modal-backdrop" id="uploadDocumentModal">
    <div class="doc-modal-content">
        <div class="doc-modal-header">
            <div class="doc-modal-title-wrap">
                <div class="emp-doc-icon-badge id" style="background:#eff6ff; color:#3b82f6;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="doc-modal-title">Upload New Document</h3>
                    <p class="doc-modal-subtitle">Supported files: PDF, JPG, PNG (Max 10MB)</p>
                </div>
            </div>
            <button class="doc-modal-close" onclick="closeModal('uploadDocumentModal')" aria-label="Close modal">&times;</button>
        </div>

        <form id="uploadDocumentForm" onsubmit="handleUploadSubmit(event)">
            <div class="doc-modal-body">
                <div class="doc-form-group">
                    <label class="doc-form-label" for="docCustomName">Document Title *</label>
                    <input type="text" id="docCustomName" class="doc-form-input" placeholder="e.g., Degree Certificate or Passport Copy" required />
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="docTypeSelect">Document Category *</label>
                    <select id="docTypeSelect" class="doc-form-input" required>
                        <option value="Identity">Identity (Aadhar, Passport, Voter ID)</option>
                        <option value="Tax Document">Tax Document (PAN Card, Form 16, ITR)</option>
                        <option value="Education">Education (Degree, Marksheet, Diploma)</option>
                        <option value="Experience">Experience (Relieving Letter, Service Certificate)</option>
                        <option value="Employment">Employment (Offer Letter, Agreement)</option>
                        <option value="Other">Other Official Document</option>
                    </select>
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label">File Selection *</label>
                    <div class="doc-dropzone" id="docDropzone" onclick="document.getElementById('docFileInput').click()">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <div class="doc-dropzone-title">Click to upload or drag &amp; drop</div>
                        <div class="doc-dropzone-sub" id="dropzoneFileSelected">PDF, PNG, JPG up to 10MB</div>
                        <input type="file" id="docFileInput" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="handleFileSelected(this)" />
                    </div>
                </div>
            </div>

            <div class="doc-modal-footer">
                <button type="button" class="doc-btn doc-btn-outline" onclick="closeModal('uploadDocumentModal')">Cancel</button>
                <button type="submit" class="doc-btn doc-btn-primary" id="btnSubmitUpload">
                    <span>Upload &amp; Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================
     MODAL 4: REQUEST HR DOCUMENT MODAL
========================================================== -->
<div class="doc-modal-backdrop" id="requestDocumentModal">
    <div class="doc-modal-content">
        <div class="doc-modal-header">
            <div class="doc-modal-title-wrap">
                <div class="emp-doc-icon-badge pan" style="background:#ede9fe; color:#6366f1;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <circle cx="12" cy="14" r="2"></circle>
                        <line x1="12" y1="8" x2="12" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="doc-modal-title">Request HR Document</h3>
                    <p class="doc-modal-subtitle">Submit a request to Human Resources</p>
                </div>
            </div>
            <button class="doc-modal-close" onclick="closeModal('requestDocumentModal')" aria-label="Close modal">&times;</button>
        </div>

        <form id="requestDocForm" onsubmit="handleRequestSubmit(event)">
            <div class="doc-modal-body">
                <div class="doc-form-group">
                    <label class="doc-form-label" for="reqDocType">Document Required *</label>
                    <select id="reqDocType" class="doc-form-input" required>
                        <option value="Bonafide Employment Certificate">Bonafide Employment Certificate</option>
                        <option value="Salary Certificate for Bank Loan">Salary Certificate for Bank Loan / Visa</option>
                        <option value="Experience Certificate (Interim)">Experience Certificate (Interim)</option>
                        <option value="Form 16 / Tax Statement">Form 16 / Tax Statement Copy</option>
                        <option value="Address Proof Letter">Company Address Proof Letter</option>
                        <option value="Other">Other Custom HR Letter</option>
                    </select>
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="reqDocUrgency">Urgency *</label>
                    <select id="reqDocUrgency" class="doc-form-input" required>
                        <option value="NORMAL">Standard (Within 2-3 business days)</option>
                        <option value="URGENT">Urgent (Within 24 hours)</option>
                    </select>
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="reqDocReason">Purpose / Remarks *</label>
                    <textarea id="reqDocReason" class="doc-form-input" rows="3" placeholder="e.g., Applying for Home Loan / Schengen Visa application" required></textarea>
                </div>
            </div>

            <div class="doc-modal-footer">
                <button type="button" class="doc-btn doc-btn-outline" onclick="closeModal('requestDocumentModal')">Cancel</button>
                <button type="submit" class="doc-btn doc-btn-primary" id="btnSubmitRequest">
                    <span>Submit Request</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================
     MODAL 5: UPDATE BANK DETAILS MODAL
========================================================== -->
<div class="doc-modal-backdrop" id="updateBankModal">
    <div class="doc-modal-content">
        <div class="doc-modal-header">
            <div class="doc-modal-title-wrap">
                <div class="emp-doc-icon-badge pan" style="background:#ede9fe; color:#6366f1;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"></path>
                        <path d="M3 10h18"></path>
                        <path d="M5 6l7-3 7 3"></path>
                        <path d="M4 10v11"></path>
                        <path d="M20 10v11"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="doc-modal-title">Salary Bank Account Details</h3>
                    <p class="doc-modal-subtitle">Direct deposit credentials for monthly payroll</p>
                </div>
            </div>
            <button class="doc-modal-close" onclick="closeModal('updateBankModal')" aria-label="Close modal">&times;</button>
        </div>

        <form id="bankDetailsForm" onsubmit="handleBankSubmit(event)">
            <div class="doc-modal-body">
                <div class="doc-alert-info">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Changes to salary account details require HR verification and take 24-48 hours.</span>
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="bankHolder">Account Holder Name *</label>
                    <input type="text" id="bankHolder" class="doc-form-input" placeholder="e.g., Priya Nair" required />
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="bankName">Bank Name *</label>
                    <input type="text" id="bankName" class="doc-form-input" placeholder="e.g., HDFC Bank" required />
                </div>

                <div class="doc-form-group">
                    <label class="doc-form-label" for="bankAcc">Account Number *</label>
                    <input type="text" id="bankAcc" class="doc-form-input" placeholder="e.g., 5010048291823" required />
                </div>

                <div class="doc-form-row">
                    <div class="doc-form-group" style="flex:1;">
                        <label class="doc-form-label" for="bankIfsc">IFSC Code *</label>
                        <input type="text" id="bankIfsc" class="doc-form-input" placeholder="e.g., HDFC0001245" required />
                    </div>
                    <div class="doc-form-group" style="flex:1;">
                        <label class="doc-form-label" for="bankBranch">Branch *</label>
                        <input type="text" id="bankBranch" class="doc-form-input" placeholder="e.g., Indiranagar, Bengaluru" required />
                    </div>
                </div>
            </div>

            <div class="doc-modal-footer">
                <button type="button" class="doc-btn doc-btn-outline" onclick="closeModal('updateBankModal')">Cancel</button>
                <button type="submit" class="doc-btn doc-btn-primary" id="btnSaveBank">
                    <span>Save &amp; Update</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script src="../../assets/js/theme.js?v=<?= time() ?>"></script>
<script src="../../assets/js/employee-documents.js?v=<?= time() ?>"></script>
</body>
</html>
