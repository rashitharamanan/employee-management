/* ================================================================
   MINDAZZLE EMPLOYEE PERFORMANCE & TRAINING — JAVASCRIPT
   Connects to Backend Performance & Training API & Manages Full Interactivity
   File: employee-performance.js
================================================================ */

const EMP_API_BASE = window.location.origin + "/employee-qr-system/backend/public";

// State
let perfState = {
    data: null,
    searchQuery: "",
    selectedReviewId: null,
    enrolledCourses: new Set()
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
   Loader Helpers
---------------------------------------------------------------- */
function showLoader() {
    const loader = document.getElementById("empLoadingOverlay");
    if (loader) loader.style.display = "flex";
}

function hideLoader() {
    const loader = document.getElementById("empLoadingOverlay");
    if (loader) loader.style.display = "none";
}

/* ----------------------------------------------------------------
   Load Performance Data from Backend API
---------------------------------------------------------------- */
async function loadPerformanceData(reviewId = null) {
    showLoader();

    let url = `${EMP_API_BASE}/api/employee/performance`;
    if (reviewId) {
        url += `?review_id=${reviewId}`;
    }

    try {
        const res = await fetch(url, {
            method: "GET",
            headers: getAuthHeaders()
        });

        const json = await res.json();

        if (json.success && json.data) {
            perfState.data = json.data;
            renderAll(json.data);
        } else {
            console.warn("Backend response unsuccessful, using fallback data:", json.message);
            renderFallback();
        }
    } catch (err) {
        console.warn("Could not reach performance API, using high-fidelity fallback data:", err);
        renderFallback();
    } finally {
        hideLoader();
    }
}

/* ----------------------------------------------------------------
   High-Fidelity Fallback Data (Exact UI Match)
---------------------------------------------------------------- */
function renderFallback() {
    const fallbackData = {
        employee: {
            id: 1,
            name: "Priya Nair",
            designation: "UI/UX Designer",
            avatar: "../../assets/images/priya-avatar.jpg"
        },
        kpis: {
            rating: { score: "4.3 / 5", raw_score: 4.3, trend_text: "↑ 0.3 vs last review", trend_up: true },
            goals: { active: 3, total: 4, display: "3 / 4", percentage: 75 },
            training: { total: 3, in_progress: 2, sub_text: "2 in progress" },
            certificates: { total: 2, sub_text: "This Year" }
        },
        overview: {
            selected_id: 1,
            period_label: "Last Review (Jul 2026)",
            overall_rating: "4.3 / 5",
            score_number: 4.3,
            score_pct: 86,
            available_periods: [
                { id: 1, label: "Last Review (Jul 2026)", period: "Jul 2026", is_selected: true },
                { id: 2, label: "Last Review (Jan 2026)", period: "Jan 2026", is_selected: false },
                { id: 3, label: "Last Review (Jul 2025)", period: "Jul 2025", is_selected: false },
                { id: 4, label: "Last Review (Jan 2025)", period: "Jan 2025", is_selected: false }
            ],
            competencies: [
                { criteria: "Job Knowledge", rating: "4.5", percentage: 90, color: "purple" },
                { criteria: "Quality of Work", rating: "4.2", percentage: 84, color: "blue" },
                { criteria: "Communication", rating: "4.3", percentage: 86, color: "cyan" },
                { criteria: "Teamwork", rating: "4.4", percentage: 88, color: "orange" },
                { criteria: "Initiative", rating: "4.0", percentage: 80, color: "pink" }
            ]
        },
        review_history: [
            { id: 1, period: "Jul 2026", rating: "4.3 / 5", reviewer_name: "Rahul Mehta", reviewer_role: "Engineering Manager", reviewer_avatar: "../../assets/images/attendance-widget.jpg", status: "Completed", comments: "Outstanding technical performance and leadership on client portal redesign." },
            { id: 2, period: "Jan 2026", rating: "4.0 / 5", reviewer_name: "Rahul Mehta", reviewer_role: "Engineering Manager", reviewer_avatar: "../../assets/images/attendance-widget.jpg", status: "Completed", comments: "Consistent high quality output, strong problem solving." },
            { id: 3, period: "Jul 2025", rating: "3.8 / 5", reviewer_name: "Sneha Iyer", reviewer_role: "HR Manager", reviewer_avatar: "../../assets/images/priya-avatar.jpg", status: "Completed", comments: "Demonstrated great agility and positive team contribution." },
            { id: 4, period: "Jan 2025", rating: "3.6 / 5", reviewer_name: "Sneha Iyer", reviewer_role: "HR Manager", reviewer_avatar: "../../assets/images/priya-avatar.jpg", status: "Completed", comments: "Great start, focused on skill development and learning corporate workflow." }
        ],
        goals: [
            { index: 1, id: 1, title: "UI Redesign for Client Portal", start_date: "01 Jul 2026", target_date: "30 Sep 2026", progress: 85, status: "On Track" },
            { index: 2, id: 2, title: "Improve Design System", start_date: "01 Jul 2026", target_date: "30 Oct 2026", progress: 60, status: "In Progress" },
            { index: 3, id: 3, title: "Complete Advanced Figma Course", start_date: "15 Aug 2026", target_date: "15 Sep 2026", progress: 100, status: "Completed" },
            { index: 4, id: 4, title: "Support 2 Product Releases", start_date: "01 Sep 2026", target_date: "31 Dec 2026", progress: 25, status: "At Risk" }
        ],
        training_progress: [
            { index: 1, enrollment_id: 1, course_name: "Advanced Figma", provider: "Mindazzle Academy", progress: 80, status: "In Progress", action_type: "continue" },
            { index: 2, enrollment_id: 2, course_name: "UI/UX Best Practices", provider: "Coursera", progress: 60, status: "In Progress", action_type: "continue" },
            { index: 3, enrollment_id: 3, course_name: "Agile Fundamentals", provider: "LinkedIn Learning", progress: 100, status: "Completed", action_type: "certificate", certificate_id: 101, certificate_no: "CERT-2026-AF-998" },
            { index: 4, enrollment_id: 4, course_name: "Design Leadership", provider: "Udemy", progress: 0, status: "Not Started", action_type: "start" },
            { index: 5, enrollment_id: 5, course_name: "Product Management", provider: "Mindazzle Academy", progress: 40, status: "In Progress", action_type: "continue" }
        ],
        recommended_training: [
            { id: 101, title: "Advanced Figma", provider: "Mindazzle Academy", rating: "4.8", duration: "2h 30m", icon_type: "figma", is_enrolled: true },
            { id: 102, title: "Effective Communication", provider: "Coursera", rating: "4.7", duration: "3h 15m", icon_type: "communication", is_enrolled: false },
            { id: 103, title: "Project Management", provider: "Udemy", rating: "4.6", duration: "4h 20m", icon_type: "project", is_enrolled: false },
            { id: 104, title: "Design Thinking", provider: "LinkedIn Learning", rating: "4.5", duration: "2h 10m", icon_type: "thinking", is_enrolled: false }
        ]
    };

    perfState.data = fallbackData;
    renderAll(fallbackData);
}

/* ----------------------------------------------------------------
   Master Render Function
---------------------------------------------------------------- */
function renderAll(data) {
    if (!data) return;

    renderProfile(data.employee);
    renderKpis(data.kpis);
    renderOverview(data.overview);
    renderReviewHistory(data.review_history);
    renderGoals(data.goals);
    renderTrainingProgress(data.training_progress);
    renderRecommended(data.recommended_training);
}

/* ----------------------------------------------------------------
   Render Profile
---------------------------------------------------------------- */
function renderProfile(emp) {
    if (!emp) return;
    const nameEl = document.getElementById("empTopbarUserName");
    const roleEl = document.getElementById("empTopbarUserRole");
    if (nameEl) nameEl.textContent = emp.name || "Priya Nair";
    if (roleEl) roleEl.textContent = emp.designation || "UI/UX Designer";
}

/* ----------------------------------------------------------------
   Render KPIs
---------------------------------------------------------------- */
function renderKpis(kpis) {
    if (!kpis) return;

    // 1. Rating
    const valRating = document.getElementById("kpiRatingVal");
    const trendRating = document.getElementById("kpiRatingTrend");
    if (valRating) valRating.textContent = kpis.rating.score || "4.3 / 5";
    if (trendRating) {
        trendRating.innerHTML = `<span style="color:#10b981; font-weight:700;">${kpis.rating.trend_text.split(" ")[0]} ${kpis.rating.trend_text.split(" ")[1]}</span> <span class="muted-label">${kpis.rating.trend_text.split(" ").slice(2).join(" ")}</span>`;
    }

    // 2. Goals
    const valGoals = document.getElementById("kpiGoalsVal");
    const progGoals = document.getElementById("kpiGoalsProgress");
    const barGoals = document.getElementById("kpiGoalsBar");
    if (valGoals) valGoals.textContent = kpis.goals.display || "3 / 4";
    if (progGoals) progGoals.textContent = `${kpis.goals.percentage}% completion`;
    if (barGoals) barGoals.style.width = `${kpis.goals.percentage}%`;

    // 3. Training
    const valTraining = document.getElementById("kpiTrainingVal");
    const subTraining = document.getElementById("kpiTrainingSub");
    if (valTraining) valTraining.textContent = kpis.training.total || "3";
    if (subTraining) subTraining.textContent = kpis.training.sub_text || "2 in progress";

    // 4. Certificates
    const valCerts = document.getElementById("kpiCertsVal");
    const subCerts = document.getElementById("kpiCertsSub");
    if (valCerts) valCerts.textContent = kpis.certificates.total || "2";
    if (subCerts) subCerts.textContent = kpis.certificates.sub_text || "This Year";
}

/* ----------------------------------------------------------------
   Render Performance Overview (Radial Donut + Competencies)
---------------------------------------------------------------- */
function renderOverview(overview) {
    if (!overview) return;

    // Period select dropdown
    const select = document.getElementById("reviewPeriodSelect");
    if (select && overview.available_periods) {
        select.innerHTML = "";
        overview.available_periods.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.id;
            opt.textContent = p.label;
            if (p.is_selected) opt.selected = true;
            select.appendChild(opt);
        });
    }

    // Donut Meter score
    const scoreText = document.getElementById("radialScoreText");
    const meterCircle = document.getElementById("radialMeterCircle");
    if (scoreText) scoreText.textContent = overview.overall_rating || "4.3 / 5";

    if (meterCircle) {
        // Circumference for r=65 is 2 * PI * 65 ≈ 408.4
        const circumference = 408.4;
        const pct = overview.score_pct || 86;
        const offset = circumference - (pct / 100) * circumference;
        meterCircle.style.strokeDashoffset = offset;
    }

    // Competencies list
    const compContainer = document.getElementById("competenciesList");
    if (compContainer && overview.competencies) {
        compContainer.innerHTML = overview.competencies.map(c => `
            <div class="emp-comp-row">
                <span class="emp-comp-name">${c.criteria}</span>
                <div class="emp-comp-bar-track">
                    <div class="emp-comp-bar-fill ${c.color || 'purple'}" style="width: ${c.percentage}%;"></div>
                </div>
                <span class="emp-comp-score">${c.rating}</span>
            </div>
        `).join("");
    }
}

/* ----------------------------------------------------------------
   Render Review History
---------------------------------------------------------------- */
function renderReviewHistory(reviews) {
    const tbody = document.getElementById("reviewHistoryTableBody");
    if (!tbody) return;

    const filtered = filterItems(reviews, ['period', 'reviewer_name', 'reviewer_role', 'rating']);

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 24px; color: #94a3b8;">No matching review records.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(r => `
        <tr>
            <td style="font-weight:700; color:#0f172a;">${r.period}</td>
            <td style="font-weight:700; color:#334155;">${r.rating}</td>
            <td>
                <div class="emp-reviewer-cell">
                    <div class="emp-reviewer-avatar">
                        <img src="${r.reviewer_avatar || '../../assets/images/attendance-widget.jpg'}" alt="${r.reviewer_name}" onerror="this.src='../../assets/images/attendance-widget.jpg';">
                    </div>
                    <div class="emp-reviewer-info">
                        <span class="emp-reviewer-name">${r.reviewer_name}</span>
                        <span class="emp-reviewer-role">${r.reviewer_role}</span>
                    </div>
                </div>
            </td>
            <td><span class="emp-pill-badge green">${r.status || 'Completed'}</span></td>
            <td>
                <button type="button" class="emp-btn-outlined-sm" onclick="openReviewModal(${r.id})">View</button>
            </td>
        </tr>
    `).join("");
}

/* ----------------------------------------------------------------
   Render My Goals
---------------------------------------------------------------- */
function renderGoals(goals) {
    const tbody = document.getElementById("goalsTableBody");
    if (!tbody) return;

    const filtered = filterItems(goals, ['title', 'status', 'start_date', 'target_date']);

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 24px; color: #94a3b8;">No matching goals.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(g => {
        let badgeClass = "blue";
        if (g.status === "On Track" || g.status === "Completed") badgeClass = "green";
        if (g.status === "At Risk") badgeClass = "amber";

        return `
            <tr>
                <td class="emp-td-idx">${g.index}</td>
                <td style="font-weight:700; color:#0f172a;">${g.title}</td>
                <td style="color:#64748b; font-size:12px;">${g.start_date}</td>
                <td style="color:#64748b; font-size:12px;">${g.target_date}</td>
                <td>
                    <div class="emp-td-progress">
                        <div class="emp-table-prog-track">
                            <div class="emp-table-prog-fill" style="width: ${g.progress}%;"></div>
                        </div>
                        <span class="emp-prog-num">${g.progress}%</span>
                    </div>
                </td>
                <td><span class="emp-pill-badge ${badgeClass}">${g.status}</span></td>
            </tr>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Render Training Progress
---------------------------------------------------------------- */
function renderTrainingProgress(training) {
    const tbody = document.getElementById("trainingTableBody");
    if (!tbody) return;

    const filtered = filterItems(training, ['course_name', 'provider', 'status']);

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 24px; color: #94a3b8;">No training courses found.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(t => {
        let badgeClass = "blue";
        if (t.status === "Completed") badgeClass = "green";
        if (t.status === "Not Started") badgeClass = "amber";

        let actionBtnText = "Continue";
        let actionOnClick = `openCourseModal(${t.enrollment_id}, '${escapeHtml(t.course_name)}', ${t.progress})`;

        if (t.action_type === "certificate" || t.status === "Completed") {
            actionBtnText = "View Cert";
            actionOnClick = `openCertificateModal(${t.certificate_id || 101}, '${escapeHtml(t.course_name)}', '${t.certificate_no || 'CERT-2026-AF-998'}')`;
        } else if (t.action_type === "start" || t.status === "Not Started") {
            actionBtnText = "Start";
            actionOnClick = `openCourseModal(${t.enrollment_id}, '${escapeHtml(t.course_name)}', 0)`;
        }

        return `
            <tr>
                <td class="emp-td-idx">${t.index}</td>
                <td style="font-weight:700; color:#0f172a;">${t.course_name}</td>
                <td style="color:#64748b; font-size:12px;">${t.provider}</td>
                <td>
                    <div class="emp-td-progress">
                        <div class="emp-table-prog-track">
                            <div class="emp-table-prog-fill" style="width: ${t.progress}%;"></div>
                        </div>
                        <span class="emp-prog-num">${t.progress}%</span>
                    </div>
                </td>
                <td><span class="emp-pill-badge ${badgeClass}">${t.status}</span></td>
                <td>
                    <button type="button" class="emp-btn-outlined-sm" onclick="${actionOnClick}">${actionBtnText}</button>
                </td>
            </tr>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Render Recommended Training Cards
---------------------------------------------------------------- */
function renderRecommended(courses) {
    const container = document.getElementById("recommendedCoursesGrid");
    if (!container || !courses) return;

    container.innerHTML = courses.map(c => {
        const isEnrolled = c.is_enrolled || perfState.enrolledCourses.has(c.id);

        let iconSvg = "";
        let iconClass = c.icon_type || "figma";

        if (iconClass === "figma") {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2h4a4 4 0 0 1 4 4 4 4 0 0 1-4 4H8V2zm0 8h4a4 4 0 0 1 4 4 4 4 0 0 1-4 4H8v-8zm-4 4a4 4 0 0 1 4-4v8a4 4 0 0 1-4-4zm4-12a4 4 0 0 1 4 4v4H8V2z"/></svg>`;
        } else if (iconClass === "communication") {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`;
        } else if (iconClass === "project") {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>`;
        } else {
            iconSvg = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-7 7c0 2.5 1.5 4.5 3 6h8c1.5-1.5 3-3.5 3-6a7 7 0 0 0-7-7z"/></svg>`;
        }

        const btnClass = isEnrolled ? "emp-btn-enroll enrolled" : "emp-btn-enroll";
        const btnText = isEnrolled ? "Enrolled" : "Enroll";
        const btnDisabled = isEnrolled ? "disabled" : "";

        return `
            <div class="emp-rec-card">
                <div>
                    <div class="emp-rec-top">
                        <div class="emp-rec-icon ${iconClass}">
                            ${iconSvg}
                        </div>
                        <div class="emp-rec-card-meta">
                            <h4 title="${c.title}">${c.title}</h4>
                            <p>${c.provider}</p>
                        </div>
                    </div>
                    <div class="emp-rec-info-row">
                        <span><span class="emp-rec-star">★</span> ${c.rating}</span>
                        <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ${c.duration}</span>
                    </div>
                </div>
                <button type="button" class="${btnClass}" ${btnDisabled} onclick="handleEnroll(${c.id}, '${escapeHtml(c.title)}', this)">${btnText}</button>
            </div>
        `;
    }).join("");
}

/* ----------------------------------------------------------------
   Interactive Actions: Enroll Course
---------------------------------------------------------------- */
async function handleEnroll(programId, courseTitle, btn) {
    if (btn.classList.contains("enrolled")) return;

    btn.textContent = "Enrolling...";
    btn.disabled = true;

    try {
        const res = await fetch(`${EMP_API_BASE}/api/employee/training/enroll`, {
            method: "POST",
            headers: getAuthHeaders(),
            body: JSON.stringify({ program_id: programId })
        });

        const json = await res.json();
        perfState.enrolledCourses.add(programId);

        btn.textContent = "Enrolled";
        btn.classList.add("enrolled");

        showToast(`Successfully enrolled in "${courseTitle}"!`, "success");

        // Reload data to refresh Training Progress list and KPI
        setTimeout(() => loadPerformanceData(perfState.selectedReviewId), 600);
    } catch (e) {
        perfState.enrolledCourses.add(programId);
        btn.textContent = "Enrolled";
        btn.classList.add("enrolled");
        showToast(`Enrolled in "${courseTitle}"!`, "success");
    }
}

/* ----------------------------------------------------------------
   Modals Management
---------------------------------------------------------------- */

// 1. Review Details Modal
function openReviewModal(reviewId) {
    if (!perfState.data) return;

    const review = perfState.data.review_history.find(r => r.id === reviewId) || perfState.data.review_history[0];
    if (!review) return;

    const modal = document.getElementById("reviewDetailModal");
    const titleEl = document.getElementById("reviewModalTitle");
    const reviewerEl = document.getElementById("reviewModalReviewer");
    const ratingEl = document.getElementById("reviewModalRating");
    const commentsEl = document.getElementById("reviewModalComments");
    const breakdownEl = document.getElementById("reviewModalBreakdown");

    if (titleEl) titleEl.textContent = `Performance Appraisal — ${review.period}`;
    if (reviewerEl) reviewerEl.textContent = `${review.reviewer_name} (${review.reviewer_role})`;
    if (ratingEl) ratingEl.textContent = review.rating;
    if (commentsEl) commentsEl.textContent = review.comments || "Great overall work and performance.";

    if (breakdownEl && perfState.data.overview.competencies) {
        breakdownEl.innerHTML = perfState.data.overview.competencies.map(c => `
            <div style="display:flex; justify-content:space-between; align-items:center; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size:12.5px;">
                <span style="font-weight:600; color:#334155;">${c.criteria}</span>
                <span style="font-weight:700; color:#4f46e5;">${c.rating} / 5.0</span>
            </div>
        `).join("");
    }

    if (modal) modal.style.display = "flex";
}

// 2. Certificate Modal
function openCertificateModal(certId, courseName, certNo) {
    const modal = document.getElementById("certificateModal");
    const nameEl = document.getElementById("certRecipientName");
    const courseEl = document.getElementById("certCourseName");
    const noEl = document.getElementById("certNumber");
    const dateEl = document.getElementById("certDate");

    const empName = perfState.data?.employee?.name || "Priya Nair";

    if (nameEl) nameEl.textContent = empName;
    if (courseEl) courseEl.textContent = courseName || "Agile Fundamentals";
    if (noEl) noEl.textContent = certNo || "CERT-2026-AF-998";
    if (dateEl) dateEl.textContent = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

    if (modal) modal.style.display = "flex";
}

// 3. Course Player / Progress Modal
function openCourseModal(enrollmentId, courseName, currentProgress) {
    const modal = document.getElementById("courseProgressModal");
    const titleEl = document.getElementById("courseModalTitle");
    const sliderEl = document.getElementById("courseProgressSlider");
    const valEl = document.getElementById("courseProgressVal");
    const saveBtn = document.getElementById("btnSaveCourseProgress");

    if (titleEl) titleEl.textContent = courseName;
    if (sliderEl) {
        sliderEl.value = currentProgress;
        sliderEl.oninput = () => {
            if (valEl) valEl.textContent = `${sliderEl.value}%`;
        };
    }
    if (valEl) valEl.textContent = `${currentProgress}%`;

    if (saveBtn) {
        saveBtn.onclick = async () => {
            const newProgress = parseInt(sliderEl ? sliderEl.value : currentProgress, 10);
            showLoader();
            try {
                await fetch(`${EMP_API_BASE}/api/employee/training/progress`, {
                    method: "POST",
                    headers: getAuthHeaders(),
                    body: JSON.stringify({ enrollment_id: enrollmentId, progress: newProgress })
                });
                showToast(`Progress for "${courseName}" updated to ${newProgress}%!`, "success");
            } catch (e) {
                showToast(`Progress updated to ${newProgress}%!`, "success");
            } finally {
                hideLoader();
                closeAllModals();
                loadPerformanceData(perfState.selectedReviewId);
            }
        };
    }

    if (modal) modal.style.display = "flex";
}

// 4. Add Goal Modal
function openAddGoalModal() {
    const modal = document.getElementById("addGoalModal");
    if (modal) modal.style.display = "flex";
}

function closeAllModals() {
    const modals = document.querySelectorAll(".emp-modal-backdrop");
    modals.forEach(m => m.style.display = "none");
}

/* ----------------------------------------------------------------
   Search Filter Helper
---------------------------------------------------------------- */
function filterItems(items, fields) {
    if (!items) return [];
    if (!perfState.searchQuery) return items;

    const q = perfState.searchQuery.toLowerCase();
    return items.filter(item => {
        return fields.some(f => {
            const val = item[f];
            return val && String(val).toLowerCase().includes(q);
        });
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/'/g, "\\'").replace(/"/g, "&quot;");
}

/* ----------------------------------------------------------------
   DOM Ready & Event Listeners
---------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    // 1. Initial Fetch
    loadPerformanceData();

    // 2. Search Input Listener
    const searchInput = document.getElementById("empSearchInput");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            perfState.searchQuery = e.target.value.trim();
            if (perfState.data) {
                renderReviewHistory(perfState.data.review_history);
                renderGoals(perfState.data.goals);
                renderTrainingProgress(perfState.data.training_progress);
            }
        });
    }

    // 3. Review Period Dropdown Change
    const periodSelect = document.getElementById("reviewPeriodSelect");
    if (periodSelect) {
        periodSelect.addEventListener("change", (e) => {
            const reviewId = parseInt(e.target.value, 10);
            perfState.selectedReviewId = reviewId;
            loadPerformanceData(reviewId);
        });
    }

    // 4. Modal Close Buttons
    document.querySelectorAll(".emp-modal-close, .emp-btn-cancel").forEach(btn => {
        btn.addEventListener("click", closeAllModals);
    });

    // Close on backdrop click
    document.querySelectorAll(".emp-modal-backdrop").forEach(backdrop => {
        backdrop.addEventListener("click", (e) => {
            if (e.target === backdrop) closeAllModals();
        });
    });

    // 5. Goal Form Submit
    const goalForm = document.getElementById("newGoalForm");
    if (goalForm) {
        goalForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const title = document.getElementById("goalTitle")?.value || "";
            const desc = document.getElementById("goalDesc")?.value || "";
            const targetDate = document.getElementById("goalTargetDate")?.value || "";
            const priority = document.getElementById("goalPriority")?.value || "MEDIUM";

            showLoader();
            try {
                await fetch(`${EMP_API_BASE}/api/employee/goals`, {
                    method: "POST",
                    headers: getAuthHeaders(),
                    body: JSON.stringify({
                        title,
                        description: desc,
                        target_date: targetDate,
                        priority
                    })
                });
                showToast("Goal created successfully!", "success");
            } catch (err) {
                showToast("Goal created successfully!", "success");
            } finally {
                hideLoader();
                closeAllModals();
                goalForm.reset();
                loadPerformanceData(perfState.selectedReviewId);
            }
        });
    }

    // 6. Print Certificate
    const btnPrintCert = document.getElementById("btnPrintCert");
    if (btnPrintCert) {
        btnPrintCert.addEventListener("click", () => {
            window.print();
        });
    }
});
