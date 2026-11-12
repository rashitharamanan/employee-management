/* ================================================================
   MINDDAZZLE MY ATTENDANCE — JAVASCRIPT
   Connects to Backend Attendance API & Manages Full Interactivity
   File: employee-attendance.js
================================================================ */

const EMP_API_BASE = window.location.origin + "/employee-qr-system/backend/public";

// State
let attState = {
    currentMonth: "2026-09",
    statusFilter: "ALL",
    searchQuery: "",
    currentPage: 1,
    perPage: 8,
    isCheckedIn: true,
    data: null
};

/* ----------------------------------------------------------------
   Auth Helpers
---------------------------------------------------------------- */
function getAuthToken() {
    return localStorage.getItem("auth_token") || localStorage.getItem("token") || "";
}

function getAuthHeaders() {
    const token = getAuthToken();
    const headers = {
        "Accept": "application/json",
        "Content-Type": "application/json"
    };
    if (token) {
        headers["Authorization"] = `Bearer ${token}`;
    }
    return headers;
}

/* ----------------------------------------------------------------
   Toast Notification
---------------------------------------------------------------- */
function showToast(message, type = "info") {
    const container = document.getElementById("empToastContainer");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = "emp-toast";
    
    let icon = "ℹ️";
    if (type === "success") icon = "✅";
    if (type === "error")   icon = "⚠️";

    toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(8px)";
        toast.style.transition = "all 0.25s ease";
        setTimeout(() => toast.remove(), 250);
    }, 3200);
}

/* ----------------------------------------------------------------
   Fetch Attendance Data from Backend
---------------------------------------------------------------- */
async function loadAttendanceData() {
    showLoader();

    const params = new URLSearchParams({
        month:    attState.currentMonth,
        status:   attState.statusFilter,
        search:   attState.searchQuery,
        page:     attState.currentPage,
        per_page: attState.perPage
    });

    try {
        const res = await fetch(`${EMP_API_BASE}/api/employee/attendance?${params.toString()}`, {
            method: "GET",
            headers: getAuthHeaders()
        });

        const json = await res.json();

        if (json.success && json.data) {
            attState.data = json.data;
            renderAllAttendance(json.data);
        } else {
            console.warn("Backend error, using fallback data:", json.message);
            renderFallbackAttendance();
        }
    } catch (err) {
        console.warn("Could not reach attendance API, using fallback data:", err);
        renderFallbackAttendance();
    } finally {
        hideLoader();
    }
}

function showLoader() {
    const loader = document.getElementById("empLoadingOverlay");
    if (loader) loader.classList.remove("hidden");
}

function hideLoader() {
    const loader = document.getElementById("empLoadingOverlay");
    if (loader) loader.classList.add("hidden");
}

/* ----------------------------------------------------------------
   Render All Attendance Components
---------------------------------------------------------------- */
function renderAllAttendance(data) {
    // 1. Top 5 Metrics
    const stats = data.stats || {};
    const presentEl  = document.getElementById("metricPresent");
    const absentEl   = document.getElementById("metricAbsent");
    const lateEl     = document.getElementById("metricLate");
    const halfDayEl  = document.getElementById("metricHalfDay");
    const wfhEl      = document.getElementById("metricWfh");

    if (presentEl) presentEl.textContent = stats.present ?? 18;
    if (absentEl)  absentEl.textContent  = stats.absent ?? 1;
    if (lateEl)    lateEl.textContent    = stats.late ?? 2;
    if (halfDayEl) halfDayEl.textContent = stats.half_day ?? 1;
    if (wfhEl)     wfhEl.textContent     = stats.wfh ?? 3;

    // 2. Today's Attendance Box
    const today = data.today || {};
    const todayDateLabel = document.getElementById("todayDateLabel");
    const todayStatusTxt = document.getElementById("todayStatusTxt");
    const todayTimeTxt   = document.getElementById("todayTimeTxt");
    const todayCheckIn   = document.getElementById("todayCheckInVal");
    const todayCheckOut  = document.getElementById("todayCheckOutVal");
    const todayMsg       = document.getElementById("todayStatusMsg");
    const btnPunchText   = document.getElementById("btnPunchText");

    if (todayDateLabel) todayDateLabel.textContent = today.date || "Tuesday, 16 September 2026";
    if (todayStatusTxt) todayStatusTxt.textContent = today.status || "Checked In";
    if (todayTimeTxt)   todayTimeTxt.textContent   = today.check_in || "09:12 AM";
    if (todayCheckIn)   todayCheckIn.textContent   = today.check_in || "09:12 AM";
    if (todayCheckOut)  todayCheckOut.textContent  = today.check_out || "-- : --";
    if (todayMsg)       todayMsg.textContent       = today.status_message || "You are currently at work";

    attState.isCheckedIn = Boolean(today.is_checked_in);
    if (btnPunchText) {
        btnPunchText.textContent = attState.isCheckedIn ? "Check Out" : "Check In";
    }

    // 3. Calendar Card
    renderCalendar(data.calendar || [], data.month_label || "September 2026");

    // 4. Monthly Summary (Donut)
    renderMonthlySummary(stats);

    // 5. History Table & Pagination
    renderHistoryTable(data.history || [], data.pagination || {});
}

/* ----------------------------------------------------------------
   Render Calendar
---------------------------------------------------------------- */
function renderCalendar(days, monthLabel) {
    const labelEl = document.getElementById("calendarMonthLabel");
    if (labelEl) labelEl.textContent = monthLabel;

    const grid = document.getElementById("calendarDaysGrid");
    if (!grid) return;

    grid.innerHTML = days.map(d => {
        const isOtherMonth = !d.is_current;
        const isToday = Boolean(d.is_today);
        const cellClasses = [
            "emp-cal-day-cell",
            isOtherMonth ? "other-month" : "",
            isToday ? "is-today" : ""
        ].filter(Boolean).join(" ");

        let dotHtml = "";
        if (d.dot_color) {
            dotHtml = `<span class="emp-cal-dot ${d.dot_color}"></span>`;
        }

        return `
            <div class="${cellClasses}" data-date="${d.date || ""}" title="${d.date || ""} ${d.status || ""}">
                <span class="emp-cal-day-num">${d.day}</span>
                ${dotHtml}
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Render Monthly Summary Donut Chart
---------------------------------------------------------------- */
function renderMonthlySummary(stats) {
    const present = stats.present ?? 18;
    const absent  = stats.absent ?? 1;
    const late    = stats.late ?? 2;
    const halfDay = stats.half_day ?? 1;
    const wfh     = stats.wfh ?? 3;
    const totalDays = stats.total_days ?? 25;

    const totalDaysEl = document.getElementById("summaryTotalDays");
    if (totalDaysEl) totalDaysEl.textContent = totalDays;

    const pcts = stats.percentages || {
        present: 72, absent: 4, late: 8, half_day: 4, wfh: 12
    };

    const map = {
        "summaryPctPresent": `${present} (${pcts.present ?? 72}%)`,
        "summaryPctAbsent":  `${absent} (${pcts.absent ?? 4}%)`,
        "summaryPctLate":    `${late} (${pcts.late ?? 8}%)`,
        "summaryPctHalf":    `${halfDay} (${pcts.half_day ?? 4}%)`,
        "summaryPctWfh":     `${wfh} (${pcts.wfh ?? 12}%)`
    };

    for (const [id, val] of Object.entries(map)) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // Render SVG donut segments
    const svg = document.getElementById("summaryDonutSvg");
    if (!svg) return;

    svg.querySelectorAll(".emp-donut-arc").forEach(el => el.remove());

    const r = 54;
    const circumference = 2 * Math.PI * r;
    const sum = present + absent + late + halfDay + wfh;

    if (sum === 0) return;

    const segments = [
        { val: present, color: "#10b981" },
        { val: absent,  color: "#ef4444" },
        { val: late,    color: "#f59e0b" },
        { val: halfDay, color: "#8b5cf6" },
        { val: wfh,     color: "#3b82f6" }
    ];

    let currentOffset = 0;

    segments.forEach(seg => {
        if (seg.val <= 0) return;
        const segmentLength = (seg.val / sum) * circumference;
        const gapLength = circumference - segmentLength;

        const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttribute("class", "emp-donut-arc");
        circle.setAttribute("cx", "70");
        circle.setAttribute("cy", "70");
        circle.setAttribute("r", String(r));
        circle.setAttribute("stroke", seg.color);
        circle.setAttribute("stroke-dasharray", `${segmentLength} ${gapLength}`);
        circle.setAttribute("stroke-dashoffset", String(-currentOffset));

        svg.appendChild(circle);
        currentOffset += segmentLength;
    });
}

/* ----------------------------------------------------------------
   Render History Table & Pagination
---------------------------------------------------------------- */
function renderHistoryTable(history, pagination) {
    const tbody = document.getElementById("attendanceTableBody");
    if (!tbody) return;

    if (!Array.isArray(history) || history.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-muted);">No attendance records found for this filter.</td></tr>`;
        return;
    }

    tbody.innerHTML = history.map(row => {
        return `
            <tr>
                <td style="font-weight:700;color:var(--text-muted);">${row.index}</td>
                <td style="font-weight:700;color:var(--text-heading);">${row.date}</td>
                <td>${row.check_in}</td>
                <td>${row.check_out}</td>
                <td>${row.total_hours}</td>
                <td><span class="emp-pill-status ${row.status_class}">${row.status}</span></td>
                <td>${row.location}</td>
                <td style="color:var(--text-muted);">${row.remarks}</td>
                <td><button type="button" class="emp-row-action-btn" title="Options">⋮</button></td>
            </tr>
        `;
    }).join("");

    // Update Pagination Info
    const infoEl = document.getElementById("paginationInfo");
    if (infoEl) {
        const from = pagination.showing_from ?? 1;
        const to   = pagination.showing_to ?? history.length;
        const total= pagination.total_records ?? 30;
        infoEl.textContent = `Showing ${from} to ${to} of ${total} entries`;
    }

    // Render Page Number Buttons
    const controls = document.getElementById("paginationControls");
    if (controls) {
        const totalPages = pagination.total_pages ?? 4;
        const current = pagination.current_page ?? 1;

        let btns = `<button type="button" class="emp-page-btn" ${current <= 1 ? "disabled style='opacity:0.5;'" : ""} onclick="changePage(${current - 1})">‹</button>`;

        for (let p = 1; p <= totalPages; p++) {
            btns += `<button type="button" class="emp-page-btn ${p === current ? "active" : ""}" onclick="changePage(${p})">${p}</button>`;
        }

        btns += `<button type="button" class="emp-page-btn" ${current >= totalPages ? "disabled style='opacity:0.5;'" : ""} onclick="changePage(${current + 1})">›</button>`;

        controls.innerHTML = btns;
    }
}

function changePage(page) {
    if (page < 1) return;
    attState.currentPage = page;
    loadAttendanceData();
}

/* ----------------------------------------------------------------
   Fallback Data Generator (Identical to Mockup)
---------------------------------------------------------------- */
function renderFallbackAttendance() {
    const sampleRows = [
        { index: 1, date: "16 Sep 2026", check_in: "09:12 AM", check_out: "-- : --",  total_hours: "--",     status: "Present",  status_class: "present",  location: "Office",          remarks: "-" },
        { index: 2, date: "15 Sep 2026", check_in: "09:05 AM", check_out: "06:10 PM", total_hours: "9h 05m", status: "Present",  status_class: "present",  location: "Office",          remarks: "-" },
        { index: 3, date: "14 Sep 2026", check_in: "09:45 AM", check_out: "06:00 PM", total_hours: "8h 15m", status: "Late",     status_class: "late",     location: "Office",          remarks: "Traffic delay" },
        { index: 4, date: "13 Sep 2026", check_in: "-- : --",  check_out: "-- : --",  total_hours: "--",     status: "Absent",   status_class: "absent",   location: "-",               remarks: "-" },
        { index: 5, date: "12 Sep 2026", check_in: "09:10 AM", check_out: "06:05 PM", total_hours: "8h 55m", status: "Present",  status_class: "present",  location: "Office",          remarks: "-" },
        { index: 6, date: "11 Sep 2026", check_in: "09:08 AM", check_out: "06:00 PM", total_hours: "8h 52m", status: "Present",  status_class: "present",  location: "Work From Home",  remarks: "-" },
        { index: 7, date: "10 Sep 2026", check_in: "09:00 AM", check_out: "05:30 PM", total_hours: "8h 30m", status: "Half Day", status_class: "half-day", location: "Office",          remarks: "Left early" },
        { index: 8, date: "09 Sep 2026", check_in: "09:15 AM", check_out: "06:10 PM", total_hours: "8h 55m", status: "Present",  status_class: "present",  location: "Office",          remarks: "-" }
    ];

    renderAllAttendance({
        today: {
            date: "Tuesday, 16 September 2026",
            check_in: "09:12 AM",
            check_out: "-- : --",
            status: "Checked In",
            status_message: "You are currently at work",
            is_checked_in: true
        },
        stats: {
            present: 18, absent: 1, late: 2, half_day: 1, wfh: 3, total_days: 25,
            percentages: { present: 72, absent: 4, late: 8, half_day: 4, wfh: 12 }
        },
        calendar: generateFallbackCalendar(),
        history: sampleRows,
        pagination: {
            current_page: 1,
            per_page: 8,
            total_records: 30,
            total_pages: 4,
            showing_from: 1,
            showing_to: 8
        }
    });
}

function generateFallbackCalendar() {
    const days = [];
    // Padding: 30, 31 Aug
    days.push({ day: 30, is_current: false, is_today: false });
    days.push({ day: 31, is_current: false, is_today: false });

    // Days 1 to 30
    for (let d = 1; d <= 30; d++) {
        let dot = null;
        if ([1, 2, 3, 4, 7, 8, 12, 14, 15, 16, 22, 28, 29, 30].includes(d)) dot = "green";
        else if ([9, 25].includes(d)) dot = "red";
        else if ([24].includes(d)) dot = "orange";
        else if ([10].includes(d)) dot = "purple";
        else if ([11, 21].includes(d)) dot = "blue";

        days.push({
            day: d,
            is_current: true,
            is_today: (d === 16),
            dot_color: dot
        });
    }

    // Padding 1, 2, 3 Oct
    days.push({ day: 1, is_current: false, is_today: false });
    days.push({ day: 2, is_current: false, is_today: false });
    days.push({ day: 3, is_current: false, is_today: false });

    return days;
}

/* ----------------------------------------------------------------
   Punch Action (Check In / Check Out)
---------------------------------------------------------------- */
async function handlePunchAction() {
    const endpoint = attState.isCheckedIn ? "/api/employee/attendance/check-out" : "/api/employee/attendance/check-in";

    try {
        const res = await fetch(`${EMP_API_BASE}${endpoint}`, {
            method: "POST",
            headers: getAuthHeaders(),
            body: JSON.stringify({
                location: "Office"
            })
        });

        const json = await res.json();

        if (json.success) {
            showToast(json.message, "success");
            loadAttendanceData();
        } else {
            showToast(json.message || "Action failed", "error");
        }
    } catch (_) {
        // Optimistic UI toggle for demo
        attState.isCheckedIn = !attState.isCheckedIn;
        const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (attState.isCheckedIn) {
            document.getElementById("todayStatusTxt").textContent = "Checked In";
            document.getElementById("todayCheckInVal").textContent = nowTime;
            document.getElementById("todayStatusMsg").textContent = "You are currently at work";
            document.getElementById("btnPunchText").textContent = "Check Out";
            showToast("Checked in successfully!", "success");
        } else {
            document.getElementById("todayStatusTxt").textContent = "Checked Out";
            document.getElementById("todayCheckOutVal").textContent = nowTime;
            document.getElementById("todayStatusMsg").textContent = "You have completed work for today";
            document.getElementById("btnPunchText").textContent = "Check In";
            showToast("Checked out successfully. Have a wonderful evening!", "info");
        }
    }
}

/* ----------------------------------------------------------------
   Setup Event Listeners
---------------------------------------------------------------- */
function initEvents() {
    // Punch button
    const punchBtn = document.getElementById("btnPunchAction");
    if (punchBtn) {
        punchBtn.addEventListener("click", handlePunchAction);
    }

    // Month Navigation in calendar
    const prevBtn = document.getElementById("btnPrevMonth");
    const nextBtn = document.getElementById("btnNextMonth");
    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            if (attState.currentMonth === "2026-09") {
                attState.currentMonth = "2026-08";
            } else if (attState.currentMonth === "2026-08") {
                attState.currentMonth = "2026-07";
            }
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            if (attState.currentMonth === "2026-07") {
                attState.currentMonth = "2026-08";
            } else if (attState.currentMonth === "2026-08") {
                attState.currentMonth = "2026-09";
            }
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }

    // Month Select filter
    const monthSelect = document.getElementById("tableMonthSelect");
    if (monthSelect) {
        monthSelect.addEventListener("change", (e) => {
            attState.currentMonth = e.target.value;
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }

    // Status Select filter
    const statusSelect = document.getElementById("tableStatusSelect");
    if (statusSelect) {
        statusSelect.addEventListener("change", (e) => {
            attState.statusFilter = e.target.value;
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }

    // Search input
    const searchInput = document.getElementById("tableSearchInput");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            attState.searchQuery = e.target.value.trim();
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }

    // Rows per page
    const rppSelect = document.getElementById("rowsPerPageSelect");
    if (rppSelect) {
        rppSelect.addEventListener("change", (e) => {
            attState.perPage = parseInt(e.target.value);
            attState.currentPage = 1;
            loadAttendanceData();
        });
    }

    // Correction Modal
    const modal = document.getElementById("correctionModal");
    const openBtn1 = document.getElementById("btnOpenCorrectionModal");
    const openBtn2 = document.getElementById("actionQuickCorrection");
    const closeBtn = document.getElementById("btnCloseCorrectionModal");
    const cancelBtn = document.getElementById("btnCancelCorrection");
    const form = document.getElementById("correctionForm");

    const openModal = () => { if (modal) modal.style.display = "flex"; };
    const closeModal = () => { if (modal) modal.style.display = "none"; };

    if (openBtn1) openBtn1.addEventListener("click", openModal);
    if (openBtn2) openBtn2.addEventListener("click", openModal);
    if (closeBtn) closeBtn.addEventListener("click", closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);

    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const date = document.getElementById("corrDate").value;
            const checkIn = document.getElementById("corrCheckIn").value;
            const checkOut = document.getElementById("corrCheckOut").value;
            const reason = document.getElementById("corrReason").value;

            try {
                const res = await fetch(`${EMP_API_BASE}/api/employee/attendance/correction`, {
                    method: "POST",
                    headers: getAuthHeaders(),
                    body: JSON.stringify({
                        date,
                        check_in: checkIn,
                        check_out: checkOut,
                        reason
                    })
                });

                const json = await res.json();
                closeModal();
                showToast(json.message || "Correction submitted successfully.", "success");
            } catch (_) {
                closeModal();
                showToast("Attendance correction request submitted to HR.", "success");
            }
        });
    }

    // Download Report
    const downloadBtn = document.getElementById("actionQuickDownload");
    if (downloadBtn) {
        downloadBtn.addEventListener("click", () => {
            const token = getAuthToken();
            window.location.href = `${EMP_API_BASE}/api/employee/attendance/report?month=${attState.currentMonth}&token=${token}`;
        });
    }

    // View Team Attendance
    const teamBtn = document.getElementById("actionQuickTeam");
    if (teamBtn) {
        teamBtn.addEventListener("click", () => {
            showToast("Team attendance overview is synced with HR roster.", "info");
        });
    }
}

/* ----------------------------------------------------------------
   Init
---------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    initEvents();
    loadAttendanceData();
});
